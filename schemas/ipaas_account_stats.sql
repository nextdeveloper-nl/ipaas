-- PostgreSQL
-- VIEW (read-only; re-run this file with CREATE OR REPLACE VIEW whenever the SELECT needs to change)

CREATE OR REPLACE VIEW ipaas_account_stats AS
WITH today_stats AS (
         SELECT ipaas_workflow_executions.iam_account_id,
            count(*) AS executions_today,
            count(*) FILTER (WHERE ipaas_workflow_executions.status = 'success'::text) AS success_today,
            count(*) FILTER (WHERE ipaas_workflow_executions.status = 'error'::text) AS errors_today
           FROM ipaas_workflow_executions
          WHERE ipaas_workflow_executions.started_at >= CURRENT_DATE::timestamp with time zone AND ipaas_workflow_executions.started_at < (CURRENT_DATE::timestamp with time zone + '1 day'::interval) AND ipaas_workflow_executions.deleted_at IS NULL
          GROUP BY ipaas_workflow_executions.iam_account_id
        )
 SELECT p.iam_account_id,
    count(DISTINCT p.id) AS total_providers,
    count(DISTINCT w.id) AS total_workflows,
    COALESCE(ts.executions_today, 0::bigint) AS executions_today,
    COALESCE(ts.success_today, 0::bigint) AS success_today,
    COALESCE(ts.errors_today, 0::bigint) AS errors_today,
    round(100.0 * COALESCE(ts.success_today, 0::bigint)::numeric / NULLIF(COALESCE(ts.executions_today, 0::bigint), 0)::numeric, 2) AS success_rate_today
   FROM ipaas_providers p
     LEFT JOIN ipaas_workflows w ON w.ipaas_provider_id = p.id AND w.deleted_at IS NULL
     LEFT JOIN today_stats ts ON ts.iam_account_id = p.iam_account_id
  WHERE p.deleted_at IS NULL
  GROUP BY p.iam_account_id, ts.executions_today, ts.success_today, ts.errors_today;
