<?php

declare(strict_types=1);

namespace F00bar\Build;

abstract class Task {
  /** @var \F00bar\Build\Parameter */
  protected \F00bar\Build\Parameter $parameter;

  /** @param \F00bar\Build\Parameter $parameter */
  final public function __construct( \F00bar\Build\Parameter $parameter ) {
    // Save parameters
    $this->parameter = $parameter;

    // Initialize task
    $this->init();
  }

  /** Initialize task */
  abstract protected function init();

  /** Execute task */
  abstract public function execute();
}
