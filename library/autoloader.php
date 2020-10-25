<?php

declare(strict_types=1);

namespace F00bar;

/** Library autoloader */
class Autoloader {
  /** Register the autoloader as spl autoload */
  public function __construct() {
    // Register spl autoloader
    \spl_autoload_register( [ $this, 'load_library' ] );
  }

  /**
   * Split given namespace by the namespace separator
   * and returns namespace parts in an array
   * @param string $class
   * @return string[]
   */
  private function split( string $class ) : array {
    return \explode( '\\', \trim( $class, '\\' ) );
  }

  /**
   * Load class file by class name
   * @param string $class
   */
  private function load_library( string $class ) {
    // Split class name
    $split = $this->split( $class );
    // Not within root name space
    if( $split[ 0 ] !== __NAMESPACE__ ) {
      return;
    }
    // Remove root namespace for class file loading
    unset( $split[ 0 ] );
    // Build include path
    $path = \PATH_LIBRARY . \strtolower( \implode( \DS, $split ) ) . '.php';
    // Check if class file exists
    if( ! \is_file( $path ) ) {
      throw new \Exception(
        'class ' . \var_export( $class, true ) . ' not found'
      );
    }
    // Load class file
    require $path;
  }
}
