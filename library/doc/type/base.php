<?php

declare(strict_types=1);

namespace F00bar\Doc\Type;

/**
 * Basis doc block details
 * @property-read \OtakuPress\Core\Doc\Comment $comment
 * @property-read string $name
 * @property-read \Reflector $reflector
 */
abstract class Base {
  /** Get magic getter pattern */
  use \F00bar\Pattern\Magic_Getter;

  /** @var \F00bar\Doc\Comment[] */
  private static array $_cache;

  /** @var string */
  private string $name;

  /** @var \Reflector */
  private \Reflector $reflector;

  /** @var \F00bar\Doc\Comment */
  private \F00bar\Doc\Comment $comment;

  /** @param mixed $name */
  public function __construct( $name ) {
    $this->name = $this->resolve_name( $name );
    $this->reflector = $this->reflector();
    $this->comment = $this->cache();
  }

  /**
   * Create Comment from doc block
   * @return \F00bar\Doc\Comment
   */
  private function cache() : \F00bar\Doc\Comment {
    $get = $this->type() . '##' . $this->name;

    // Create if not cached
    if( ! isset( self::$_cache[ $get ] ) ) {
      // Create and cache
      self::$_cache[ $get ] = $this->create();
    }

    return self::$_cache[ $get ];
  }

  /**
   * Create Comment from doc block
   * @return \F00bar\Doc\Comment
   */
  protected function create() : \F00bar\Doc\Comment {
    return new \F00bar\Doc\Comment(
      $this->reflector->getDocComment()
    );
  }

  /**
   * Check if given matches type
   * @param mixed $name
   * @return bool
   */
  abstract public static function match( $name ) : bool;

  /** @return \Reflector */
  abstract protected function reflector() : \Reflector;

  /**
   * @param mixed $name
   * @return string
   */
  abstract protected function resolve_name( $name ) : string;

  /** @return string */
  abstract public function type() : string;
}
