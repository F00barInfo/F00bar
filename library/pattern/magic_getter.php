<?php

declare(strict_types=1);

namespace F00bar\Pattern;

/** Magic getter pattern; getter function over direct property access */
trait Magic_Getter {
  /**
   * @param string $name
   * @return mixed
   * @throws \Exception Property not exists
   */
  final public function __get( string $name ) {
    // Trim leading/trailing underscores, as they are assumed to be private
    $public = \trim( $name, '_' );

    // Check for existing getter method
    if( \method_exists( $this, 'get_' . $public ) ) {
      return $this->{ 'get_' . $public }();
    // Check for existing property
    } elseif( \property_exists( $this, $public ) ) {
      return $this->$public;
    }

    // Nothing to get
    throw new \Exception(
      'get parameter ' . \var_export( $public, true ) . ' not found'
    );
  }
}
