<?php

declare(strict_types=1);

namespace F00bar\Translate\Twig;

/** Twig translate extension */
class Extension
extends \Twig\Extension\AbstractExtension {
  /** @var string[] */
  private array $l10n = [];

  /** Save collected translation on destruct */
  public function __destruct() {
    // Create php file from collected translastions
    $output = '<?php' . \EOL_PRE;
    foreach( $this->l10n as $data ) {
      $output .= $data[ 0 ]
        . '(\'' . \implode( '\',\'', \array_map(
          fn( $item ) => str_replace( '\'', '\\\'', $item ),
          \array_slice( $data, 1 )
        ) ) . '\');' . \EOL_PRE;
    }

    $file_system = new \Symfony\Component\Filesystem\Filesystem;
    // Save compiled template
    $file_system->dumpFile(
      \PATH_CACHE . 'twig' . \DS . 'l10n.php',
      $output
    );
  }

  /** @return array */
  public function getFunctions() {
    return [
      new \Twig\TwigFunction( '__', [ $this, '__' ] ),
    ];
  }

  /** @param array ...$argument */
  private function log( array ...$argument ) {
    $this->l10n[ \implode( ':?:', $argument ) ] = $argument;
  }

  /**
   * @param string $text
   * @param string $locale
   * @return string
   */
  public function __( string $text, string $locale ) : string {
    $this->log( '__', $text, 'default' );
    return \F00bar\Translate\Translate::__( $text, $locale );
  }
}
