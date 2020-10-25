<?php

declare(strict_types=1);

namespace F00bar\Error;

/** Error handlers */
class Handler {
  /** Register custom error handlers */
  public function __construct() {
    // Enable or disable displayed error depending on debug mode
    \ini_set( 'display_errors', '1' );
    \ini_set( 'html_errors', 'gui' === \OUT ? '1' : '0' );

    // Register error handler
    \set_error_handler( [ $this, 'error_handler' ] );
    \set_exception_handler( [ $this, 'exception_handler' ] );
  }

  /**
   * Non throwable php errors
	 * @param int $code
	 * @param string $message
   */
  public function error_handler(
    int $code,
    string $message
  ) {
    throw new \Exception( 'PHP_ERROR: ' . $message, $code );
  }

  /**
   * Default throwable handler
	 * @param \Throwable $throwable
   */
  public function exception_handler( \Throwable $throwable ) {
    new Output( $throwable );
    exit;
  }
}
