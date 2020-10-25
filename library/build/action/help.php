<?php

declare(strict_types=1);

namespace F00bar\Build\Action;

/** Build action "help" */
class Help
extends \F00bar\Build\Action {
  /** Initialize action */
  protected function init() {
    echo \EOL;

    // Get property details
    $prop_list = $this->parameter->list;
    // Get the maximal parameter length
    $pad = 4 + \max( \array_map( 'strlen', \array_keys( $prop_list ) ) );
    // Desc intend
    $intend = \str_repeat( ' ', $pad + 1 );

    // Shop parameter help
    foreach( $prop_list as $prop => $data ) {
      // Skip read only
      if( 'property-read' === $data[ 'property' ] ) {
        continue;
      }

      // Show property
      echo
        \str_pad( '--' . $prop, $pad, ' ', \STR_PAD_LEFT ) . ' '
        . \wordwrap( $data[ 'description' ], 60, \EOL . $intend ) . \EOL
        . $intend . 'Default = '
        . \preg_replace(
          '/\r?\n\r?/',
          \EOL . $intend, \var_export( $data[ 'default' ], true )
        ) . \EOL
        . \EOL;
    }
  }
}
