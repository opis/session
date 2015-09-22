CHANGELOG
-------------
### v3.1.0, 2015.09.22

* `Opis\Session\Storage\File` was modified and now throws an exception if you don't
have write permissions or if the specified path does not exist and it can't be created.
See [issue #7](https://github.com/opis/session/issues/7)

### v3.0.0, 2015.08.31

* `Opis\Session\Storage\Database` and `Opis\Session\Storage\Redis` classes were moved into
the `opis/storages` package
* Removed `opis\database` and `predis/predis` dependencies

### v2.4.0, 2015.07.31

* Removed some properties form `composer.json` file
* Updated `opis/database` library dependency to version `^2.1.1`

### v2.3.0, 2014.11.23

* Added an autoload file.
* Changed how the `regenerate` method in `Opis\Session\Session` works. If the argument passed
to the method is `true` the the old data are kept, otherwise the old data are deleted.
* Updated `predis/predis` library dependency to version `1.0.*`

### v2.2.1, 2014.11.16

* Fixed a bug in `Opis\Session\Storage\Redis`

### v2.2.0, 2014.10.23

* Updated `opis/database` library dependency to version `2.0.*`

### v2.1.0, 2014.06.26

* Updated `opis/database` library dependency to version `1.3.*`

### v2.0.0, 2014.05.27

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