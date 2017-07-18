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

    const RETRIES_COUNT = 3;
    const RETRY_DELAY_IN_MICROSECONDS = 500000;

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
     * @throws \Exception
     */
    static public function acquire($key)
    {
        $keyMD5 = md5(serialize($key));
        if (isset(self::$data[$keyMD5])) {
            throw new \Exception('A lock called ' . $key . ' is already acquired!');
        }
        $lock = function() use ($keyMD5) {
            if (!isset(self::$data[$keyMD5])) {
                $filename = self::getLocksDir() . $keyMD5 . '.lock';
                $dir = pathinfo($filename, PATHINFO_DIRNAME);
                if (!is_dir($dir)) {
                    try {
                        mkdir($dir, 0777, true);
                    } catch (\Exception $e) {
                        if ($e->getMessage() !== 'mkdir(): File exists') { // The directory may be just created in other process.
                            throw $e;
                        }
                    }
                }
                try {
                    $filePointer = fopen($filename, "w");
                } catch (Exception $e) {
                    $filePointer = false;
                }
                if ($filePointer === false) {
                    return false;
                }
                try {
                    $flockResult = flock($filePointer, LOCK_EX | LOCK_NB);
                } catch (Exception $e) {
                    $flockResult = false;
                }
                if ($flockResult === false) {
                    return false;
                }
                self::$data[$keyMD5] = $filePointer;
                return true;
            }
            return false;
        };

        $isOk = false;
        for ($i = 0; $i < self::RETRIES_COUNT; $i++) {
            if ($lock()) {
                $isOk = true;
                break;
            }
            if ($i < self::RETRIES_COUNT - 1) {
                usleep(self::RETRY_DELAY_IN_MICROSECONDS);
            }
        }

        if (!$isOk) {
            throw new \Exception('Cannot acquire lock for ' . $key);
        }
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
        if (isset(self::$data[$keyMD5])) {
            return true;
        }

        $exists = function() use ($keyMD5) {
            $filename = self::getLocksDir() . $keyMD5 . '.lock';
            if (!is_file($filename)) {
                return false;
            }
            try {
                $filePointer = fopen($filename, "w");
            } catch (Exception $e) {
                $filePointer = false;
            }
            if ($filePointer === false) {
                return null;
            }
            try {
                $wouldBlock = null;
                $flockResult = flock($filePointer, LOCK_EX | LOCK_NB, $wouldBlock);
            } catch (Exception $e) {
                $flockResult = null;
            }
            if ($flockResult === true) {
                return false;
            } elseif ($flockResult === false) {
                if ($wouldBlock === 1) {
                    return true;
                } else {
                    return false;
                }
            }
            return null;
        };

        for ($i = 0; $i < self::RETRIES_COUNT; $i++) {
            $existsResult = $exists();
            if ($existsResult === true) {
                return true;
            } elseif ($existsResult === false) {
                return false;
            }
            if ($i < self::RETRIES_COUNT - 1) {
                usleep(self::RETRY_DELAY_IN_MICROSECONDS);
            }
        }

        throw new \Exception('Cannot check if lock exists (' . $key . ')');
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
            throw new \Exception('A lock called ' . $key . ' does not exists!');
        }
        $unlock = function() use ($keyMD5) {
            try {
                flock(self::$data[$keyMD5], LOCK_UN);
                $fcloseResult = fclose(self::$data[$keyMD5]);
                $filename = self::getLocksDir() . $keyMD5 . '.lock';
                $tempFilename = $filename . '.' . md5(uniqid() . rand(0, 999999));
                $renameResult = @rename($filename, $tempFilename);
                if ($renameResult) {
                    unlink($tempFilename);
                }
            } catch (Exception $e) {
                
            }
            unset(self::$data[$keyMD5]);
            return $fcloseResult;
        };

        $isOk = false;
        for ($i = 0; $i < self::RETRIES_COUNT; $i++) {
            if ($unlock()) {
                $isOk = true;
                break;
            }
            if ($i < self::RETRIES_COUNT - 1) {
                usleep(self::RETRY_DELAY_IN_MICROSECONDS);
            }
        }
        if (!$isOk) {
            throw new \Exception('Cannot release lock for ' . $key);
        }
    }

}
