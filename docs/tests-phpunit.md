# Tests with PHPUnit Testing Framework

Unit and application tests are realized with PHPUnit Testing framework. They are based on the symfony standards for using PHPUnit as a Unit- and Application Testing Framework for symfony applications. Symfony does suggest, to not use mockups for repositories, but since the application is strongly database-driven, the tests require a test-database.

## Howto

1. Create test database in your dbms (mysql, mariadb)

2. Set test database-connection for testing in your .env.test file. There is an example file called ```.env.test.example```, that you can copy, paste and rename to ```.env.test```.

```DATABASE_URL="mysql://USERNAME:PASSWORD@127.0.0.1:3306/DB_NAME?serverVersion=5.7"```

3. Run console commands, to install the database:

```
php bin/console --env=test doctrine:database:create

php bin/console --env=test doctrine:schema:create
```

