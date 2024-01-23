# Lock

A simple locking library
... that is based on file locking (and that's awesome).

[![Latest Stable Version](https://poser.pugx.org/ivopetkov/lock/v/stable)](https://packagist.org/packages/ivopetkov/lock)
[![License](https://poser.pugx.org/ivopetkov/lock/license)](https://packagist.org/packages/ivopetkov/lock)

## Why you need it

Imagine you have two tasks that you do not want to overlap (they modify the same data for example). Using this library will bring you peace of mind.

## Keep in mind

The library uses file locking mechanism to ensure it works correctly. This means that it can be used only on applications/instances/processes that share a common file system. A website on one server does just that.

## Install via Composer

```shell
composer require ivopetkov/lock
```

## Usage

Using the following two methods (acquire() and release()) will ensure no two applications/instances/processes execute the code between them.
```php
<?php
require 'vendor/autoload.php';
use IvoPetkov\Lock;

Lock::acquire('lock1'); // Acquires a lock and pauses other applications/instances/processes until the lock is released.
// Do something awesome
Lock::release('lock1'); // Releases the acquired lock.
```

## Documentation

Full [documentation](https://github.com/ivopetkov/lock/blob/master/docs/markdown/index.md) is available as part of this repository.

## Configuration

The default timeout to acquire a lock is 1.5 seconds. You can modify it by calling the following method:

```php
Lock::setDefaultLockTimeout(2.5);
```

The temporary lock files needed by the library are stored in your OS temp dir. You can modify it by calling the following method:

```php
Lock::setLocksDir('/some/other/temp/dir/');
```

If multiple applications use this library, you can call the following methods to prefix the keys provided. This way the different applications can use the same keys without interference.

```php
Lock::setKeyPrefix('app1');
```

## License
This project is licensed under the MIT License. See the [license file](https://github.com/ivopetkov/lock/blob/master/LICENSE) for more information.

## Contributing
Feel free to open new issues and contribute to the project. Let's make it awesome and let's do in a positive way.

## Author
This library is created and maintained by [Ivo Petkov](https://github.com/ivopetkov/) ([ivopetkov.com](https://ivopetkov.com)).
