# IvoPetkov\Lock
## Methods

```php
static public string getLocksDir ( void )
```

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

```php
static public void setLocksDir ( string $dir )
```

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$dir`

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;No value is returned.

```php
static public string getKeyPrefix ( void )
```

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

```php
static public void setKeyPrefix ( string $prefix )
```

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$prefix`

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;No value is returned.

```php
static public string getDefaultLockTimeout ( void )
```

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

```php
static public void setDefaultLockTimeout ( $seconds )
```

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$seconds`

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;No value is returned.

```php
static public void acquire ( mixed $key [, array $options = [] ] )
```

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$key`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$options`

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;No value is returned.

```php
static public boolean exists ( mixed $key )
```

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$key`

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

```php
static public void release ( mixed $key )
```

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$key`

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;No value is returned.

