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
     * @param mixed $key
     * @param array $options
     * @throws \Exception
     */
    static public function acquire($key, $options = [])
    {
        $keyMD5 = md5(serialize($key));
        $timeout = isset($options['timeout']) ? (float) $options['timeout'] : 1.5;
        $retryInterval = 0.5;
        $maxRetriesCount = floor($timeout / $retryInterval);
        $lock = function() use ($keyMD5) {
            if (!isset(self::$data[$keyMD5])) {
                $dir = self::getLocksDir();
                if (!is_dir($dir)) {
                    try {
                        mkdir($dir, 0777, true);
                    } catch (\Throwable $e) {
                        if ($e->getMessage() !== 'mkdir(): File exists') { // The directory may be just created in other process.
                            throw $e;
                        }
                    }
                }
                $filename = $dir . $keyMD5 . '.lock';
                try {
                    $filePointer = @fopen($filename, "w");
                    if ($filePointer === false) {
                        return false;
                    }
                    if (@flock($filePointer, LOCK_EX | LOCK_NB) === false) {
                        return false;
                    }
                } catch (\Throwable $e) {
                    return false;
                }
                self::$data[$keyMD5] = $filePointer;
                return true;
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
        $keyMD5 = md5(serialize($key));
        $filename = self::getLocksDir() . $keyMD5 . '.lock';
        try {
            $filePointer = @fopen($filename, "w");
            if ($filePointer !== false) {
                $wouldBlock = null;
                if (@flock($filePointer, LOCK_EX | LOCK_NB, $wouldBlock)) {
                    return false;
                } else {
                    return $wouldBlock === 1;
                }
            }
        } catch (\Throwable $e) {
            
        }
        throw new \Exception('Cannot check if lock named "' . $key . '" exists.');
    }

    /**
     * 
     * @param mixed $key
     * @throws \Exception
     */
    static public function release($key)
    {
        $keyMD5 = md5(serialize($key));
        if (!isset(self::$data[$keyMD5])) {
            throw new \Exception('A lock name "' . $key . '" does not exists in current process!');
            return;
        }
        try {
            if (@flock(self::$data[$keyMD5], LOCK_UN) && @fclose(self::$data[$keyMD5])) {
                $filename = self::getLocksDir() . $keyMD5 . '.lock';
                $tempFilename = $filename . '.' . md5(uniqid() . rand(0, 999999));
                $renameResult = @rename($filename, $tempFilename);
                if ($renameResult) {
                    @unlink($tempFilename);
                }
                unset(self::$data[$keyMD5]);
                return;
            }
        } catch (\Throwable $e) {
            throw new \Exception('Cannot release the lock named "' . $key . '". Reason: ' . $e->getMessage());
        }
        throw new \Exception('Cannot release the lock named "' . $key . '"');
    }

}
