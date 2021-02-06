Classe Royale
=============

> Gestion ludique de l'évaluation des élèves, à travers cartes d'apprentissage,
> gain d'or et d'elixir.

## Running in dev

### Requirements

See [Symfony technical requirements](https://symfony.com/doc/current/setup.html#technical-requirements)

php, composer, yarn and mariadb must be installed.

```sh
$ php -v && composer -V && yarn -v && mariadb --version
PHP 7.4.14 (cli) (built: Jan  8 2021 15:34:32) ( NTS )
Copyright (c) The PHP Group
Zend Engine v3.4.0, Copyright (c) Zend Technologies
    with Zend OPcache v7.4.14, Copyright (c), by Zend Technologies
    with Xdebug v3.0.2, Copyright (c) 2002-2021, by Derick Rethans
Composer version 2.0.8 2020-12-03 17:20:38
1.22.10
mariadb  Ver 15.1 Distrib 10.5.8-MariaDB, for Linux (x86_64) using readline 5.1
```

Here are the extensions enabled in my `php.ini`:

```
extension=curl
extension=iconv
extension=intl
extension=mysqli
zend_extension=opcache
extension=pdo_mysql
zend_extension=xdebug
extension=zip
```

Depending on your PHP setup you may install some of these extensions as
separate packages with your package manager.

### Install project dependencies

Install JS dependencies

```sh
$ yarn install
```

Install PHP dependencies

```sh
$ composer install
```

Install [`symfony-cli`](https://symfony.com/download) to check that
all requirements are met on your machine.

```sh
$ symfony check:requirements
```

### Create the dev database

You must have a `mysql_user` with the ability to create a database.
Define `DATABASE_URL` in `.env.local`, to look like:

```
DATABASE_URL=mysql://mysql_user:password@localhost:3306/classe_royale?serverVersion=mariadb-10.5.8
```

Adapt `mysql_user`, `password` and `mariadb-10.5.8` to your situation.

Now you can create the database `classe_royale` with:

```sh
$ php bin/console doctrine:database:create
Created database `classe_royale` for connection named default
```

Finally apply the schema migrations.

```sh
$ php bin/console doctrine:migrations:migrate
```

You can check the status of the migrations.

```sh
$ php bin/console doctrine:migrations:status
```

### Sending emails

You must also [define an email transport](https://symfony.com/doc/current/mailer.html#transport-setup)
by defining `MAILER_DSN` in `.env.local`

### Start the server

Build javascript and CSS once.

```sh
$ yarn dev
```

If you plan to edit some style and/or javascript you can start the previous
build in watch mode in its own terminal.

```sh
$ yarn watch
```


Then start the PHP server with `symfony-cli`

```sh
$ symfony server:start
```

or the alias

```sh
$ yarn start
```

You should see the app live at <http://localhost:8000/>

