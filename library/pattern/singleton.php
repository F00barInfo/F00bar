<?php

declare(strict_types=1);

namespace F00bar\Pattern;

/** Singleton pattern */
trait Singleton {
  /** @var array Singleton instances */
  static private array $instance_cache = [];

  /** Private singleton constructor */
  final private function __construct() {
    $this->init();
  }

  /** Private singleton clone */
  final private function __clone() {
  }

  /** Private singleton wakeup */
  final private function __wakeup() {
  }

  /** Initialize singleton class */
  protected function init() {
  }

  /**
   * Create singleton instance
   * @return object
   */
  static protected function create() : object {
    $singleton = \get_called_class();

    return self::$instance_cache[ $singleton ]
      ?? ( self::$instance_cache[ $singleton ] = new $singleton );
  }

  /** Get singleton instance */
  abstract static public function instance();
}
