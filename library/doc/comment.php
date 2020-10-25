<?php

declare(strict_types=1);

namespace F00bar\Doc;

/**
 * Doc comment parser
 * @property-read string $description
 * @property-read string $summary
 * @property-read \phpDocumentor\Reflection\DocBlock\Tag[] $tag
 * @property-read \phpDocumentor\Reflection\DocBlock\Tags\Property[] $tag_property
 */
class Comment {
  /** Get magic getter pattern */
  use \F00bar\Pattern\Magic_Getter;

  /** @var \phpDocumentor\Reflection\DocBlockFactory */
  private static ?\phpDocumentor\Reflection\DocBlockFactory $_factory = null;

  /** @var \phpDocumentor\Reflection\DocBlock */
  private ?\phpDocumentor\Reflection\DocBlock $_doc = null;

  /** @param string $comment */
  public function __construct( string $comment ) {
    // Create factory
    self::$_factory ?: self::$_factory =
      \phpDocumentor\Reflection\DocBlockFactory::createInstance();

    // Create doc block
    $this->_doc = $this::$_factory->create( $comment );
  }

  /** @return string */
  private function get_description() : string {
   return $this->_doc->getDescription()->render();
  }

  /** @return string */
  private function get_summary() : string {
    return $this->_doc->getSummary();
  }

  /** @return \phpDocumentor\Reflection\DocBlock\Tag[] */
  private function get_tag() : array {
    return $this->_doc->getTags();
  }

  /** @return \phpDocumentor\Reflection\DocBlock\Tags\Property[] */
  private function get_tag_property() : array {
    return \array_merge(
      $this->_doc->getTagsByName( 'property' ),
      $this->_doc->getTagsByName( 'property-read' ),
      $this->_doc->getTagsByName( 'property-write' )
    );
  }
}
