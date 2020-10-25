<?php

declare(strict_types=1);

namespace F00bar\Error;

/** Display and log errors */
class Output {
  /** @var string Error log path */
  const LOG_PATH = \PATH_CACHE . 'php' . \DS. 'error' . \DS;

  /** @var string Header used to displaz error */
  const DISPLAY_HEADER = <<<'EOT'
    __________  ____  __               _       ____
   / ____/ __ \/ __ \/ /_  ____ ______(_)___  / __/___
  / /_  / / / / / / / __ \/ __ `/ ___/ / __ \/ /_/ __ \
 / __/ / /_/ / /_/ / /_/ / /_/ / /  / / / / / __/ /_/ /
/_/    \____/\____/_.___/\__,_/_(_)/_/_/ /_/_/  \____/
EOT;

  /** @param \Throwable $throwable */
  public function __construct( \Throwable $throwable ) {
    $error_log = $this->parse( $throwable );

    $this->log( $error_log );
    $this->display( $error_log );
  }

  /**
   * Prepare thrown error for log and display
   * @param \Throwable $throwable
   * @return string Error log message
   */
  private function parse( \Throwable $throwable ) : string {
    // Get full recursive call stace
    $stack = $this->call_stack( $throwable );
    // Build printable trace log
    $error_line = '"%s" in file "%s" on line "%s"' . \EOL_PRE;
    $error_log =
      'Time: ' . \time() . \EOL_PRE . \EOL_PRE;

    foreach( $stack as $index => $item ) {
      $error_log .=
        '#' . $index . ': ' . $item[ 'message' ]
        . ( $item[ 'code' ] ? ' (' . $item[ 'code' ] . ')'  : '' ). \EOL_PRE
        . \TAB_PRE . 'thrown ' . \sprintf(
          $error_line, $item[ 'error' ], $item[ 'file' ], $item[ 'line' ]
        );

        foreach( $item[ 'trace' ] as $level => $trace ) {
          $offset = \str_repeat( \TAB_PRE, $level + 2 );

          $error_log .= $offset . \sprintf(
            $error_line,
            $trace[ 'function' ],
            $trace[ 'file' ],
            $trace[ 'line' ]
          );

          foreach( $trace[ 'args' ] as $arg_name => $arg_value ) {
            $error_log .= $offset . \TAB_PRE . $arg_name . ' -> '
              . \htmlspecialchars( \var_export( $arg_value, true ) ) . \EOL_PRE;
          }
        }

      $error_log .= \EOL_PRE;
    }

    return $error_log;
  }

  /**
   * Get the throwable call stack (from cur > prev > ...)
   * @param \Throwable $throwable
   * @return array
   */
  private function call_stack( \Throwable $throwable ) : array {
    $stack = [];

    do {
      $stack[] = $this->throwable_detail( $throwable );
      $throwable = $throwable->getPrevious();
    } while( $throwable );

    return $stack;
  }

  /**
   * Get throwable details
   * @param \Throwable $throwable
   * @return array[ 'code', 'message', 'file', 'line', 'trace' ]
   */
  private function throwable_detail( \Throwable $throwable ) : array {
    return [
      'error' => \get_class( $throwable ),
      'code' => $throwable->getCode(),
      'message' => $throwable->getMessage(),
      'file' => $throwable->getFile(),
      'line' => $throwable->getLine(),
      'trace' => $this->throwable_trace( $throwable )
    ];
  }

  /**
   * Get throwable trace
   * @param \Throwable $throwable
   * @return array
   */
  private function throwable_trace( \Throwable $throwable ) : array {
    $trace = [];

    foreach( $throwable->getTrace() as $item ) {
      $class = $item[ 'class' ] ?? '';
      $function = $item[ 'function' ] ?? '';
      $arg = $item[ 'args' ] ?? [];

      $trace[] = [
        'function' => $class . ( $item[ 'type' ] ?? '' ) . $function,
        'file' => $item[ 'file' ] ?? 'unknown',
        'line' => $item[ 'line' ] ?? '-1',
        'args' => $this->throwable_trace_arg( $class, $function, $arg ),
      ];
    }

    return $trace;
  }

  /**
   * Get throwable trace parameter via reflection
   * @param string $class
   * @param string $function
   * @param array $arg
   * @return array
   */
  protected function throwable_trace_arg(
    string $class,
    string $function,
    array $arg
  ) : array {
    if(
      // Without a function the reflection class cannot be used
      empty( $function )
      // Check if reflection is possible
      || ! (
        ! empty( $class )
          ? \method_exists( $class, $function )
          : \function_exists( $function )
      )
    ) {
      return $arg;
    }

    // Use reflection class to get the parameter names
    $reflection = ! empty( $class )
      ? new \ReflectionMethod( $class, $function )
      : new \ReflectionFunction( $function );

    $args = [];

    foreach( $reflection->getParameters() as $index => $param ) {
      $type = $param->getType();
      $name = ( $type ? $type->getName() . ':' : '' )
        . '$' . $param->getName();

      $args[ $name ] = $arg[ $index ] ?? null;
    }

    return $args;
  }

  /**
   * Write thrown error to error log file
   * @param string $error_log
   */
  private function log( string $error_log ) {
      // Create log dir if necessary
      if( ! \is_dir( self::LOG_PATH ) ) {
        \mkdir( self::LOG_PATH, 0777, true );
      }

      // Append message to log or create a new log
      $file = \fopen( self::LOG_PATH . \date( 'Y-m-d' ) . '.log', 'a+' );
      \fwrite( $file, $error_log );
      \fclose( $file );
  }

  /**
   * Displaz thrown error to user
   * @param string $error_log
   */
  public function display( string $error_log ) {
    // Clean output
    0 < \ob_get_level() && \ob_clean();

    // Display error log
    echo
      ( 'gui' === \OUT ? '<pre>' : '' )
      . self::DISPLAY_HEADER . \EOL_PRE
      . \EOL_PRE
      . 'Nice error! See below or error log for more details.' . \EOL_PRE
      . \EOL_PRE
      . $error_log
      . ( 'gui' === \OUT ? '</pre>' : '' );
  }
}
