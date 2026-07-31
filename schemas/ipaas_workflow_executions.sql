-- PostgreSQL

CREATE TABLE ipaas_workflow_executions (
    id                     bigint NOT NULL DEFAULT nextval('ipaas_workflow_executions_id_seq'::regclass),
    uuid                   uuid DEFAULT gen_random_uuid(),
    ipaas_workflow_id      bigint NOT NULL,
    ipaas_provider_id      bigint NOT NULL,
    iam_account_id         bigint NOT NULL,
    external_execution_id  text NOT NULL, -- [!model][label:Execution ID assigned by the WAP provider (e.g. N8N)]
    status                 text NOT NULL, -- [label:Current state: running, success, error, waiting, canceled]
    trigger_mode           text NOT NULL, -- [label:How the execution was triggered: webhook, schedule, manual, trigger, retry]
    started_at             timestamp with time zone,
    finished_at            timestamp with time zone,
    duration_ms            integer DEFAULT ((EXTRACT(epoch FROM (finished_at - started_at)) * (1000)::numeric))::integer, -- [label:Wall-clock duration in milliseconds, auto-computed from started_at and finished_at]
    error_message          text,
    error_node             text, -- [label:Name of the N8N node where the execution failed]
    error_stack            text,
    retry_of_execution_id  bigint, -- [!model][label:FK to the parent ipaas_workflow_executions row this is a retry of]
    created_at             timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at             timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at             timestamp with time zone,
    CONSTRAINT ipaas_workflow_executions_pkey PRIMARY KEY (id)
);
