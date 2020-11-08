<?php

declare(strict_types=1);

namespace F00bar\Build\Action;

/** Build action "cleanup" */
class Cleanup
extends \F00bar\Build\Action {
  /** Initialize action */
  protected function init() {
    $this->add_task( 'Cleanup' );
  }
}
