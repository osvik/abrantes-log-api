CREATE TABLE IF NOT EXISTS logs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    microtime TEXT NOT NULL,
    date_iso8601 TEXT NOT NULL,
    ip_address TEXT NOT NULL,
    user_agent TEXT NOT NULL,
    referrer TEXT NOT NULL,
    event TEXT NOT NULL,
    ab_test_data TEXT NOT NULL,
    experiment_name TEXT NOT NULL,
    variant_name TEXT NOT NULL,
    url TEXT NOT NULL,
    note TEXT NOT NULL
);

CREATE INDEX IF NOT EXISTS idx_logs_date ON logs(date_iso8601);
CREATE INDEX IF NOT EXISTS idx_logs_event ON logs(event);
CREATE INDEX IF NOT EXISTS idx_logs_experiment ON logs(experiment_name);
