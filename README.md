# paratest-databases-factory

This library provides a convinient way to automate the creation of test databases and is supposed to 
be used when running PHP unit tests in parallel. It is compatible with paratest library - https://github.com/paratestphp/paratest.

## How to install
Execute `composer require bancer/paratest-databases-factory` or `composer require bancer/paratest-databases-factory --update-no-dev` to install by composer.

## How to use

Create test databases in bootstrap.php:

```php
use Bancer\ParatestDatabasesFactory\DatabasesFactory;
...
(new DatabasesFactory())
    ->setDsn(getenv('pf_dsn'))
    ->setUsername(getenv('pf_user'))
    ->setPassword(getenv('pf_pass'))
    ->createDatabase('pf_test');
```
This will create pf_test1, pf_test2 etc. test databases when unit tests are started.
The database name and credentials are just an example here. The requirement is that the user has
permissions to connect to the database and has been granted the privilege to create databases.

Load the bootstrap file in phpunit.xml file if not already done, 
f.ex.: `<phpunit ... bootstrap="./bootstrap.php"/>`.

Create the database user and grant privileges to create databases:

```sql
CREATE USER 'pf_user'@'%' IDENTIFIED BY 'your_password';
GRANT CREATE ON *.* TO 'pf_user'@'%';
CREATE DATABASE pf_test;
GRANT SELECT ON `pf_test`.* TO 'pf_user'@'%';
```

Add the database credentials to phpunit.xml:

```xml
<env name="pf_dsn" value="mysql:dbname=pf_test;host=localhost"/>
<env name="pf_user" value="pf_user"/>
<env name="pf_pass" value="your_password"/>
```

Adjust your database configuration by appending `TEST_TOKEN` environment variable value. 
Something like this:

```php
if (getenv('TEST_TOKEN') !== false) { // Using paratest
    $databaseName .= getenv('TEST_TOKEN');
}
```

There are two ways how to run phpunit tests in parallel:

1. By using plain phpunit.
    * Pros: 
        * No dependencies to third party libraries.
        * Can be implemented with any version of PHP and PHPUnit.
    * Cons:
        * Complex setup.
    * Howto:
        * Split your unit tests into two or more testsuites that take approximately the same time to run. 
        The example below is for two test suites that are named ci-1 and ci-2.
        * Ensure that shell's job control is enabled or execute `set -m` in the console.
        * Execute `TEST_TOKEN=1 Vendor/bin/phpunit --testsuite ci-1 &> /tmp/ci-1.out & TEST_TOKEN=2 Vendor/bin/phpunit --testsuite ci-2 & fg` or similar command. 
        This sets `TEST_TOKEN` environment variable, starts the first test suite in the background process 
        and writes its output to a file in tmp folder, starts the second test suite and brings it output 
        to the foreground. This way two test suites are run in parallel. 
        Run the slowest test suite the last.
        * Execute `cat ./tmp/ci-1.out` to print the results of the first test suite to the console.
        * Execute `grep "OK" ./tmp/ci-1.out` or similar command to check that the first test suite successfully finished.

2. By using paratest library.
    * Pros: 
        * Simple setup.
    * Cons:
        * Dependency to third party library.
        * Paratest is actively supported only for the latest PHP version therefore not all projects can 
        use it.
    * Howto:
        * Install paratest and follow their instructions - https://github.com/paratestphp/paratest.
