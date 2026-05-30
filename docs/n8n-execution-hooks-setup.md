# N8N Self-Hosted Execution Hooks — Setup Guide

> Receive real-time notifications in your iPaaS platform whenever an N8N workflow starts, finishes, or errors — **without adding any nodes to user workflows**.

---

## Prerequisites

- Docker & Docker Compose installed on your server
- An N8N self-hosted instance (or planning to set one up)
- A publicly accessible webhook endpoint on your iPaaS platform

---

## Overview

N8N's `ExecutionLifecycleHooks` system allows you to run custom server-side code on every workflow execution event. You configure it once at the infrastructure level via the `EXTERNAL_HOOK_FILES` environment variable — no workflow changes required.

**Events available:**

| Hook | Fires when |
|---|---|
| `workflowExecuteBefore` | A workflow execution starts |
| `workflowExecuteAfter` | A workflow execution finishes (success, error, or cancelled) |

---

## Step 1 — Create the Hooks File

Create a file called `n8n-hooks.js` in the same directory as your `docker-compose.yml`:

```javascript
module.exports = {
  workflowExecuteBefore: [
    async function (context) {
      try {
        await fetch('https://your-platform.com/webhooks/n8n', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-iPaaS-Secret': process.env.IPAAS_WEBHOOK_SECRET,
          },
          body: JSON.stringify({
            event: 'execution.started',
            workflowId: context.workflowData.id,
            workflowName: context.workflowData.name,
            executionId: this.executionId,
            timestamp: new Date().toISOString(),
          }),
        });
      } catch (err) {
        // Never let a hook crash the N8N process itself
        console.error('[iPaaS Hook] Failed to notify execution start:', err.message);
      }
    },
  ],

  workflowExecuteAfter: [
    async function (context, runData) {
      try {
        await fetch('https://your-platform.com/webhooks/n8n', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-iPaaS-Secret': process.env.IPAAS_WEBHOOK_SECRET,
          },
          body: JSON.stringify({
            event: 'execution.finished',
            workflowId: context.workflowData.id,
            workflowName: context.workflowData.name,
            executionId: this.executionId,
            status: runData.status,       // "success" | "error" | "canceled"
            startedAt: runData.startedAt,
            stoppedAt: runData.stoppedAt,
            timestamp: new Date().toISOString(),
          }),
        });
      } catch (err) {
        console.error('[iPaaS Hook] Failed to notify execution end:', err.message);
      }
    },
  ],
};
```

> **Replace** `https://your-platform.com/webhooks/n8n` with your actual webhook receiver URL.

---

## Step 2 — Configure Docker Compose

Create or update your `docker-compose.yml` in the same directory:

```yaml
version: '3.8'

services:
  n8n:
    image: n8nio/n8n
    restart: always
    ports:
      - "5678:5678"
    environment:
      # N8N general config
      - N8N_HOST=your-n8n-domain.com
      - N8N_PORT=5678
      - N8N_PROTOCOL=https
      - WEBHOOK_URL=https://your-n8n-domain.com/

      # External hooks — key line
      - EXTERNAL_HOOK_FILES=/hooks/n8n-hooks.js

      # Shared secret for your platform to validate incoming requests
      - IPAAS_WEBHOOK_SECRET=replace-with-a-strong-secret

    volumes:
      - n8n_data:/home/node/.n8n
      # Mount your hooks file into the container (read-only)
      - ./n8n-hooks.js:/hooks/n8n-hooks.js:ro

volumes:
  n8n_data:
```

---

## Step 3 — Start N8N

```bash
docker compose up -d
```

To confirm N8N started correctly:

```bash
docker compose logs -f n8n
```

You should see N8N boot without errors. The hooks file is loaded at startup.

---

## Step 4 — Verify the Hooks Are Firing

Add a temporary debug log to your hooks file to confirm events are being received:

```javascript
workflowExecuteBefore: [
  async function (context) {
    console.log('[iPaaS Hook] Execution started:', context.workflowData.name);
    // ... rest of your fetch call
  }
]
```

Then trigger any workflow in N8N and watch the logs:

```bash
docker logs -f <container_name>
```

You should see your log line appear immediately when the workflow runs.

---

## Step 5 — Secure Your Webhook Receiver

On your iPaaS platform, validate the shared secret on every incoming request:

```javascript
// Express.js example
app.post('/webhooks/n8n', (req, res) => {
  const secret = req.headers['x-ipaas-secret'];

  if (secret !== process.env.IPAAS_WEBHOOK_SECRET) {
    return res.status(401).json({ error: 'Unauthorized' });
  }

  const { event, workflowId, executionId, status, timestamp } = req.body;

  // Handle the event
  console.log(`[N8N Event] ${event} — workflow: ${workflowId}, execution: ${executionId}`);

  res.status(200).json({ received: true });
});
```

---

## Payload Reference

### `execution.started`

```json
{
  "event": "execution.started",
  "workflowId": "abc123",
  "workflowName": "My Workflow",
  "executionId": "xyz789",
  "timestamp": "2026-05-30T10:00:00.000Z"
}
```

### `execution.finished`

```json
{
  "event": "execution.finished",
  "workflowId": "abc123",
  "workflowName": "My Workflow",
  "executionId": "xyz789",
  "status": "success",
  "startedAt": "2026-05-30T10:00:00.000Z",
  "stoppedAt": "2026-05-30T10:00:05.123Z",
  "timestamp": "2026-05-30T10:00:05.124Z"
}
```

**Possible `status` values:**

| Value | Meaning |
|---|---|
| `success` | Workflow completed without errors |
| `error` | Workflow encountered an unhandled error |
| `canceled` | Workflow was manually stopped |

---

## Node.js Version Note

The hooks file uses the native `fetch` API, which requires **Node.js 18 or higher**. Current N8N Docker images ship with Node 18+, but you can verify with:

```bash
docker exec <container_name> node --version
```

If the version is below v18, replace `fetch` with the built-in `https` module:

```javascript
const https = require('https');

function postToIPaaS(payload) {
  return new Promise((resolve, reject) => {
    const data = JSON.stringify(payload);
    const options = {
      hostname: 'your-platform.com',
      path: '/webhooks/n8n',
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Content-Length': Buffer.byteLength(data),
        'X-iPaaS-Secret': process.env.IPAAS_WEBHOOK_SECRET,
      },
    };
    const req = https.request(options, resolve);
    req.on('error', reject);
    req.write(data);
    req.end();
  });
}
```

---

## Troubleshooting

| Problem | Check |
|---|---|
| Hooks not firing | Confirm `EXTERNAL_HOOK_FILES` path matches the volume mount path exactly |
| `fetch is not defined` error | Node version is below 18 — use the `https` module fallback above |
| 401 errors on your platform | Verify `IPAAS_WEBHOOK_SECRET` matches on both sides |
| Hook crashes N8N | Ensure all async calls are wrapped in `try/catch` |
| No logs appearing | Run `docker compose logs -f n8n` and trigger a workflow manually |

---

## File Structure

Your deployment directory should look like this:

```
your-n8n-deployment/
├── docker-compose.yml
└── n8n-hooks.js
```

---

## Summary

| What | How |
|---|---|
| Execution start event | `workflowExecuteBefore` hook |
| Execution end/error event | `workflowExecuteAfter` hook |
| Configuration | `EXTERNAL_HOOK_FILES` env var |
| Zero workflow changes | ✅ Hooks are infrastructure-level |
| Security | Shared secret via `X-iPaaS-Secret` header |
