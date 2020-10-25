<?php

declare(strict_types=1);

/**
 *     __________  ____  __               _       ____
 *    / ____/ __ \/ __ \/ /_  ____ ______(_)___  / __/___
 *   / /_  / / / / / / / __ \/ __ `/ ___/ / __ \/ /_/ __ \
 *  / __/ / /_/ / /_/ / /_/ / /_/ / /  / / / / / __/ /_/ /
 * /_/    \____/\____/_.___/\__,_/_(_)/_/_/ /_/_/  \____/
 *
 * Nice bootstrap!
 */

// Enable full error reporting
\error_reporting( -1 );

// Check php version if it's higher or equal to the requirement
if( ! \version_compare( \PHP_VERSION, '7.4', '>=' ) ) {
  echo 'PHP 7.4 or newer required to run F00bar.info';
  exit;
}

// Get constants
require 'constant.php';

// Register autoloader
require \PATH_LIBRARY . 'autoloader.php';
new \F00bar\Autoloader;

// Register error handler
new \F00bar\Error\Handler;

// Regiter vendor autoloader
$vendor_autoloader = \PATH_ROOT . 'vendor' . \DS . 'autoload.php';
if( ! \file_exists( $vendor_autoloader ) ) {
  echo 'missing composer autoloader!';
  exit;
}
require $vendor_autoloader;

// Send OtakuPress meta header
\header( 'Content-Type: text/html;charset=UTF-8', true );
\header( 'X-Powered-By: Nice Boat!', true );
\header( 'X-Version: OVER 9000!', true );
\header( 'X-UA-Compatible: IE=edge,chrome=1', true );
