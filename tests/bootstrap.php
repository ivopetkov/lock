<?php

/*
 * Lock
 * https://github.com/ivopetkov/lock
 * Copyright 2017, Ivo Petkov
 * Free to use under the MIT license.
 */

class LocksTestCase extends PHPUnit_Framework_TestCase
{

    function setUp()
    {
        require __DIR__ . '/../vendor/autoload.php';
    }

}

class LocksAutoloaderTestCase extends PHPUnit_Framework_TestCase
{

    function setUp()
    {
        require __DIR__ . '/../autoload.php';
    }

}
