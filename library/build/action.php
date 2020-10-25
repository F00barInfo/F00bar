<?php

declare(strict_types=1);

namespace F00bar\Build;

abstract class Action {
  /** @var \Symfony\Component\Finder\Finder File writer */
  private ?\Symfony\Component\Filesystem\Filesystem $file_system = null;

  /** @var \F00bar\Build\Parameter */
  protected \F00bar\Build\Parameter $parameter;

  /** @var int Execution counter */
  private $execution_count = 0;

  /** @var \F00bar\Build\Task[] List of tasks to be executed */
  private $task_list = [];

  /** @param \F00bar\Build\Parameter $parameter */
  final public function __construct( \F00bar\Build\Parameter $parameter ) {
    $this->file_system = new \Symfony\Component\Filesystem\Filesystem;

    // Save parameters
    $this->parameter = $parameter;
    // Force tasks on first run with enabled debug
    $force = $this->parameter->force
      || ( $this->parameter->debug && 1 === $this->execution_count );

    // Run cleanup when force is set
    if( $force ) {
      $this->cleanup();
    }

    // Initialize action
    $this->init();
    // Execute action
    $this->execute( $force );
  }

  /** Initialize action */
  abstract protected function init();

  /** Cleanup export */
  protected function cleanup() {
    echo \TAB . 'Cleanup' . \EOL;
    $this->file_system->remove( \PATH_DIST );
  }

  /**
   * Add a new task to the task list
   * @param string $name
   */
  protected function add_task( string $name ) {
    // Build task class
    $class = '\\F00bar\\Build\\Task\\' . $name;
    // Create new task
    $this->task_list[] = new $class( $this->parameter );
  }

  /**
   * Execute action
   * @param bool $force
   */
  protected function execute( bool $force ) {
    // Increase execution counter
    ++ $this->execution_count;
    // Execute task list
    foreach( $this->task_list as $task ) {
      $task->execute( $force );
    }
  }
}
