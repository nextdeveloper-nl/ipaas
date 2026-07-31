-- PostgreSQL
-- VIEW (read-only; re-run this file with CREATE OR REPLACE VIEW whenever the SELECT needs to change)

CREATE OR REPLACE VIEW ipaas_execution_daily_stats AS
WITH date_spine AS (
         SELECT generate_series((CURRENT_DATE - 13)::timestamp with time zone, CURRENT_DATE::timestamp with time zone, '1 day'::interval)::date AS stat_date
        ), daily_agg AS (
         SELECT ipaas_workflow_executions.started_at::date AS stat_date,
            ipaas_workflow_executions.iam_account_id,
            ipaas_workflow_executions.ipaas_provider_id,
            count(*) AS total_executions,
            count(*) FILTER (WHERE ipaas_workflow_executions.status = 'success'::text) AS success_count,
            count(*) FILTER (WHERE ipaas_workflow_executions.status = 'error'::text) AS error_count,
            count(*) FILTER (WHERE ipaas_workflow_executions.status = 'canceled'::text) AS canceled_count
           FROM ipaas_workflow_executions
          WHERE ipaas_workflow_executions.started_at >= (CURRENT_DATE - 13)::timestamp with time zone AND ipaas_workflow_executions.started_at < (CURRENT_DATE + 1)::timestamp with time zone AND ipaas_workflow_executions.deleted_at IS NULL
          GROUP BY (ipaas_workflow_executions.started_at::date), ipaas_workflow_executions.iam_account_id, ipaas_workflow_executions.ipaas_provider_id
        )
 SELECT ds.stat_date,
    p.iam_account_id,
    p.id AS ipaas_provider_id,
    p.name AS provider_name,
    p.provider_type,
    COALESCE(da.total_executions, 0::bigint) AS total_executions,
    COALESCE(da.success_count, 0::bigint) AS success_count,
    COALESCE(da.error_count, 0::bigint) AS error_count,
    COALESCE(da.canceled_count, 0::bigint) AS canceled_count,
    round(100.0 * COALESCE(da.success_count, 0::bigint)::numeric / NULLIF(COALESCE(da.total_executions, 0::bigint), 0)::numeric, 2) AS success_rate
   FROM date_spine ds
     CROSS JOIN ipaas_providers p
     LEFT JOIN daily_agg da ON da.stat_date = ds.stat_date AND da.iam_account_id = p.iam_account_id AND da.ipaas_provider_id = p.id
  WHERE p.deleted_at IS NULL
  ORDER BY ds.stat_date, p.name;
