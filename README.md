# pCAS library
[![Build Status](https://drone.fpfis.eu/api/badges/openeuropa/pcas/status.svg)](https://drone.fpfis.eu/openeuropa/pcas/)

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
php bin/console server:run 127.0.0.1:8001
```

Then run the web app:

```
cd demo-client
composer install
php bin/console server:run 127.0.0.1:8000
```

Then go on [http://127.0.0.1:8000](http://127.0.0.1:8000).

You can also test it against ECAS, the authentication service from European Commission by updating the file ```.env``` in
```demo-client``` directory. If this file doesn't exists, you can create it from ```.env.dist```.

The file must contains:

```
APP_ENV=ec
```

## Run the tests

```bash
composer install
./vendor/bin/grumphp run
```
