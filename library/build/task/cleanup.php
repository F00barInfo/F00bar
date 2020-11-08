<?php

declare(strict_types=1);

namespace F00bar\Build\Task;

/** Build task "cleanup" */
class Cleanup
extends \F00bar\Build\Task {
  /** @var \Symfony\Component\Finder\Finder File reader */
  private \Symfony\Component\Finder\Finder $finder;

  /** @var \Symfony\Component\Finder\Finder File writer */
  private \Symfony\Component\Filesystem\Filesystem $file_system;

  /** Initialize task */
  protected function init() {
    $this->finder = new \Symfony\Component\Finder\Finder;
    $this->file_system = new \Symfony\Component\Filesystem\Filesystem;
  }

  /** Execute task */
  public function execute() {
    echo \TAB . 'Cleanup' . \EOL;
    $this->file_system->remove( \PATH_DIST );
  }
}
