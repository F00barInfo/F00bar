<?php

declare(strict_types=1);

namespace F00bar\Doc\Type;

/** Function doc block details */
class Type_Function
extends Base {
  /**
   * Check if given matches type
   * @param string $name
   * @return bool
   */
  public static function match( $name ) : bool {
    return \is_string( $name ) && \function_exists( $name );
  }

  /** @return \Reflector */
  protected function reflector() : \Reflector {
    return new \ReflectionFunction( $this->name );
  }

  /**
   * @param mixed $name
   * @return string
   */
  protected function resolve_name( $name ) : string {
    return (string) $name;
  }

  /** @return string */
  public function type() : string {
    return 'function';
  }
}
