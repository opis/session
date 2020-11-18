Opis Session
============
[![Tests](https://github.com/opis/session/workflows/Tests/badge.svg)](https://github.com/opis/session/actions)
[![Latest Stable Version](https://poser.pugx.org/opis/session/version.png)](https://packagist.org/packages/opis/session)
[![Latest Unstable Version](https://poser.pugx.org/opis/session/v/unstable.png)](https://packagist.org/packages/opis/session)
[![License](https://poser.pugx.org/opis/session/license.png)](https://packagist.org/packages/opis/session)

Session manager
--------------
**Opis Session** is a session manager library with support for multiple backend stores, providing
developers with an API which allows them to handle session related information in a standardised way.

The currently available handlers are: File and native store. 

## Documentation

The full documentation for this library can be found [here][documentation].

## License

**Opis Session** is licensed under the [Apache License, Version 2.0][license].

## Requirements

* PHP ^7.4|^8.0

## Installation

**Opis Session** is available on [Packagist] and it can be installed from a 
command line interface by using [Composer]. 

```bash
composer require opis/session
```

Or you could directly reference it into your `composer.json` file as a dependency

```json
{
    "require": {
        "opis/session": "^2020"
    }
}
```

[documentation]: https://www.opis.io/session
[license]: https://www.apache.org/licenses/LICENSE-2.0 "Apache License"
[Packagist]: https://packagist.org/packages/opis/session "Packagist"
[Composer]: https://getcomposer.org "Composer"