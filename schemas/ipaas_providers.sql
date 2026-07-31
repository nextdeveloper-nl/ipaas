-- PostgreSQL

CREATE TABLE ipaas_providers (
    id                       bigint NOT NULL DEFAULT nextval('ipaas_providers_id_seq'::regclass),
    uuid                     uuid DEFAULT gen_random_uuid(),
    name                     text NOT NULL,
    description              text,
    provider_type            text DEFAULT 'hosted'::text,
    is_default_wap           boolean DEFAULT true,
    iaas_virtual_machine_id  bigint,
    external_account_id      text, -- [!model]
    region                   text, -- [label:Geographic or logical region of the provider (e.g. eu, us) — legacy field]
    iam_account_id           bigint NOT NULL,
    iam_user_id              bigint NOT NULL,
    created_at               timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at               timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at               timestamp with time zone,
    base_url                 text, -- [label:Full URL of the WAP instance (e.g. https://n8n.example.com)]
    api_token                text, -- [label:Primary API credential — stored encrypted, decrypted by the application]
    api_secret               text, -- [label:Secondary API credential (OAuth 1.0 secret, etc.) — stored encrypted]
    CONSTRAINT ipaas_providers_pkey PRIMARY KEY (id)
);
