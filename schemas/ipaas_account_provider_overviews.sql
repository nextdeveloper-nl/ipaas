-- PostgreSQL
-- VIEW (read-only; re-run this file with CREATE OR REPLACE VIEW whenever the SELECT needs to change)

CREATE OR REPLACE VIEW ipaas_account_provider_overviews AS
WITH provider_counts AS (
         SELECT ipaas_providers.iam_account_id,
            count(*) AS total_providers
           FROM ipaas_providers
          WHERE ipaas_providers.deleted_at IS NULL
          GROUP BY ipaas_providers.iam_account_id
        ), workflow_counts AS (
         SELECT ipaas_workflows.ipaas_provider_id,
            count(*) AS total_workflows
           FROM ipaas_workflows
          WHERE ipaas_workflows.deleted_at IS NULL
          GROUP BY ipaas_workflows.ipaas_provider_id
        ), today_stats AS (
         SELECT ipaas_workflow_executions.ipaas_provider_id,
            count(*) AS executions_today,
            count(*) FILTER (WHERE ipaas_workflow_executions.status = 'success'::text) AS success_today,
            count(*) FILTER (WHERE ipaas_workflow_executions.status = 'error'::text) AS errors_today
           FROM ipaas_workflow_executions
          WHERE ipaas_workflow_executions.started_at >= CURRENT_DATE::timestamp with time zone AND ipaas_workflow_executions.started_at < (CURRENT_DATE::timestamp with time zone + '1 day'::interval) AND ipaas_workflow_executions.deleted_at IS NULL
          GROUP BY ipaas_workflow_executions.ipaas_provider_id
        )
 SELECT p.iam_account_id,
    p.id AS ipaas_provider_id,
    p.uuid AS ipaas_provider_uuid,
    p.name AS ipaas_provider_name,
    p.provider_type,
    p.is_default_wap,
    p.base_url,
    COALESCE(wc.total_workflows, 0::bigint) AS total_workflows,
    COALESCE(pc.total_providers, 1::bigint) AS total_automation_engines,
    COALESCE(ts.executions_today, 0::bigint) AS executions_today,
    COALESCE(ts.success_today, 0::bigint) AS success_today,
    COALESCE(ts.errors_today, 0::bigint) AS errors_today,
    round(100.0 * COALESCE(ts.success_today, 0::bigint)::numeric / NULLIF(COALESCE(ts.executions_today, 0::bigint), 0)::numeric, 2) AS success_rate_today,
    p.created_at AS provider_created_at,
    p.updated_at AS provider_updated_at
   FROM ipaas_providers p
     LEFT JOIN provider_counts pc ON pc.iam_account_id = p.iam_account_id
     LEFT JOIN workflow_counts wc ON wc.ipaas_provider_id = p.id
     LEFT JOIN today_stats ts ON ts.ipaas_provider_id = p.id
  WHERE p.deleted_at IS NULL;
