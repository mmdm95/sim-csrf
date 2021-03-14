# Simplicity CSRF
A library for CSRF token management.

## Install
**composer**
```php 
composer require mmdm/sim-csrf
```

Or you can simply download zip file from github and extract it, 
then put file to your project library and use it like other libraries.

Just add line below to autoload files:

```php
require_once 'path_to_library/autoloader.php';
```

and you are good to go.

## How to use
```php
// to instance a csrf object
$csrf = new Csrf();
// then use csrf mothods like
$field = $csrf->getField();
// the output will be
// <input type="hidden" name="csrftoken" value="generated token">
```

#### `__construct(ICsrfStorage $storage = null)`

You can pass custom storage if you want. Just implement `ICsrfStorage` 
class and pass it through constructor and you are good to go.

Go to [Custom Csrf Storage][1] section for more information about 
how to create custom storage.

## Available functions

- setStorage(ICsrfStorage $storage): ICsrf

You can set storage even after construction with this method.

- getStorage(): ICsrfStorage

Get csrf storage.

- setExpiration(int $timeout): ICsrf

This method set expiration from now to a csrf token. Default 
expiration is 300 seconds.

```php
// to set token expiration
$csrf->setExpiration(10);
// token is valid just for 10 seconds from now
// after 10 seconds it'll be generate again
```

Note: If you plan to get token continuously if a code snipped then 
you should specify expiration each time before getting field or 
token.

exp.
If you don't specify expiration in any of them,
it has no problem and all of them will be default expiration, but 
if you specify in one of them, you should speficy expiration after 
that in each of usage

No problem example:

```php
$token1 = $csrf->getToken();
// some code
// ...
$token2 = $csrf->getToken();
// some othe code
// ...
$token3 = $csrf->getToken();
```

Problematic example:

```php
$token1 = $csrf->setExpiration(20)->getToken();
// some code
// ...
// in this code, expiration time will be 20 seconds
//according to previous codes
// if you want anothe expiration, specify it then
$token2 = $csrf->setExpiration(300)->getToken();
// some othe code
// ...
// same thing here
$token3 = $csrf->getToken();
```

- getField(string $name = null, string $input_name = null): string

This method will return input with type hidden and value of token. 
The $name is an ID to generated token and $input_name is the name 
of hidden input.

```php
// returns filed string for form
$field = $csrf->getField();
// output is
// <input type="hidden" name="csrftoken" value="generated token">
```

- getToken(string $name = null): string

This method will return token value only.

Note: If we had token with $name before and it is valid, returns it.

```php
// returns token string
$token = $csrf->getToken();
// output is a hashed string
```

- regenerateToken(string $name = null): string

This method unlike getToken, returns a new token every time.

```php
// returns token string
$token = $csrf->regenerateToken();
// output is a hashed string
```

- validate($token, $name = null): bool

This method validate a token.

```php
// returns true on valid and false otherwise
$isValid = $csrf->validate();
```

- clear(): ICsrf

This method clears all generated token.

```php
// to clear all tokens
$csrf->clear();
```

## Custom Csrf Storage

Just implement `ICsrfStorage` interface

```php
interface ICsrfStorage
{
    /**
     * @param $key
     * @param $value
     * @param $time
     * @return ICsrfStorage
     */
    public function set($key, $value, $time): ICsrfStorage;

    /**
     * @param $key
     * @return mixed
     */
    public function get($key);

    /**
     * @param $key
     * @return bool
     */
    public function has($key): bool;

    /**
     * @param $key
     * @return ICsrfStorage
     */
    public function remove($key): ICsrfStorage;

    /**
     * Remove all stored tokens
     *
     * @param string $prefix
     * @return ICsrfStorage
     */
    public function clear($prefix): ICsrfStorage;
}
```

**Important Note:** All `$key` parameters has an extra prefix that is 
concatenated with a dot to token's hashed name.

```
$key will be something like

__simplicity_csrf_tokens_.f7ff9e8b7bb2e09b70935a5d785e0cc5d9d0abf0

as you can see [__simplicity_csrf_tokens_] is prefix and 
[f7ff9e8b7bb2e09b70935a5d785e0cc5d9d0abf0] is hashed of 
token name that is [Hello] here
```

# License
Under MIT license.

[1]: #custom-csrf-storage