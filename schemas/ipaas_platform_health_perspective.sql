-- PostgreSQL
-- VIEW (read-only; re-run this file with CREATE OR REPLACE VIEW whenever the SELECT needs to change)

CREATE OR REPLACE VIEW ipaas_platform_health_perspective AS
WITH active_workflows AS (
         SELECT ipaas_workflows.ipaas_provider_id,
            count(*) AS active_count
           FROM ipaas_workflows
          WHERE ipaas_workflows.status = 'active'::text AND ipaas_workflows.deleted_at IS NULL
          GROUP BY ipaas_workflows.ipaas_provider_id
        ), today_stats AS (
         SELECT ipaas_workflow_executions.ipaas_provider_id,
            count(*) AS executions_today,
            count(*) FILTER (WHERE ipaas_workflow_executions.status = 'success'::text) AS success_today,
            count(*) FILTER (WHERE ipaas_workflow_executions.status = 'error'::text) AS errors_today,
            count(*) FILTER (WHERE ipaas_workflow_executions.status = 'running'::text) AS running_today
           FROM ipaas_workflow_executions
          WHERE ipaas_workflow_executions.started_at >= CURRENT_DATE::timestamp with time zone AND ipaas_workflow_executions.started_at < (CURRENT_DATE::timestamp with time zone + '1 day'::interval) AND ipaas_workflow_executions.deleted_at IS NULL
          GROUP BY ipaas_workflow_executions.ipaas_provider_id
        ), last_execution AS (
         SELECT DISTINCT ON (ipaas_workflow_executions.ipaas_provider_id) ipaas_workflow_executions.ipaas_provider_id,
            ipaas_workflow_executions.started_at AS last_execution_at,
            ipaas_workflow_executions.status AS last_execution_status
           FROM ipaas_workflow_executions
          WHERE ipaas_workflow_executions.deleted_at IS NULL
          ORDER BY ipaas_workflow_executions.ipaas_provider_id, ipaas_workflow_executions.started_at DESC
        ), rates AS (
         SELECT today_stats.ipaas_provider_id,
            round(100.0 * today_stats.success_today::numeric / NULLIF(today_stats.executions_today, 0)::numeric, 2) AS success_rate_today
           FROM today_stats
        )
 SELECT p.iam_account_id,
    p.id AS ipaas_provider_id,
    p.uuid AS ipaas_provider_uuid,
    p.name AS provider_name,
    p.provider_type,
    p.is_default_wap,
    COALESCE(aw.active_count, 0::bigint) AS active_workflows,
    COALESCE(ts.executions_today, 0::bigint) AS executions_today,
    COALESCE(ts.success_today, 0::bigint) AS success_today,
    COALESCE(ts.errors_today, 0::bigint) AS errors_today,
    COALESCE(ts.running_today, 0::bigint) AS running_today,
    r.success_rate_today,
    le.last_execution_at,
    le.last_execution_status,
        CASE
            WHEN COALESCE(ts.executions_today, 0::bigint) = 0 THEN 'idle'::text
            WHEN COALESCE(r.success_rate_today, 0::numeric) >= 90::numeric THEN 'online'::text
            WHEN COALESCE(r.success_rate_today, 0::numeric) >= 50::numeric THEN 'degraded'::text
            ELSE 'offline'::text
        END AS health_status,
    p.created_at AS provider_created_at
   FROM ipaas_providers p
     LEFT JOIN active_workflows aw ON aw.ipaas_provider_id = p.id
     LEFT JOIN today_stats ts ON ts.ipaas_provider_id = p.id
     LEFT JOIN rates r ON r.ipaas_provider_id = p.id
     LEFT JOIN last_execution le ON le.ipaas_provider_id = p.id
  WHERE p.deleted_at IS NULL;
