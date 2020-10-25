<?php

declare(strict_types=1);

namespace F00bar\Build\Task;

/** Build task "markdown parse" */
class Markdown_Parse
extends \F00bar\Build\Task {
  /** @var \Symfony\Component\Finder\Finder File reader */
  private ?\Symfony\Component\Finder\Finder $finder = null;

  /** @var \Pagerange\Markdown\MetaParsedown Markdown parser */
  private ?\Pagerange\Markdown\MetaParsedown $parser = null;

  /** @var \Symfony\Component\Finder\Finder File writer */
  private ?\Symfony\Component\Filesystem\Filesystem $file_system = null;

  /** Initialize task */
  protected function init() {
    $this->finder = new \Symfony\Component\Finder\Finder;
    $this->parser = new \Pagerange\Markdown\MetaParsedown;
    $this->file_system = new \Symfony\Component\Filesystem\Filesystem;
  }

  /**
   * Execute task
   * @param bool $force
   */
  public function execute( bool $force = false ) {
    echo \TAB . 'Markdown parse:' . \EOL;
    // Parse and save markdown to html
    $tree = $this->read_tree();
    $this->save_tree( \PATH_DIST, $tree );
  }

  /**
   * Parse markdown content and inherit parent meta into child
   * @return array
   */
  private function read_tree() : array {
    // Find file extension matches
    foreach( $this->finder->in( \PATH_CONTENT )->name( '*.md' ) as $file ) {
      $path = $file->getRelativePathname();
      // Get necessarz line pad
      $pad = \max( $pad ?? 0, \strlen( $path ) );
      // Show current path
      echo \TAB . \TAB . \str_pad( $path, $pad, ' ' ) . "\r";
      \flush(); \usleep( 500 );
      // Get markdown file content
      $content = $file->getContents();
      // Add file to file list
      $tree = $this->merge_tree(
        \explode( \DS, $file->getRelativePath() ),
        $tree ?? [],
        $file->getFilename(),
        [
          'meta' => $this->parser->meta( $content ) ?? [],
          'content' => $this->parser->text( $content ),
        ]
      );
    }

    echo \EOL;

    return $this->inherit_meta( $tree );
  }

  /**
   * Merge markdown content tree recursive
   * @param array $path
   * @param array $tree
   * @param string $name
   * @param array $entry
   * @return array
   */
  private function merge_tree(
    array $path,
    array $tree,
    string $name,
    array $entry
  ) : array {
    $current = \array_shift( $path );

    if( ! empty( $current ) ) {
      // Set locale from first dir
      $entry[ 'locale' ] = $entry[ 'locale' ] ?? $current;
      // Merge tree
      $tree[ $current ] = $this->merge_tree(
        $path,
        $tree[ $current ] ?? [],
        $name,
        $entry
      );
    } else {
      $tree[ $name ] = $entry;
    }

    return $tree;
  }


  /**
   * Inherit markdown meta data from parent to child
   * @param array $tree
   * @param array $meta
   * @return array
   */
  private function inherit_meta( array $tree, array $meta = [] ) : array {
    // Inherit meta from parent
    $inherit = ( $tree[ 'index.md' ][ 'meta' ] ?? [] ) + $meta;
    // Inherit tree meta from parent
    foreach( $tree as $name => &$data ) {
      if( '.md' == substr( $name, -3 ) ) {
        $data[ 'meta' ] += $inherit;
      } else {
        $data = $this->inherit_meta( $data, $inherit );
      }
    }

    return $tree;
  }

  /**
   *
   * @param string $path
   * @param array $tree
   * @throws \Exception
   */
  private function save_tree( string $path, array $tree ) {
    foreach( $tree as $name => $data ) {
      if( '.md' == substr( $name, -3 ) ) {
        $this->save_tree_file( $path . $name . '.html', $data );
      } else {
        $this->save_tree( $path . $name . \DS, $data );
      }
    }
  }

  /**
   * Save a parsed markdown file from tree
   * @param string $path
   * @param array $data
   */
  private function save_tree_file( string $path, array $data ) {
    // Skip files with empty content
    if( empty( $data[ 'content' ] ) ) {
      return;
    }

    $this->file_system->dumpFile( $path, \json_encode( $data ) );
  }
}
