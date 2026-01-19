# AppexTech Checkout Kata

## Offline test execution

This kata can be tested without Composer or network access.

1. Place a PHPUnit PHAR at `tools/phpunit.phar`.
   - You can download `phpunit.phar` on a machine with internet access and copy it here.
2. Run the tests:

```bash
php tools/phpunit.phar -c phpunit.xml
```

The test bootstrap uses a local autoloader, so Composer is not required.
