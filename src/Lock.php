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
     * @return string
     */
    static function setLocksDir($dir)
    {
        return self::$dir = rtrim($dir, '\//') . '/';
    }

    /**
     * 
     * @param string $prefix
     * @return string
     */
    static function setKeyPrefix($prefix)
    {
        return self::$keyPrefix = $prefix;
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
        $timeout = isset($options['timeout']) ? (float) $options['timeout'] : 1.5;
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
                    } catch (\Throwable $e) {
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
                } catch (\Throwable $e) {
                    
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
        $filename = self::getLocksDir() . $keyMD5 . '.lock';
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
        } catch (\Throwable $e) {
            
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
            return;
        }
        set_error_handler(function($errno, $errstr) {
            throw new \Exception($errstr);
        });
        try {
            if (flock(self::$data[$keyMD5], LOCK_UN) && fclose(self::$data[$keyMD5])) {
                $filename = self::getLocksDir() . $keyMD5 . '.lock';
                $tempFilename = $filename . '.' . md5(uniqid() . rand(0, 999999));
                $renameResult = rename($filename, $tempFilename);
                if ($renameResult) {
                    unlink($tempFilename);
                }
                unset(self::$data[$keyMD5]);
                restore_error_handler();
                return;
            }
        } catch (\Throwable $e) {
            restore_error_handler();
            throw new \Exception('Cannot release the lock named "' . $key . '". Reason: ' . $e->getMessage());
        }
        restore_error_handler();
        throw new \Exception('Cannot release the lock named "' . $key . '"');
    }

}
