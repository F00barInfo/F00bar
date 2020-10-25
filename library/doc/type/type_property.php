<?php

declare(strict_types=1);

namespace F00bar\Doc\Type;

/**
 * Property doc block details
 * @property-read mixed $default
 */
class Type_Property
extends Base {
  /**
   * Create Comment from doc block
   * @return \F00bar\Doc\Comment
   */
  protected function create() : \F00bar\Doc\Comment {
    return new \F00bar\Doc\Comment(
      $this->reflector->getProperty(
        \explode( '::', $this->name )[ 1 ]
      )->getDocComment()
    );
  }

  /**
   * Get the properties default value
   * @return mixed
   */
  protected function get_default() {
    return $this->reflector
      ->getDefaultProperties()[ \explode( '::', $this->name )[ 1 ] ] ?? null;
  }

  /**
   * Check if given matches type
   * @param strin|array $name [ mixed, string, ]
   * @return bool
   * @throws \Exception When given input name is invalid
   */
  public static function match( $name ) : bool {
    if(
      ! \is_array( $name )
      || 2 !== \count( $name )
    ) {
      // Invalid input
      throw new \Exception( 'doc type property invalid' );
    }

    return (
      ( \is_string( $name[ 0 ] ) && \class_exists( $name[ 0 ] ) )
      || \is_object( $name[ 0 ] )
    ) && \property_exists( $name[ 0 ], $name[ 1 ] );
  }

  /** @return \Reflector */
  protected function reflector() : \Reflector {
    return new \ReflectionClass( \explode( '::', $this->name )[ 0 ] );
  }

  /**
   * @param mixed $name [ mixed, string, ]
   * @return string
   * @throws \Exception When given input name is invalid
   */
  protected function resolve_name( $name ) : string {
    if(
      ! \is_array( $name )
      || 2 !== \count( $name )
    ) {
      // Invalid input
      throw new \Exception( 'doc type property invalid' );
    }

    if( ! \is_string( $name[ 0 ] ) ) {
      $name[ 0 ] = \get_class( $name[ 0 ] );
    }

    return (string) \implode( '::', $name );
  }

  /** @return string */
  public function type() : string {
    return 'property';
  }
}
