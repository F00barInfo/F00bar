<?php

declare(strict_types=1);

namespace F00bar\Translate;

/** Translate locale instance */
class Locale {
  /** @var \Gettext\Translations Translations instance */
  private ?\Gettext\Translations $translation = null;

  /**
   * Initialize locale
   * @param string $locale
   */
  public function __construct( string $locale ) {
    $mo = new \Gettext\Loader\MoLoader;
    // Load reqested MO file
    if( \file_exists( \PATH_LOCALE . $locale . '.mo' ) ) {
      $this->translation = $mo->loadFile( \PATH_LOCALE . $locale . '.mo' );
    }
  }

  /**
   * Return translated text
   * @param string $text
   * @return string
   */
  public function translate( string $text ) : string {
    if( $this->translation ) {
      $find = $this->translation->find( null, $text );
      if( $find ) {
        $text = $find->getTranslation();
      }
    }

    return $text;
  }
}
