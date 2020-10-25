<?php

declare(strict_types=1);

namespace F00bar\Build\Task;

/** Build task "markdown parse" */
class Twig_Compile
extends \F00bar\Build\Task {
  /** @var string Path to twig templates */
  const PATH_TEMPLATE = \PATH_PUBLIC . 'template' . \DS;

  /** @var \Symfony\Component\Finder\Finder File reader */
  private ?\Symfony\Component\Finder\Finder $finder = null;

  /** @var \Twig\Environment Twig template compiler */
  private ?\Twig\Environment $twig = null;

  /** @var \Symfony\Component\Finder\Finder File writer */
  private ?\Symfony\Component\Filesystem\Filesystem $file_system = null;

  /** Initialize task */
  protected function init() {
    $this->finder = new \Symfony\Component\Finder\Finder;
    $this->twig = new \Twig\Environment(
      new \Twig\Loader\FilesystemLoader( self::PATH_TEMPLATE )
    );
    $this->file_system = new \Symfony\Component\Filesystem\Filesystem;
    // Register twig extension
    $this->twig->addExtension( new \F00bar\Translate\Twig\Extension );
  }

  /**
   * Execute task
   * @param bool $force
   */
  public function execute( bool $force = false ) {
    echo \TAB . 'Twig compile:' . \EOL;
    // Read parsed mark down and save compiled templates
    $this->save_template();
  }

  /** Read parsed mark down and save compiled templates */
  private function save_template() {
    // Find file extension matches
    foreach( $this->finder->in( \PATH_DIST )->name( '*.md.html' ) as $file ) {
      $path = $file->getRelativePathname();
      // Get necessarz line pad
      $pad = \max( $pad ?? 0, \strlen( $path ) );
      // Show current path
      echo \TAB . \TAB . \str_pad( $path, $pad, ' ' ) . "\r";
      \flush(); \usleep( 500 );
      // Get compiled markdown content
      $data = \json_decode( $file->getContents(), true );
      $path = \PATH_DIST . $file->getRelativePath() . \DS
        . $file->getBasename( '.md.html' ) . '.html';
      // Compile template
      $output = $this->compile_template( $data );
      // Save compiled template
      $this->file_system->dumpFile( $path, $output );
    }

    echo \EOL;
  }

  private function compile_template( array $data ) {
    // Get custom template file or use default
    $template = $data[ 'meta' ][ 'template' ] ?? 'index.html';
    // Compile template
    return $this->twig->render(
      $template, [
      'locale' => $data[ 'locale' ],
      'title' => $this->find_title( $data ),
      'content' => $data[ 'content' ],
      'content_meta' => $data[ 'meta' ],
    ] );
  }

  private function find_title( array $data ) : string {
    $crawler = new \Symfony\Component\DomCrawler\Crawler( $data[ 'content' ] );
    // Try to find h1 headlinea
    $find = $crawler->filter( 'h1' );
    // Use found title to fallback
    return $find->count()
      ? $find->first()->text()
      : \F00bar\Translate\Translate::__( 'Untitled', $data[ 'locale' ] );
  }
}
