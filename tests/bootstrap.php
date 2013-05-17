<?php
/**
 * FormMaker- php forming basic HTML and FORM elements
 * @author Raftalks <http://github.com/raftalks>
 *
 * Bootstraper for PHPUnit tests.
 */
error_reporting(E_ALL | E_STRICT);

$loader = require_once __DIR__ . '/../vendor/autoload.php';
$loader->add('Form\\', __DIR__);
$loader->add('Html\\', __DIR__);
