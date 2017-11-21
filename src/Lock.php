<?php

/*
 * Lock
 * https://github.com/ivopetkov/lock
 * Copyright 2017, Ivo Petkov
 * Free to use under the MIT license.
 */

namespace IvoPetkov;

class Lock
{

    static private $data = [];
    static private $dir = null;
    static private $keyPrefix = null;
    static private $defaultLockTimeout = 1.5;

    /**
     * 
     * @return string
     */
    static function getLocksDir()
    {
        return self::$dir === null ? sys_get_temp_dir() . '/ivopetkovlocks/' : self::$dir;
    }

    /**
     * 
     * @param string $dir
     */
    static function setLocksDir($dir)
    {
        self::$dir = rtrim($dir, '\//') . '/';
    }

    /**
     * 
     * @return string
     */
    static function getKeyPrefix()
    {
        return self::$keyPrefix;
    }

    /**
     * 
     * @param string $prefix
     */
    static function setKeyPrefix($prefix)
    {
        self::$keyPrefix = $prefix;
    }

    /**
     * 
     * @return string
     */
    static function getDefaultLockTimeout()
    {
        return self::$defaultLockTimeout;
    }

    /**
     * 
     * @param string $prefix
     */
    static function setDefaultLockTimeout($seconds)
    {
        self::$defaultLockTimeout = $seconds;
    }

    /**
     * 
     * @param mixed $key
     * @param array $options
     * @throws \Exception
     */
    static public function acquire($key, $options = [])
    {
        $keyMD5 = md5(self::$keyPrefix . serialize($key));
        $timeout = isset($options['timeout']) ? (float) $options['timeout'] : self::$defaultLockTimeout;
        $retryInterval = 0.5;
        $maxRetriesCount = floor($timeout / $retryInterval);
        $lock = function() use ($keyMD5) {
            if (!isset(self::$data[$keyMD5])) {
                set_error_handler(function($errno, $errstr) {
                    throw new \Exception($errstr);
                });
                $dir = self::getLocksDir();
                if (!is_dir($dir)) {
                    try {
                        mkdir($dir, 0777, true);
                    } catch (\Exception $e) {
                        if ($e->getMessage() !== 'mkdir(): File exists') { // The directory may be just created in other process.
                            restore_error_handler();
                            return false;
                        }
                    }
                }
                $filename = $dir . $keyMD5 . '.lock';
                try {
                    $filePointer = fopen($filename, "w");
                    if ($filePointer !== false && flock($filePointer, LOCK_EX | LOCK_NB) !== false) {
                        self::$data[$keyMD5] = $filePointer;
                        restore_error_handler();
                        return true;
                    }
                } catch (\Exception $e) {
                    
                }
                restore_error_handler();
                return false;
            }
            return false;
        };
        $startTime = microtime(true);
        for ($i = 0; $i < $maxRetriesCount + 1; $i++) {
            if ($lock()) {
                return;
            }
            if (microtime(true) - $startTime > $timeout) {
                break;
            }
            usleep($retryInterval * 1000000);
        }
        throw new \Exception('Cannot acquire lock for "' . $key . '"');
    }

    /**
     * 
     * @param mixed $key
     * @throws \Exception
     * @return boolean
     */
    static public function exists($key)
    {
        $keyMD5 = md5(self::$keyPrefix . serialize($key));
        $dir = self::getLocksDir();
        if (!is_dir($dir)) {
            return false;
        }
        $filename = $dir . $keyMD5 . '.lock';
        set_error_handler(function($errno, $errstr) {
            throw new \Exception($errstr);
        });
        try {
            $filePointer = fopen($filename, "w");
            if ($filePointer !== false) {
                $wouldBlock = null;
                if (flock($filePointer, LOCK_EX | LOCK_NB, $wouldBlock)) {
                    restore_error_handler();
                    return false;
                } else {
                    restore_error_handler();
                    return $wouldBlock === 1;
                }
            }
        } catch (\Exception $e) {
            
        }
        restore_error_handler();
        throw new \Exception('Cannot check if lock named "' . $key . '" exists.');
    }

    /**
     * 
     * @param mixed $key
     * @throws \Exception
     */
    static public function release($key)
    {
        $keyMD5 = md5(self::$keyPrefix . serialize($key));
        if (!isset(self::$data[$keyMD5])) {
            throw new \Exception('A lock name "' . $key . '" does not exists in current process!');
        }
        $dir = self::getLocksDir();
        if (!is_dir($dir)) {
            return;
        }
        set_error_handler(function($errno, $errstr) {
            throw new \Exception($errstr);
        });
        try {
            if (flock(self::$data[$keyMD5], LOCK_UN) && fclose(self::$data[$keyMD5])) {
                try {
                    $filename = $dir . $keyMD5 . '.lock';
                    $tempFilename = $filename . '.' . md5(uniqid() . rand(0, 999999));
                    $renameResult = rename($filename, $tempFilename);
                    if ($renameResult) {
                        unlink($tempFilename);
                    }
                } catch (\Exception $e) {
                    // Don't care whether the rename is successful
                }
                unset(self::$data[$keyMD5]);
                restore_error_handler();
                return;
            }
        } catch (\Exception $e) {
            restore_error_handler();
            throw new \Exception('Cannot release the lock named "' . $key . '". Reason: ' . $e->getMessage());
        }
        restore_error_handler();
        throw new \Exception('Cannot release the lock named "' . $key . '"');
    }

}
