<?php

if ( !defined( 'MEDIAWIKI' ) ) die( 'This file is a MediaWiki extension, it is not a valid entry point' );

$wgExtensionCredits['parserhook'][] = array(
  'path'        => __FILE__,
  'name'        => 'NoTOC',
  'version'     => '0.1.0',
  'author'      =>'[http://swiftlytilting.com Andrew Fitzgerald]',
  'url'         => 'http://www.mediawiki.org/wiki/Extension:NoTOC',
  'description' => '[http://docs.webplatform.org/wiki/WPD:Infrastructure/Components/WebPlatformDocsExtensionBundle Part of WebPlatformDocs extension bundle];  Turns off TOC by default on all pages',
);

$wgHooks['ParserClearState'][] = 'efMWNoTOC';

function efMWNoTOC($parser) {
    $parser->mShowToc = false;
    return true;
}
