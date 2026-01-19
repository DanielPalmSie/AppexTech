# AppexTech Checkout Kata

A flexible checkout system implemented in pure PHP with extensible pricing rules  
and full unit test coverage.

The solution is intentionally designed **without a database or framework** and serves  
as a foundation for future discount rule extensions.

---

## Requirements

- PHP 8.1+
- No Composer required for test execution
- Docker optional (recommended on Windows)

---

## Running tests (offline, no Composer)

This project supports running PHPUnit **without network access**.

### Option 1: Using PHPUnit PHAR (offline)

1. Place a PHPUnit PHAR at:

```
tools/phpunit.phar
```

You can download it from https://phpunit.de/ on a machine with internet access  
and copy it into the project.

2. Run the tests:

```bash
php tools/phpunit.phar -c phpunit.xml
```

The test bootstrap uses a local autoloader (`tests/bootstrap.php`),  
so Composer is **not required**.

---

### Option 2: Running tests via Docker (recommended on Windows)

The repository includes a Docker setup suitable for running tests  
in a consistent environment.

```bash
docker compose up -d --build
docker compose exec php sh -lc "cd /var/www/html && php tools/phpunit.phar -c phpunit.xml"
```

---

## Notes

- Pricing rules are implemented as **independent rule classes** and may depend on
  multiple products.
- Adding a new discount rule does **not** require changes to existing rules
  or to the checkout logic.
- All monetary values are handled using **integer pennies** to avoid
  floating-point errors.
- Unit tests cover all code paths (**100% coverage**).

---

## Example API

```php
$checkout = new Checkout([
    new BogofRule('FR1'),
    new BulkPriceRule('SR1', 3, 450),
]);

$checkout->scan('FR1');
$checkout->scan('SR1');

echo $checkout->total();
```
