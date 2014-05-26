CHANGELOG
-------------

### Opis Session 2.0.0, 2014.05.27

* Started changelog
* Removed `Opis\Session\SessionStorage` class
* Removed `Opis\Session\SessionInterface` interface
* Removed `Opis\Session\Storage\Native` class
* Modified `Opis\Session\Session` constructor.
    You can now set various options like session name, cookie domain, path or cookie lifetime
* Added a destructor method to Opis\Session\Session class
* Added new method `set` to `Opis\Session\Session`
* Added new method `load` to `Opis\Session\Session`
* Added new method `delete` to `Opis\Session\Session`
* Deprecated `remember` method in `Opis\Session\Session`
* Deprecated `forget` method in `Opis\Session\Session`
* Deprecated `dispose` method in `Opis\Session\Session`
* Added new method `load` to `Opis\Session\Flash`