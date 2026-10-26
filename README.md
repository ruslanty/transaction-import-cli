# Transaction import CLI application

CLI application for importing transactions from a JSON file.

## Requirements

- Docker Compose

## Technology stack

- PHP 8.5.12
- FrankenPHP
- MySQL 9.7
- Docker Compose
- PDO
- PHPUnit
- PHPStan
- PHP-CS-Fixer
- Rector
- Monolog

## Usage

Show CLI information:

```bash
docker compose run --rm app bin/console about
```

---

Import transaction from JSON file:

```bash
docker compose run --rm app bin/console import var/input.json
```

Expected result:

```text
Transaction imported: trx-001
```

---

Import transactions from JSON array:

```bash
docker compose run --rm app bin/console import var/input-bulk.json
```

Expected result:

```text
Transactions imported: 2
```

---

Invalid input is rejected and the database transaction is rolled back:

```bash
docker compose run --rm app bin/console import var/input-invalid.json
```

Expected result:

```text
Error: Transaction amount must be a valid decimal number
```

---

Importing the same transaction again is rejected by `external_id`:

```text
Error: Transaction with external ID "external-001" already exists
```

---

Run this to start with empty database (next import command will create fresh MySQL volume):

```bash
docker compose down -v
```

## Check MySQL data

```bash
docker compose exec mysql mysql -h 127.0.0.1 -utransaction_import -ptransaction_import transaction_import -e "SELECT id, external_id, amount_minor_units, currency, status FROM transactions;"
```

 - The amount is stored in minor currency units. For example, `125.50 EUR` is stored as `12550`.

## Check logs

Application logs stored in `var/app.log`.

Read the log from application container:

```bash
docker compose run --rm app cat var/app.log
```

## Tests and code analysis

Run the complete project check:

```bash
docker compose run --rm app composer quality
```

 - Runs simultaneously `php-cs-fixer`, `phpstan`, `rector` and `phpunit`

## Structure

```text
src/Domain              domain objects and contracts
src/Application         import and persistence use cases
src/Infrastructure      JSON, PDO, MySQL and logging
src/Presentation/CLI    import CLI command
tests                   unit and integration tests
docker/mysql/init.sql   transactions table schema
```
