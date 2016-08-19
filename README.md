Opis Session
============
[![Latest Stable Version](https://poser.pugx.org/opis/session/version.png)](https://packagist.org/packages/opis/session)
[![Latest Unstable Version](https://poser.pugx.org/opis/session/v/unstable.png)](//packagist.org/packages/opis/session)
[![License](https://poser.pugx.org/opis/session/license.png)](https://packagist.org/packages/opis/session)

Session manager
--------------
**Opis Session** is a session manager library with support for multiple backend stores, providing
developers with an API which allows them to handle session related information in a standardised way.

The currently available handlers are: File and native store. 

### License

**Opis Session** is licensed under the [Apache License, Version 2.0](http://www.apache.org/licenses/LICENSE-2.0). 

### Requirements

* PHP 7.0.* or higher

### Installation

This library is available on [Packagist](https://packagist.org/packages/opis/session) and can be installed using [Composer](http://getcomposer.org).

```json
{
    "require": {
        "opis/session": "^4.0.*@dev"
    }
}
```

If you are unable to use [Composer](http://getcomposer.org) you can download the
[tar.gz](https://github.com/opis/session/archive/master.tar.gz) or the [zip](https://github.com/opis/session/archive/master.zip)
archive file, extract the content of the archive and include de `autoload.php` file into your project. 

```php

require_once 'path/to/master/autoload.php';

```

### Documentation

Examples and documentation(outdated) can be found [here](http://opis.io/session).