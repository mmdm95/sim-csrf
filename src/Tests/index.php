<?php

use Sim\Csrf\Csrf;

include_once '../../vendor/autoload.php';

$csrf = new Csrf();

$tokenDefault = $csrf->getToken();
var_dump('default name token:', $tokenDefault);
echo PHP_EOL;
$tokenSpecific = $csrf->getToken('addUser');
var_dump('specific name token:', $tokenSpecific);
echo PHP_EOL;

var_dump('default name token validation:', $csrf->validate($tokenDefault));
echo PHP_EOL;
var_dump('specific name token validation:', $csrf->validate($tokenSpecific, 'addUser'));
