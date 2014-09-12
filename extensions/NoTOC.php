<?php

if ( !defined( 'MEDIAWIKI' ) ) die( 'This file is a MediaWiki extension, it is not a valid entry point' );

$wgExtensionCredits['parserhook'][] = array(
  'path'        => __FILE__,
  'name'        => 'NoTOC',
  'author'      =>'[http://swiftlytilting.com Andrew Fitzgerald]',
  'url'         => 'http://www.mediawiki.org/wiki/Extension:NoTOC',
  'description' => '[http://docs.webplatform.org/wiki/WPD:Infrastructure/Components/WebPlatformDocsExtensionBundle WebPlatform Docs MediaWiki Extension bundle]; Adjust how we display the tables of contents',
);

$wgHooks['ParserClearState'][] = 'efMWNoTOC';

function efMWNoTOC($parser) {
    $parser->mShowToc = false;
    return true;
}
