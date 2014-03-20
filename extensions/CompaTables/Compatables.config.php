<?php

/**
 * `$wgCompatablesJsonFileUrl`: Compatibility data JSON object location
 *
 * Specify full URL to a JSON data object.
 *
 * NOTE: JSON object format is currently work in progress.
 *
 * *HINT* The hostname (e.g. `docs.webplatform.local`) MUST be resolvable by
 * the application server running MediaWiki. If you use a special name make
 * sure your MediaWiki installation has a maching entry in the hosts file
 *
 * To use, add the following under the `require_once` statement in
 * your `LocalSettings.php` file:
 *
 *    require_once('$IP/extensions/CompaTables/compatables.php');
 *    $wgCompatablesJsonFileUrl = 'http://docs.webplatform.local/compat/data.json';
 *
 * @var string Location of compatability JSON file
 */
$wgCompatablesJsonFileUrl = (isset($GLOBALS['wgCompatablesJsonFileUrl']))?$GLOBALS['wgCompatablesJsonFileUrl']:'http://docs.webplatform.org/compat/data.json';

/**
 * `$wgCompatablesUseESI`: Enable ESI tags support
 *
 * Specify whether or not to use ESI tags in the rendered
 * document.
 *
 * To use, add the following under the `require_once` statement in
 * your `LocalSettings.php` file:
 *
 *    require_once('$IP/extensions/CompaTables/compatables.php');
 *    $wgCompatablesUseESI = true;
 *
 * @var bool Whether to use Edge-Side-Includes for tables
 */
$wgCompatablesUseESI = (isset($GLOBALS['wgCompatablesUseESI']))?$GLOBALS['wgCompatablesUseESI']:true;

/**
 * `$wgCompatablesCssFileUrl`: CSS file to include to style tables
 *
 * @var string Path to the CSS file publicly accessible to the web
 */
$wgCompatablesCssFileUrl = (isset($GLOBALS['wgCompatablesCssFileUrl']))?$GLOBALS['wgCompatablesCssFileUrl']:'/w/extensions/CompaTables/compatables.css';

/**
 * `$wgCompatablesSpecialUrl`: Path to the special page
 */
$wgCompatablesSpecialUrl = (isset($GLOBALS['wgCompatablesSpecialUrl']))?$GLOBALS['wgCompatablesSpecialUrl']:'/wiki/Special:Compatables';


$wgAvailableRights[] = 'purgecompatables';
$wgGroupPermissions['sysop']['purgecompatables'] = true;
