-- PostgreSQL

CREATE TABLE ipaas_workflows (
    id                    bigint NOT NULL DEFAULT nextval('ipaas_workflows_id_seq'::regclass),
    uuid                  uuid DEFAULT gen_random_uuid(),
    name                  text NOT NULL,
    description           text,
    trigger_type          text NOT NULL,
    status                text DEFAULT 'draft'::text,
    current_version       uuid,
    ipaas_provider_id     bigint NOT NULL,
    external_workflow_id  text NOT NULL, -- [!model]
    last_synched_at       timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    iam_account_id        bigint NOT NULL,
    iam_user_id           bigint NOT NULL,
    created_at            timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at            timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at            timestamp with time zone,
    nodes                 json,
    connections           json,
    CONSTRAINT ipaas_workflows_pkey PRIMARY KEY (id)
);
