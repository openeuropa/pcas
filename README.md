[![Build Status](https://www.travis-ci.org/openeuropa/pcas.svg?branch=master)](https://www.travis-ci.org/openeuropa/pcas)

# pCAS library

pCAS, a CAS library for PHP.

## Installation

```bash
composer require openeuropa/pcas
```

## Demo

The pCAS library is bundled with two independent Symfony app:

* demo-client: A basic web app that can authenticate to a CAS server to access specific pages.
* demo-server: A basic CAS server.

In order to test pCAS library, you must run the server:

```
cd demo-server
composer install
php bin/console server:run 127.0.1.1:8001
```

Then run the web app:

```
cd demo-client
composer install
php bin/console server:run 127.0.0.1:8000
```

Then go on [http://127.0.0.1:8000](http://127.0.0.1:8000).

## Run the tests

```bash
composer install
./vendor/bin/grumphp run
```
