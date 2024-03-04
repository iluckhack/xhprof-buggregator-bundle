# Xhprof support in buggregator for Symfony Framework

Welcome to the Xhprof integration package for [buggregator](https://buggregator.dev/) in Symfony Framework.
This repository allows you to effortlessly enable Xhprof support for buggregator in your Symfony application.

## Installation

To get started, install the package via composer:

```bash
composer require --dev iluckhack/xhprof-buggregator-bundle
```

Your bundle should be automatically enabled by Flex. But it useful for dev environment only, so you need to change it in config/bundles.php:
```php
<?php
// config/bundles.php

return [
    // ...
    Iluckhack\XhprofBuggregatorBundle\XhprofBuggregatorBundle::class => ['dev' => true],
    // ...
];
```

## Usage

You can configure parameters via environment variables (if `.env` file for example):

```dotenv
# ...
# Your application name, it uses for providing to SpiralPackages\Profiler\Profiler
XHPROF_BUGGREGATOR_APP_NAME="My awesome app"
# Buggregator endpoint, it http://127.0.0.1:8000/api/profiler/store by default
XHPROF_BUGGREGATOR_ENDPOINT=http://127.0.0.1:8123/api/profiler/store
# If profiling enabled for CLI commands or HTTP requests respectively
XHPROF_BUGGREGATOR_CLI_ENABLED=false
XHPROF_BUGGREGATOR_HTTP_ENABLED=true
# Custom header in your request to explicitly enable or disable profiling for that specific call
# When this header is configured and present, it takes precedence over the "XHPROF_BUGGREGATOR_HTTP_ENABLED" variable
XHPROF_BUGGREGATOR_HTTP_ENABLED_HEADER=X-Xhprof-Enabled
# ...
```

When `XHPROF_BUGGREGATOR_HTTP_ENABLED_HEADER` defined and header presents enabled values are `true`/`1`/`on`/`yes`, otherwise profiling will be disabled.

Values by default:

```dotenv
XHPROF_BUGGREGATOR_APP_NAME="Symfony Application"
XHPROF_BUGGREGATOR_ENDPOINT=http://127.0.0.1:8000/api/profiler/store
XHPROF_BUGGREGATOR_CLI_ENABLED=false
XHPROF_BUGGREGATOR_HTTP_ENABLED=false
XHPROF_BUGGREGATOR_HTTP_ENABLED_HEADER=
```

## Testing

Run tests via composer:

```bash
composer tests
```

Or run it directly

```bash
./vendor/bin/simple-phpunit
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.