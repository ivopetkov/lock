<?php

/*
 * Lock
 * https://github.com/ivopetkov/lock
 * Copyright (c) Ivo Petkov
 * Free to use under the MIT license.
 */

class LocksTestCase extends PHPUnit_Framework_TestCase
{

    function setUp()
    {
        require __DIR__ . '/../vendor/autoload.php';
        $dir = sys_get_temp_dir() . '/lock-unit-tests/' . uniqid() . '/';
        mkdir($dir, 0777, true);
        IvoPetkov\Lock::setLocksDir($dir);
    }

}

class LocksAutoloaderTestCase extends PHPUnit_Framework_TestCase
{

    function setUp()
    {
        require __DIR__ . '/../autoload.php';
    }

}
