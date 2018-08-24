# IvoPetkov\Lock

A class that provides locking functionality.

## Methods

##### static public void [acquire](ivopetkov.lock.acquire.method.md) ( mixed $key [, array $options = [] ] )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Acquires a new lock for the key specified.

##### static public boolean [exists](ivopetkov.lock.exists.method.md) ( mixed $key )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Checks if a lock exists.

##### static public float [getDefaultLockTimeout](ivopetkov.lock.getdefaultlocktimeout.method.md) ( void )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns the default lock timeout.

##### static public string [getKeyPrefix](ivopetkov.lock.getkeyprefix.method.md) ( void )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns the current key prefix.

##### static public string [getLocksDir](ivopetkov.lock.getlocksdir.method.md) ( void )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns the current locks dir.

##### static public void [release](ivopetkov.lock.release.method.md) ( mixed $key )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Releases a lock.

##### static public void [setDefaultLockTimeout](ivopetkov.lock.setdefaultlocktimeout.method.md) ( float $seconds )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sets a new default lock timeout.

##### static public void [setKeyPrefix](ivopetkov.lock.setkeyprefix.method.md) ( string $prefix )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sets a new key prefix.

##### static public void [setLocksDir](ivopetkov.lock.setlocksdir.method.md) ( string $dir )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sets a new locks dir.

## Details

File: /src/Lock.php

---

[back to index](index.md)

