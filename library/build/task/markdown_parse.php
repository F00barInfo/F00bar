<?php

declare(strict_types=1);

namespace F00bar\Build\Task;

/** Build task "markdown parse" */
class Markdown_Parse
extends \F00bar\Build\Task {
  /** @var array List of meta attributes not to be inherited */
  const META_SKIP_INHERIT = [ 'title' ];

  /** @var \Symfony\Component\Finder\Finder File reader */
  private \Symfony\Component\Finder\Finder $finder;

  /** @var \Symfony\Component\Finder\Finder File writer */
  private \Symfony\Component\Filesystem\Filesystem $file_system;

  /** @var \Pagerange\Markdown\MetaParsedown Markdown parser */
  private \Pagerange\Markdown\MetaParsedown $parser;

  /** Initialize task */
  protected function init() {
    $this->finder = new \Symfony\Component\Finder\Finder;
    $this->file_system = new \Symfony\Component\Filesystem\Filesystem;
    $this->parser = new \Pagerange\Markdown\MetaParsedown;
  }

  /** Execute task */
  public function execute() {
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
          'content' => $this->read_content( $content ),
        ]
      );
    }

    echo \EOL;

    $tree = $this->inherit_meta( $tree );
    return $this->inherit_tree( $tree, $tree );
  }

  /**
   * Strip markdown from meta, render with twig and then with markdown parser
   * @param string $content
   * @return string
   */
  private function read_content( string $content ) : string {
    $twig_loader = new \Twig\Loader\ChainLoader( [
      new \Twig\Loader\ArrayLoader( [
        'index.html' => trim( $this->parser->stripMeta( $content ) ),
      ] ),
      new \Twig\Loader\FilesystemLoader( Twig_Compile::PATH_TEMPLATE )
    ] );

    return $this->parser->text(
      ( new \Twig\Environment( $twig_loader ) )->render('index.html')
    );
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
  private function inherit_meta(
      array $tree,
      array $meta = []
    ) : array {
    // Inherit meta from parent
    $inherit = \array_filter(
      ( $tree[ 'index.md' ][ 'meta' ] ?? [] ) + $meta,
      fn( $key ) => ! \in_array( $key, self::META_SKIP_INHERIT ),
      \ARRAY_FILTER_USE_KEY
    );

    // Inherit tree meta from parent
    foreach( $tree as $name => &$data ) {
      if( '.md' !== substr( $name, -3 ) ) {
        $data = $this->inherit_meta( $data, $inherit );
      } else {
        $data[ 'meta' ] += $inherit;
      }
    }

    return $tree;
  }

  /**
   * Inherit tree content when inherit attribute is set
   * @param array $tree
   * @param array $item
   * @param string $name
   * @param array $path
   * @return array
   */
  private function inherit_tree(
    array $tree,
    array $item,
    string $name = '',
    array $path = []
  ) : array {
    empty( $name ) ?: $path[] = $name;

    // Inherit tree meta from parent
    foreach( $item as $item_name => $item_data ) {
      if( '.md' !== substr( $item_name, -3 ) ) {
        $tree = $this->inherit_tree( $tree, $item_data, $item_name, $path );
      } elseif( (bool)( $item_data[ 'meta' ][ 'inherit' ] ?? false ) ) {
        foreach( $tree as $locale => &$tree_data ) {
          // Overwrite locale with value from target branch
          $item_data[ 'locale' ] = $tree_data[ 'index.md' ][ 'locale' ];
          // Inherit item into other branches
          $tree_data = $this->inherit_tree_data(
            $tree_data,
            $item_data,
            $item_name,
            \array_slice( $path, 1 )
          );
        }
      }
    }

    return $tree;
  }

  /**
   * Inherit item onto given path
   * @param array $tree
   * @param array $item
   * @param string $name
   * @param array $path
   * @return array
   */
  private function inherit_tree_data(
    array $tree,
    array $item,
    string $name,
    array $path
  ) : array {
    $current = \array_shift( $path );
    if( ! empty( $current ) ) {
      // Go deeper
      isset( $tree[ $current ] ) ?: $tree[ $current ] = [];

      foreach( $tree as $tree_name => &$tree_data ) {
        $tree_data = $this->inherit_tree_data(
          $tree_data,
          $item,
          $name,
          $path
        );
      }
    } else {
      $tree[ $name ] = $item;
    }

    return $tree;
  }

  /**
   * Save given tree
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
