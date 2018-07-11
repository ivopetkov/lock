<?php

/*
 * Lock
 * https://github.com/ivopetkov/lock
 * Copyright (c) Ivo Petkov
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
    public function testAcquire()
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
        Lock::acquire('test2');
        Lock::release('test2');
        Lock::acquire('test2');
        Lock::release('test2');
        $this->setExpectedException('\Exception');
        Lock::release('test2');
    }

    /**
     * 
     */
    public function testKeyPrefix()
    {
        Lock::setKeyPrefix('prefix1');
        $this->assertTrue(Lock::getKeyPrefix() === 'prefix1');
        $this->assertTrue(Lock::exists('test1') === false);
        Lock::acquire('test1');
        $this->assertTrue(Lock::exists('test1') === true);
        Lock::release('test1');
        $this->assertTrue(Lock::exists('test1') === false);
    }

    /**
     * 
     */
    public function testExists()
    {
        $this->assertTrue(Lock::exists('test3') === false);
        Lock::acquire('test3');
        $this->assertTrue(Lock::exists('test3') === true);
        Lock::release('test3');
        $this->assertTrue(Lock::exists('test3') === false);
    }

    /**
     * 
     */
    public function testTimeout()
    {
        Lock::setDefaultLockTimeout(2.5);
        $this->assertTrue(Lock::getDefaultLockTimeout() === 2.5);
        Lock::acquire('test2');
        try {
            $time = microtime(true);
            Lock::acquire('test2');
            throw new Exception('Should not get here');
        } catch (Exception $e) {
            $time = microtime(true) - $time;
            $this->assertTrue($e->getMessage() === 'Cannot acquire lock for "test2"');
            $this->assertTrue($time > 2.5);
            $this->assertTrue($time < 3);
        }
    }

    /**
     * 
     */
    public function testFailAcquire()
    {
        $dir = Lock::getLocksDir();
        $filename = $dir . md5(serialize('test4')) . '.lock';
        mkdir($filename, 0777, true);
        try {
            Lock::acquire('test4');
            throw new Exception('Should not get here');
        } catch (Exception $e) {
            $this->assertTrue($e->getMessage() === 'Cannot acquire lock for "test4"');
        }
    }

    /**
     * 
     */
    public function testFailExists()
    {
        $dir = Lock::getLocksDir();
        $filename = $dir . md5(serialize('test5')) . '.lock';
        mkdir($filename, 0777, true);
        try {
            Lock::exists('test5');
            throw new Exception('Should not get here');
        } catch (Exception $e) {
            $this->assertTrue($e->getMessage() === 'Cannot check if lock named "test5" exists.');
        }
    }

}
