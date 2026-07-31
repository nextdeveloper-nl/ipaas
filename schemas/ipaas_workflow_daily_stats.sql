-- PostgreSQL

CREATE TABLE ipaas_workflow_daily_stats (
    id                 bigint NOT NULL DEFAULT nextval('ipaas_workflow_daily_stats_id_seq'::regclass),
    uuid               uuid DEFAULT gen_random_uuid(),
    stat_date          date NOT NULL, -- [label:Calendar date this stat row covers (UTC)]
    ipaas_workflow_id  bigint NOT NULL,
    iam_account_id     bigint NOT NULL,
    total_executions   integer NOT NULL DEFAULT 0,
    success_count      integer NOT NULL DEFAULT 0,
    error_count        integer NOT NULL DEFAULT 0,
    canceled_count     integer NOT NULL DEFAULT 0,
    avg_duration_ms    integer NOT NULL DEFAULT 0, -- [label:Average wall-clock execution time in milliseconds for the day]
    max_duration_ms    integer NOT NULL DEFAULT 0, -- [label:Longest single execution time in milliseconds for the day]
    created_at         timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at         timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT ipaas_workflow_daily_stats_pkey PRIMARY KEY (id)
);
