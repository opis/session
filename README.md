Opis Session
============
[![Latest Stable Version](https://poser.pugx.org/opis/session/version.png)](https://packagist.org/packages/opis/session)
[![Latest Unstable Version](https://poser.pugx.org/opis/session/v/unstable.png)](//packagist.org/packages/opis/session)
[![License](https://poser.pugx.org/opis/session/license.png)](https://packagist.org/packages/opis/session)

Session manager
--------------
**Opis Session** is a session manager library with support for multiple backend storages that provides
developers with an API which allows them to handle session related informations in a standardised way.

The currently supported storages are: Database, File, Mongo and Redis. 

### License

**Opis Session** is licensed under the [Apache License, Version 2.0](http://www.apache.org/licenses/LICENSE-2.0). 

### Requirements

* PHP 5.3.* or higher
* [Opis Database](http://www.opis.io/database) 2.0.* (for Database storage)
* [Predis](https://github.com/nrk/predis) 1.0.* (for Redis storage)

### Installation

This library is available on [Packagist](https://packagist.org/packages/opis/session) and can be installed using [Composer](http://getcomposer.org).

```json
{
    "require": {
        "opis/session": "2.3.*"
    }
}
```

If you are unable to use [Composer](http://getcomposer.org) you can download the
[tar.gz](https://github.com/opis/session/archive/2.3.0.tar.gz) or the [zip](https://github.com/opis/session/archive/2.3.0.zip)
archive file, extract the content of the archive and include de `autoload.php` file into your project. 

```php

require_once 'path/to/session-2.3.0/autoload.php';

```

### Documentation

Examples and documentation can be found at http://opis.io/session .