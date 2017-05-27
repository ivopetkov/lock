<?php

/*
 * Lock
 * https://github.com/ivopetkov/lock
 * Copyright 2017, Ivo Petkov
 * Free to use under the MIT license.
 */

use IvoPetkov\Lock;

/**
 * @runTestsInSeparateProcesses
 */
class LocksTest extends LocksTestCase
{

    /**
     * 
     */
    public function testacquire()
    {
        Lock::acquire('test1');
        Lock::release('test1');
        Lock::acquire('test1');
        Lock::release('test1');
        Lock::acquire('test1');
        $this->setExpectedException('\Exception');
        Lock::acquire('test1');
    }

    /**
     * 
     */
    public function testRelease()
    {
        Lock::acquire('test1');
        Lock::release('test1');
        Lock::acquire('test1');
        Lock::release('test1');
        $this->setExpectedException('\Exception');
        Lock::release('test1');
    }

    /**
     * 
     */
    public function testExists()
    {
        $this->assertTrue(Lock::exists('test1') === false);
        Lock::acquire('test1');
        $this->assertTrue(Lock::exists('test1') === true);
        Lock::release('test1');
        $this->assertTrue(Lock::exists('test1') === false);
    }

}
