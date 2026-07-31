-- PostgreSQL

CREATE TABLE ipaas_accounts (
    id                  bigint NOT NULL DEFAULT nextval('ipaas_accounts_id_seq'::regclass),
    uuid                uuid DEFAULT gen_random_uuid(),
    limits              json,
    iam_account_id      bigint NOT NULL,
    created_at          timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at          timestamp with time zone,
    is_service_enabled  boolean DEFAULT true,
    CONSTRAINT ipaas_accounts_pkey PRIMARY KEY (id)
);
