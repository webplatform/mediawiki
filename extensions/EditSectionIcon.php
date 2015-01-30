<?php

/**
 * Edit Section Link Icon
 *
 * @author Doug Schepers (http://schepers.cc)
 * @author Tim Laqua <t.laqua at gmail dot com>
 */

if ( !defined( 'MEDIAWIKI' ) ) die( 'This file is a MediaWiki extension, it is not a valid entry point' );

$wgExtensionCredits['parserhook'][] = array(
  'path'        => __FILE__,
  'name'        =>'Edit Section Link Icon',
  'author'      => array(
                    '[http://schepers.cc Doug Schepers]',
                    'Tim Laqua <t.laqua at gmail dot com>'
                ),
  'url'         =>'http://docs.webplatform.org/wiki/WPD:Infrastructure/Components/WebPlatformMediaWikiExtensionBundle',
  'description' =>'[http://docs.webplatform.org/wiki/WPD:Infrastructure/Components/WebPlatformMediaWikiExtensionBundle WebPlatform Docs MediaWiki Extension bundle]; Removes the edit link beside each page sections'
);

$wgHooks['OutputPageBeforeHTML'][]  = 'wfEditSectionLinkTransform';

function wfEditSectionLinkTransform(&$parser, &$text) {
    $text = preg_replace("/<span class=\"editsection\">\[<a href=\"(.+)\" title=\"(.+)\">".wfMessage('editsection')->text()."<\/a>\]<\/span>/i", "<span class=\"editsection\"><a href=\"$1\" title=\"$2\">&nbsp;</a></span>",$text);
    return true;
}
