<?php

declare(strict_types=1);

// Load bootstrap
require __DIR__ . \DIRECTORY_SEPARATOR
  . '..' . \DIRECTORY_SEPARATOR
  . 'library' . \DIRECTORY_SEPARATOR
  . 'bootstrap.php';

// Needs to be executed from cli
if( 'cli' !== \OUT ) {
  echo 'Run build tools from command line!' . \EOL;
  exit;
}

// Get parameter
$parameter = new \F00bar\Build\Parameter;

// Determine action and action class
$action = $parameter->help ? 'help' : $parameter->action;
$action_class = '\\F00bar\\Build\\Action\\' . \ucfirst( $action );

// Start build process
echo <<<'EOT'
    __________  ____  __               _       ____
   / ____/ __ \/ __ \/ /_  ____ ______(_)___  / __/___
  / /_  / / / / / / / __ \/ __ `/ ___/ / __ \/ /_/ __ \
 / __/ / /_/ / /_/ / /_/ / /_/ / /  / / / / / __/ /_/ /
/_/    \____/\____/_.___/\__,_/_(_)/_/_/ /_/_/  \____/

Nice build process!

EOT;

new $action_class( $parameter );
