#!/usr/bin/env bash
# =============================================================================
#  n8n Post-Boot Script
#  Generated dynamically by the PlusClouds API — do not edit manually.
#
#  Flow:
#    1. Report  → booting
#    2. Write   shared env file (/etc/n8n-boot.env)
#    3. Install Docker if missing
#    4. Start   n8n container
#    5. Report  → starting
#    6. Install health check script + systemd timer
#    7. Wait    for /healthz
#    8. Report  → ready
#
#  Callback endpoint : POST https://apiv4.plusclouds.com/ipaas/provider
#  Auth              : X-Api-Key header baked in at generation time
#  Boot log          : /var/log/n8n-boot.log
#  Health log        : /var/log/n8n-healthcheck.log
# =============================================================================
set -euo pipefail

# ── Values baked in by your API at generation time ────────────────────────────
INSTANCE_ID="{{INSTANCE_ID}}"
N8N_VERSION="{{N8N_VERSION}}"
N8N_HOST="{{N8N_HOST}}"
N8N_PORT="{{N8N_PORT}}"
N8N_PROTOCOL="{{N8N_PROTOCOL}}"
N8N_API_KEY="{{N8N_API_KEY}}"
N8N_ENCRYPTION_KEY="{{N8N_ENCRYPTION_KEY}}"
PLUSCLOUDS_API_KEY="{{PLUSCLOUDS_API_KEY}}"
CALLBACK_URL="https://apiv4.plusclouds.com/ipaas/provider"

# ── Tunables ──────────────────────────────────────────────────────────────────
HEALTH_TIMEOUT="${HEALTH_TIMEOUT:-180}"
POLL_INTERVAL="${POLL_INTERVAL:-5}"

# ── Helpers ───────────────────────────────────────────────────────────────────
log() {
  echo "[$(date -u +%Y-%m-%dT%H:%M:%SZ)] $*" | tee -a /var/log/n8n-boot.log
}

report() {
  local stage="$1"
  local message="${2:-}"

  local payload
  payload=$(cat <<JSON
{
  "instance_id": "${INSTANCE_ID}",
  "stage":       "${stage}",
  "message":     "${message}",
  "n8n_url":     "${N8N_PROTOCOL}://${N8N_HOST}:${N8N_PORT}",
  "timestamp":   "$(date -u +%Y-%m-%dT%H:%M:%SZ)"
}
JSON
)

  local http_code
  http_code=$(
    curl -s -o /dev/null -w "%{http_code}" \
      -X POST \
      -H "Content-Type: application/json" \
      -H "X-Api-Key: ${PLUSCLOUDS_API_KEY}" \
      -d "$payload" \
      --max-time 10 \
      "$CALLBACK_URL"
  ) || true

  log "Reported stage=${stage} → API HTTP ${http_code:-000}"
}

on_error() {
  local exit_code=$?
  local line=$1
  log "ERROR: script failed at line ${line} (exit ${exit_code})"
  report "error" "Boot script failed at line ${line} — exit code ${exit_code}. Check /var/log/n8n-boot.log"
  exit "$exit_code"
}

trap 'on_error $LINENO' ERR

# =============================================================================
#  STAGE: booting
# =============================================================================
log "=== n8n boot script starting ==="
report "booting" "VM is up. Running boot script."

# =============================================================================
#  Write shared env file
#  Both the boot script and the health check script source this file
#  so credentials and config live in exactly one place on disk.
# =============================================================================
log "Writing /etc/n8n-boot.env…"
mkdir -p /var/lib/n8n-status

cat > /etc/n8n-boot.env <<ENV
INSTANCE_ID="${INSTANCE_ID}"
N8N_HOST="${N8N_HOST}"
N8N_PORT="${N8N_PORT}"
N8N_PROTOCOL="${N8N_PROTOCOL}"
PLUSCLOUDS_API_KEY="${PLUSCLOUDS_API_KEY}"
CALLBACK_URL="${CALLBACK_URL}"
ENV

chmod 600 /etc/n8n-boot.env
log "/etc/n8n-boot.env written."

# =============================================================================
#  Install Docker if missing
# =============================================================================
log "Checking Docker…"
if ! command -v docker >/dev/null 2>&1; then
  log "Docker not found — installing…"
  curl -fsSL https://get.docker.com | sh
  systemctl enable --now docker
  log "Docker installed."
else
  log "Docker already present: $(docker --version)"
fi

# =============================================================================
#  Start n8n container
# =============================================================================
log "Removing any existing n8n container…"
docker rm -f n8n 2>/dev/null || true

log "Pulling n8nio/n8n:${N8N_VERSION}…"
docker pull "n8nio/n8n:${N8N_VERSION}"

log "Starting n8n container…"
docker run -d \
  --name n8n \
  --restart unless-stopped \
  -p "${N8N_PORT}:5678" \
  -e N8N_HOST="${N8N_HOST}" \
  -e N8N_PROTOCOL="${N8N_PROTOCOL}" \
  -e N8N_PORT=5678 \
  -e N8N_ENCRYPTION_KEY="${N8N_ENCRYPTION_KEY}" \
  -e N8N_API_ENABLED=true \
  -e N8N_API_KEY="${N8N_API_KEY}" \
  -v n8n_data:/home/node/.n8n \
  "n8nio/n8n:${N8N_VERSION}"

# =============================================================================
#  STAGE: starting
# =============================================================================
report "starting" "n8n container started. Installing health monitor."

# =============================================================================
#  Install health check script + systemd units
# =============================================================================
log "Installing health check script…"

cat > /usr/local/bin/n8n-healthcheck.sh <<'HEALTHCHECK'
#!/usr/bin/env bash
set -euo pipefail

source /etc/n8n-boot.env

STATE_FILE="/var/lib/n8n-status/health-state"
LOG="/var/log/n8n-healthcheck.log"
BASE_URL="http://localhost:${N8N_PORT}"

log() {
  echo "[$(date -u +%Y-%m-%dT%H:%M:%SZ)] $*" >> "$LOG"
}

report() {
  local stage="$1"
  local message="${2:-}"
  local payload
  payload=$(cat <<JSON
{
  "instance_id": "${INSTANCE_ID}",
  "stage":       "${stage}",
  "message":     "${message}",
  "n8n_url":     "${N8N_PROTOCOL}://${N8N_HOST}:${N8N_PORT}",
  "timestamp":   "$(date -u +%Y-%m-%dT%H:%M:%SZ)"
}
JSON
)
  local http_code
  http_code=$(
    curl -s -o /dev/null -w "%{http_code}" \
      -X POST \
      -H "Content-Type: application/json" \
      -H "X-Api-Key: ${PLUSCLOUDS_API_KEY}" \
      -d "$payload" \
      --max-time 10 \
      "${CALLBACK_URL}"
  ) || true
  log "Reported stage=${stage} → API HTTP ${http_code:-000}"
}

last_state() { cat "$STATE_FILE" 2>/dev/null || echo "unknown"; }
save_state()  { mkdir -p "$(dirname "$STATE_FILE")"; echo "$1" > "$STATE_FILE"; }

# ── Check ─────────────────────────────────────────────────────────────────────
http_code=$(
  curl -s -o /dev/null -w "%{http_code}" \
    --max-time 5 \
    "${BASE_URL}/healthz"
) || true

previous=$(last_state)

if [[ "$http_code" == "200" ]]; then
  log "Healthy (HTTP 200)"
  if [[ "$previous" != "healthy" ]]; then
    log "State change: ${previous} → healthy"
    report "healthy" "n8n recovered and is responding normally."
    save_state "healthy"
  fi
else
  log "Unhealthy (HTTP ${http_code:-000}). Previous state: ${previous}"
  if [[ "$previous" != "restarting" ]]; then
    report "unhealthy" "n8n health check failed (HTTP ${http_code:-000}). Restarting container."
    save_state "restarting"
    log "Restarting n8n container…"
    docker restart n8n >> "$LOG" 2>&1 || true
    report "restarting" "n8n container restart issued. Waiting for recovery."
    log "Restart issued."
  else
    log "Already in restarting state — waiting for recovery."
  fi
fi
HEALTHCHECK

chmod +x /usr/local/bin/n8n-healthcheck.sh
log "Health check script installed."

# ── systemd service ───────────────────────────────────────────────────────────
cat > /etc/systemd/system/n8n-healthcheck.service <<UNIT
[Unit]
Description=n8n Health Check
After=docker.service
Requires=docker.service

[Service]
Type=oneshot
ExecStart=/usr/local/bin/n8n-healthcheck.sh
StandardOutput=journal
StandardError=journal
UNIT

# ── systemd timer (every 30s, first run 60s after boot) ───────────────────────
cat > /etc/systemd/system/n8n-healthcheck.timer <<UNIT
[Unit]
Description=Run n8n Health Check every 30 seconds
After=n8n-healthcheck.service

[Timer]
OnBootSec=60
OnUnitActiveSec=30s
AccuracySec=5s

[Install]
WantedBy=timers.target
UNIT

systemctl daemon-reload
systemctl enable --now n8n-healthcheck.timer
log "Health monitor enabled (every 30s)."

# =============================================================================
#  Wait for /healthz
# =============================================================================
log "Waiting for n8n to become healthy on port ${N8N_PORT}…"
elapsed=0

until curl -sf "http://localhost:${N8N_PORT}/healthz" -o /dev/null 2>/dev/null; do
  if (( elapsed >= HEALTH_TIMEOUT )); then
    log "Health check timed out after ${HEALTH_TIMEOUT}s. Last container logs:"
    docker logs n8n --tail 30 >> /var/log/n8n-boot.log 2>&1 || true
    exit 1
  fi
  log "  Not healthy yet (${elapsed}s / ${HEALTH_TIMEOUT}s)…"
  sleep "$POLL_INTERVAL"
  (( elapsed += POLL_INTERVAL ))
done

log "n8n is healthy after ${elapsed}s."

# Seed the initial state so the health monitor doesn't false-alarm on first run
echo "healthy" > /var/lib/n8n-status/health-state

# =============================================================================
#  STAGE: ready
# =============================================================================
report "ready" "n8n is live and healthy. Health monitor active."

log "============================================"
log " n8n is ready!"
log " URL   : ${N8N_PROTOCOL}://${N8N_HOST}:${N8N_PORT}"
log " Health: systemctl status n8n-healthcheck.timer"
log " Logs  : journalctl -u n8n-healthcheck.service -f"
log "============================================"
