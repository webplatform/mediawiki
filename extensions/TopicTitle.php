<?php

/**
 * Change HTML title to feature topic
 *
 * @author Doug Schepers (http://schepers.cc)
 */

if ( !defined( 'MEDIAWIKI' ) ) die( 'This file is a MediaWiki extension, it is not a valid entry point' );

$wgExtensionCredits['other'][] = array(
  'path'        => __FILE__,
  'name'        => 'Topic Title',
  'version'     => '0.1',
  'author'      => '[http://schepers.cc Doug Schepers]',
  'url'         => 'http://docs.webplatform.org/wiki/WPD:Infrastructure/Components/WebPlatformDocsExtensionBundle',
  'description' => '[http://docs.webplatform.org/wiki/WPD:Infrastructure/Components/WebPlatformDocsExtensionBundle WebPlatform Docs MediaWiki Extension bundle]; Adds common keywords to document title',
);

$wgHooks['BeforePageDisplay'][] = 'TopicTitle::insertTitle';

class TopicTitle {
        public static function insertTitle( OutputPage &$out ) {
            $title = $out->getHTMLTitle();
            $title = str_replace(' - WebPlatform Docs', '', $title);

            $page_path = explode('/', $title);

            $topic = $page_path[0];
            $pagename = array_pop($page_path);

            $new_title = "$pagename · $topic · WPD · WebPlatform.org";
            $out->setHTMLTitle( $new_title );

            return true;
        }
}
