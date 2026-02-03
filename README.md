# Abrantes Log API

Simple PHP endpoint that logs incoming requests to an SQLite database and returns JSON. It's used by the [Abrantes Log Plugin](https://github.com/osvik/abrantes/blob/main/plugins/log.js).

## Requirements

- PHP with `pdo_sqlite` enabled
- `sqlite3` CLI (to initialize the database)

## Setup

1. Configure your web server document root to `public/` (only this folder should be exposed).
2. Create the SQLite database (from the repository root):

   ```sh
   sqlite3 logs.db < create.sql
   ```

3. Configure the allowed origins and database path in [config.php](config.php).
4. Ensure the PHP/web-server user can write to `logs.db` (and the repo folder, if needed).


## Endpoint

- `GET /` (or `GET /index.php`) logs the request and returns:
  - `200` with `{"result":"ok"}`
- `OPTIONS /` returns `204` for CORS preflight

### Query parameters (all optional)

Missing parameters are stored as an empty string in the database.

- `event`
- `ab_test_data`
- `experiment_name`
- `variant_name`
- `url`
- `note`

Example:

```sh
curl "http://localhost/?event=example_event&ab_test_data=example_test-1&experiment_name=example_test&variant_name=v1&url=http%3A%2F%2Fexample.com&note=lorem+ipsum"
```

## What gets logged

Each request inserts one row into the `logs` table with:

### From the server

- `microtime` (seconds since epoch, with microseconds)
- `date_iso8601` (UTC, ISO 8601, e.g. `2026-01-24T19:03:02Z`)
- `ip_address` (uses `HTTP_X_FORWARDED_FOR` first, otherwise `REMOTE_ADDR`)
- `user_agent` (from `HTTP_USER_AGENT`)
- `referrer` (from `HTTP_REFERER`)

### From the URL

- `event`
- `ab_test_data`
- `experiment_name`
- `variant_name`
- `url`
- `note`

## Inspecting logs

```sh
sqlite3 logs.db "SELECT * FROM logs ORDER BY id DESC LIMIT 20;"
```
