<?php

declare(strict_types=1);

namespace F00bar\Doc\Type;

/**
 * Class/object doc block details
 * @property-read string[] $property_list
 */
class Type_Class
extends Base {
  /** @var string[] */
  private array $property_list;

  /** @return string[] */
  private function fetch_property_list() : array {
    $list = [];

    foreach( $this->comment->tag_property as $tag ) {
      $prop = $tag->getVariableName();

      // Check if the property actual exists
      $prop_exists = \property_exists( $this->name, $prop );

      // Add property details
      $list[ $prop ] = [
        'property' => $tag->getName(),
        'description' => $tag->getDescription()->render(),
        'type' => (string) $tag->getType(),
        'default' => $prop_exists
          ? ( new Type_Property( [ $this->name, $prop ] ) )->default
          : null,
      ];
    }

    return $this->property_list = $list;
  }

  /** @return array */
  protected function get_property_list() : array {
    return $this->property_list ?? $this->fetch_property_list();
  }

  /**
   * Check if given matches type
   * @param string|object $name
   * @return bool
   */
  public static function match( $name ) : bool {
    return
      ( \is_string( $name ) && \class_exists( $name ) )
      || \is_object( $name );
  }

  /** @return \Reflector */
  protected function reflector() : \Reflector {
    return new \ReflectionClass( $this->name );
  }

  /**
   * @param mixed $name
   * @return string
   */
  protected function resolve_name( $name ) : string {
    if( \is_object( $name ) ) {
      $name = \get_class( $name );
    }

    return (string) $name;
  }

  /** @return string */
  public function type() : string {
    return 'class';
  }
}
