Opis Session
============
[![Latest Stable Version](https://poser.pugx.org/opis/session/version.png)](https://packagist.org/packages/opis/session)
[![Latest Unstable Version](https://poser.pugx.org/opis/session/v/unstable.png)](//packagist.org/packages/opis/session)
[![License](https://poser.pugx.org/opis/session/license.png)](https://packagist.org/packages/opis/session)

Session library
--------------


### Installation

This library is available on [Packagist](https://packagist.org/packages/opis/session) and can be installed using [Composer](http://getcomposer.org)

```json
{
    "require": {
        "opis/session": "2.2.*"
    }
}
```

### Examples

```php
use Opis\Session\Session;

$session = new Session();

//Write to session
$session->set('email', 'email@example.com');

$session->set('primes', array(11, 19, 23));

//Read from session
$user = $session->get('user');

//..specify a default value that will be returned if the specified key was not set.

$user = $session->get('user', 'anonymous');

/**
 * Use the load method to read values from session. If the specified key was not set,
 * it will be initialized with the value returned by the anonymous function callback.
 */

$number = $session->load('number', function($key){
    return 3.14159265359;
});

//Deletes a key from session
$session->delete('email');

//Delete all keys
$session->clear();

//Destroy a session
$session->destroy();
```