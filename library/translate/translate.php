<?php

declare(strict_types=1);

namespace F00bar\Translate;

/** Translate handler */
class Translate {
  /** Get singleton pattern */
  use \F00bar\Pattern\Singleton;

  /** @var Locale[] Cached locale instance */
  private array $locale = [];

  /**
   * {@inheritDoc}
   * @return Translate
   */
  static public function instance() : Translate {
    return self::create();
  }

  /**
   * Get locale instance
   * @param string $locale
   * @return Locale
   */
  private function locale( string $locale ) : Locale {
    return $this->locale[ $locale ] =
      $this->locale[ $locale ] ?? new Locale( $locale );
  }

  /**
   * Returns translated text
   * @param string $text
   * @param string $locale
   * @return string
   */
  private function translate( string $text, string $locale ) : string {
    return $this->locale( $locale )->translate( $text );
  }

  /**
   * Returns translated text
   * @param string $text
   * @param string $locale
   * @return string
   */
  public static function __( string $text, string $locale ) : string {
    return self::instance()->translate( $text, $locale );
  }
  /**
   * Display translated text
   * @param string $text
   * @param string $locale
   */
  public static function _e( string $text, string $locale ) {
    echo self::instance()->translate( $text, $locale );
  }
}
