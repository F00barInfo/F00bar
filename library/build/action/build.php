<?php

declare(strict_types=1);

namespace F00bar\Build\Action;

/** Build action "build" */
class Build
extends \F00bar\Build\Action {
  /** Initialize action */
  protected function init() {
    $this->add_task( 'Markdown_Parse' );
    $this->add_task( 'Twig_Compile' );
  }
}
