# Test webi package

## Create database

```sql
CREATE DATABASE IF NOT EXISTS laravel_testing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## Mysql user

```sql
GRANT ALL PRIVILEGES ON *.* TO root@localhost IDENTIFIED BY 'toor' WITH GRANT OPTION;
GRANT ALL PRIVILEGES ON *.* TO root@127.0.0.1 IDENTIFIED BY 'toor' WITH GRANT OPTION;
```

## Update .env.testing

```conf
# environment
APP_ENV=testing
APP_DEBUG=true

# Mysql settings
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_testing
DB_USERNAME=root
DB_PASSWORD=toor

# Smpt (etc. gmail, mailgun or localhost)
MAIL_MAILER=smtp
MAIL_HOST=localhost
MAIL_PORT=25
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=noreply@local.host
MAIL_FROM_NAME="${APP_NAME}"
```

## Copy locales to Laravel app/lang

```sh
php artisan vendor:publish --tag=webi-lang-en
php artisan vendor:publish --tag=webi-lang-pl
```

## Create database tables

Commands: --env, --seed, --force

```sh
# Create
php artisan --env=testing migrate

# Refresh
php artisan --env=testing migrate:fresh
```

## Add in phpunit.xml

```xml
<!-- Composer vendor -->
<testsuites>
  <testsuite name="Webi">
  <directory suffix="Test.php">./vendor/atomjoy/webi/tests</directory>
  </testsuite>
  <testsuite name="WebiApi">
  <directory suffix="Test.php">./vendor/atomjoy/webi/tests/Webi/Api</directory>
  </testsuite>
  <testsuite name="WebiLang">
  <directory suffix="Test.php">./vendor/atomjoy/webi/tests/Webi/Lang</directory>
  </testsuite>
</testsuites>

<php>
  <env name="APP_ENV" value="testing" force="true"/>
  <env name="APP_DEBUG" value="true" force="true"/>
</php>
```

## Run tests

```sh
# Webi test
php artisan vendor:publish --tag=webi-lang-en
php artisan vendor:publish --tag=webi-lang-pl

# Webi api
php artisan test --stop-on-failure --testsuite=WebiApi

# Webi api with pl
php artisan test --stop-on-failure --testsuite=WebiLang

# All tests
php artisan test --stop-on-failure --testsuite=Webi
```

## Dev settings (ignore)

### Local composer dev

```json
{
 "repositories": [
  {
   "type": "path",
   "url": "packages/atomjoy/webi"
  }
 ],
 "require": {
  "php": "^8.1.0",
  "atomjoy/webi": "dev-main"
 }
}
```

### Linux install

```sh
sudo apt install git composer ufw net-tools dnsutils mailutils
sudo apt install mariadb-server postfix nginx redis memcached
sudo apt install -y php8.1-fpm
sudo apt install -y php8.1-{mysql,xml,curl,mbstring,opcache,gd,imagick,imap,bcmath,bz2,zip,intl,redis,memcache,memcached}
```
