<?php

declare(strict_types=1);

namespace F00bar\Pattern;

/** Magic setter pattern; setter function over direct property access */
trait Magic_Setter {
  /**
   * @param string $name
   * @param mixed $value
   * @throws \Exception Property not exists
   */
  final public function __set( string $name, $value ) {
    // Trim leading/trailing underscores, as they are assumed to be private
    $public = \trim( $name, '_' );

    // Check for existing setter method
    if( \method_exists( $this, 'set_' . $public ) ) {
      $this->{ 'set_' . $public }();
    // Check for existing property
    } elseif( \property_exists( $this, $public ) ) {
      $this->$public = $value;
    // Nothing to set
    } else {
      throw new \Exception(
        'set parameter ' . \var_export( $public, true ) . ' not found'
      );
    }
  }
}
