# pCAS library
[![Build Status](https://drone.fpfis.eu/api/badges/openeuropa/pcas/status.svg)](https://drone.fpfis.eu/openeuropa/pcas/)

pCAS, a CAS library for PHP.

## Installation

```bash
composer require openeuropa/pcas
```

## Usage

In order to use the pCAS library you need a session object that implements
[Symfony's `SessionInterface`](http://symfony.com/doc/current/components/http_foundation/sessions.html).

You can pass that object to the pCAS factory class as shown below:

```php
<?php

use OpenEuropa\pcas\PCasFactory;
use Symfony\Component\HttpFoundation\Session\Session;

$factory = new PCasFactory(new Session());
```

You can access a fully functional pCAS library as shown below:

```php
<?php

$pcas = $factory->getPCas();
```

The pCAS factory object also accepts the following configuration parameters:

| Parameter  | Description |
|------------|-------------|
| `base_url` | The CAS service base URL. It defaults to `http://127.0.0.1:8000` |
| `protocol` | The CAS protocol specification. It defaults to [this list](./Resources/config/p_cas.yml) |

You can set those parameters as shown below:

```php
<?php

use OpenEuropa\pcas\PCasFactory;
use Symfony\Component\HttpFoundation\Session\Session;

$factory = new PCasFactory(
    new Session(), 
    'http://my-cas-server.com', // This is your custom base_url parameter.
    [...] // This is your custom protocol parameter.
);
$pcas = $factory->getPCas();
```

Both parameters are optional, although you'll surely want to set `base_url` to a different value.

## Demo

Copy docker-compose.yml.dist into docker-compose.yml.

You can make any alterations you need for your local Docker setup. However, the defaults should be enough to set the project up.

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
