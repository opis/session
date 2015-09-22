Opis Session
============
[![Latest Stable Version](https://poser.pugx.org/opis/session/version.png)](https://packagist.org/packages/opis/session)
[![Latest Unstable Version](https://poser.pugx.org/opis/session/v/unstable.png)](//packagist.org/packages/opis/session)
[![License](https://poser.pugx.org/opis/session/license.png)](https://packagist.org/packages/opis/session)

Session manager
--------------
**Opis Session** is a session manager library with support for multiple backend storages that provides
developers with an API which allows them to handle session related informations in a standardised way.

The currently supported storages are: File, Mongo and the native storage. 

##### Important!

You can install additional storage adapters, for SQL databases and Redis, by using the optional [Opis Storages](https://github.com/opis/storages) package.

### License

**Opis Session** is licensed under the [Apache License, Version 2.0](http://www.apache.org/licenses/LICENSE-2.0). 

### Requirements

* PHP 5.3.* or higher

### Installation

This library is available on [Packagist](https://packagist.org/packages/opis/session) and can be installed using [Composer](http://getcomposer.org).

```json
{
    "require": {
        "opis/session": "^3.1.0"
    }
}
```

If you are unable to use [Composer](http://getcomposer.org) you can download the
[tar.gz](https://github.com/opis/session/archive/3.0.0.tar.gz) or the [zip](https://github.com/opis/session/archive/3.1.0.zip)
archive file, extract the content of the archive and include de `autoload.php` file into your project. 

```php

require_once 'path/to/session-3.1.0/autoload.php';

```

### Documentation

Examples and documentation can be found at http://opis.io/session .