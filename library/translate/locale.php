<?php

declare(strict_types=1);

namespace F00bar\Translate;

/** Translate locale instance */
class Locale {
  /** @var \Gettext\Translations Translations instance */
  private \Gettext\Translations $translation;

  /**
   * Initialize locale
   * @param string $locale
   */
  public function __construct( string $locale ) {
    $this->translation =
      \file_exists( \PATH_LOCALE . $locale . '.mo' )
        ? ( new \Gettext\Loader\MoLoader )->loadFile( \PATH_LOCALE . $locale . '.mo' )
        : \Gettext\Translations::create( null, $locale );
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
