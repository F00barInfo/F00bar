<?php

declare(strict_types=1);

################################################################################
# FORMATTING                                                                   #
################################################################################

/** @var string Directory separator short hand */
\defined( '\DS' ) ||
  \define( 'DS', \DIRECTORY_SEPARATOR );

/** @var string Output type (cli or gui) */
\defined( '\OUT' ) ||
  \define( 'OUT', 'cli' === \PHP_SAPI ? 'cli' : 'gui' );

/** @var string "end of line" preformatted  */
\defined( '\EOL_PRE' ) ||
  \define( 'EOL_PRE', \PHP_EOL );

/** @var string "end of line" depending on output type */
\defined( '\EOL' ) ||
  \define( 'EOL', 'cli' === \OUT ? \EOL_PRE : '<br>' );

/** @var string "tab" preformatted */
\defined( '\TAB_PRE' ) ||
  \define( 'TAB_PRE', '  ' );

/** @var string "tab" depending on output type */
\defined( '\TAB' ) ||
  \define( 'TAB', 'cli' === \OUT ? \TAB_PRE : '&nbsp;&nbsp;' );

################################################################################
# PATH                                                                         #
################################################################################

/** @var string Overall root path */
\defined( '\PATH_ROOT' ) ||
  \define( 'PATH_ROOT', \realpath( __DIR__ . \DS . '..' ) . \DS );

/** @var string Library path */
\defined( '\PATH_LIBRARY' ) ||
  \define( 'PATH_LIBRARY', \PATH_ROOT . 'library' . \DS );

/** @var string Library path */
\defined( '\PATH_CACHE' ) ||
  \define( 'PATH_CACHE', \PATH_ROOT . 'cache' . \DS );

/** @var string Library path */
\defined( '\PATH_CONTENT' ) ||
  \define( 'PATH_CONTENT', \PATH_ROOT . 'content' . \DS );

/** @var string Library path */
\defined( '\PATH_LOCALE' ) ||
  \define( 'PATH_LOCALE', \PATH_ROOT . 'locale' . \DS );

/** @var string Public path */
\defined( '\PATH_PUBLIC' ) ||
  \define( 'PATH_PUBLIC', \PATH_ROOT . 'public' . \DS );

/** @var string Public path */
\defined( '\PATH_DIST' ) ||
  \define( 'PATH_DIST', \PATH_ROOT . 'dist' . \DS );
