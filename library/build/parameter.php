<?php

declare(strict_types=1);

namespace F00bar\Build;

/**
 * Build parameters
 * @property-read array $list List of possible parameters
 * @property string $action Build action, consists of different tasks
 * @property bool $compress Activate or deactivate compression for certain tasks
 * @property bool $debug Enable debug mode with more detailed progress output
 * @property ?string $dist_path Path to export to
 * @property bool $force Force the certain tasks to run ignoring caches
 * @property bool $help Show help
 */
class Parameter {
  /** Get magic getter pattern */
  use \F00bar\Pattern\Magic_Getter;

  /** Get magic setter pattern */
  use \F00bar\Pattern\Magic_Setter;

  /** @var array */
  private ?array $_list = null;

  /** @var string */
  private string $action = 'build';

  /** @var bool */
  private bool $compress = false;

  /** @var bool */
  private bool $debug = false;

  /** @var string */
  private ?string $dist_path = \PATH_DIST;

  /** @var bool */
  private bool $force = false;

  /** @var bool */
  private bool $help = false;

  /** Constructor loads parameters */
  public function __construct() {
    $this->load_parameter();
  }

  /** Parse config and fetch cli parameters (param over preset) */
  private function load_parameter() {
    // Make all options require a value
    $opt = \getopt( '', \array_map(
      fn( string $v ) => $v . '::',
      \array_keys( $this->list )
    ) );

    // Use parameters to overwrite preset
    foreach( $opt as $name => $value ) {
      if( 'true' === $value || 'false' === $value ) {
        $value = (bool)$value;
      }
      $this->__set( $name, $value );
    }
  }

  /**
   * Load and return list of parameters
   * @return array
   */
  private function get_list() : array {
    // Load if not already done
    if( null === $this->_list ) {
      $this->_list = (
        new \F00bar\Doc\Type\Type_Class( $this )
      )->property_list;
    }
    // Retrun details
    return $this->_list;
  }

  /**
   * Passing help parameter will enable help mode, cannot be turned of again
   * @return \F00bar\Build\Parameter
   */
  private function set_help() : \F00bar\Build\Parameter {
    // Enable help parameter
    $this->help = true;
    // Method chaining
    return $this;
  }
}
