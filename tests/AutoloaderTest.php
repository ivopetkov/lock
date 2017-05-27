<?php

/*
 * Lock
 * https://github.com/ivopetkov/lock
 * Copyright 2017, Ivo Petkov
 * Free to use under the MIT license.
 */

/**
 * @runTestsInSeparateProcesses
 */
class AutoloaderTest extends LocksAutoloaderTestCase
{

    /**
     * 
     */
    public function testClasses()
    {
        $this->assertTrue(class_exists('IvoPetkov\Lock'));
    }

}
