<?php

declare(strict_types=1);

namespace F00bar\Build;

abstract class Action {
  /** @var \F00bar\Build\Parameter */
  protected \F00bar\Build\Parameter $parameter;

  /** @var bool Run tasks in forced mode */
  protected bool $force = false;

  /** @var int Execution counter */
  private int $execution_count = 0;

  /** @var \F00bar\Build\Task[] List of tasks to be executed */
  private array $task_list = [];

  /** @param \F00bar\Build\Parameter $parameter */
  final public function __construct( \F00bar\Build\Parameter $parameter ) {
    // Save parameters
    $this->parameter = $parameter;
    // Force tasks on first run with enabled debug
    $this->force = $this->parameter->force
      || ( $this->parameter->debug && 0 === $this->execution_count );

    // Initialize action
    $this->init();
    // Execute action
    $this->execute();
  }

  /** Initialize action */
  abstract protected function init();

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

  /** Execute action */
  protected function execute() {
    // Increase execution counter
    ++ $this->execution_count;
    // Execute task list
    foreach( $this->task_list as $task ) {
      $task->execute();
    }
  }
}
