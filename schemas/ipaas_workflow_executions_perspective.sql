-- PostgreSQL
-- VIEW (read-only; re-run this file with CREATE OR REPLACE VIEW whenever the SELECT needs to change)

CREATE OR REPLACE VIEW ipaas_workflow_executions_perspective AS
SELECT e.id,
    e.uuid,
    e.status,
    e.trigger_mode,
    e.started_at,
    e.finished_at,
    e.duration_ms,
    e.error_message,
    e.error_node,
    e.external_execution_id,
    e.ipaas_workflow_id,
    w.name AS workflow_name,
    e.ipaas_provider_id,
    p.name AS provider_name,
    p.provider_type,
    e.iam_account_id,
    e.created_at,
    e.updated_at
   FROM ipaas_workflow_executions e
     JOIN ipaas_workflows w ON w.id = e.ipaas_workflow_id
     JOIN ipaas_providers p ON p.id = e.ipaas_provider_id
  WHERE e.deleted_at IS NULL;
