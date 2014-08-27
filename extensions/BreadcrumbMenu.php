<?php

/**
 * Create breadcrumbs based on subpage hierarchy, with dropdown menus for child pages at each level
 *
 * This extension is loosely based on the SubPageList3 extension by James McCormack;
 *
 * See: http://www.mediawiki.org/wiki/Extension:SubPageList3
 *
 * @author Doug Schepers (http://schepers.cc)
 */

if ( !defined( 'MEDIAWIKI' ) ) die( 'This file is a MediaWiki extension, it is not a valid entry point' );

$wgExtensionCredits['other'][] = array(
   'path'        => __FILE__,
   'name'        => 'Breadcrumb Menu',
   'version'     => '0.1',
   'author'      => '[http://schepers.cc Doug Schepers]',
   'url'         => 'http://docs.webplatform.org/wiki/WPD:Infrastructure/Components/WebPlatformDocsExtensionBundle',
   'description' => '[http://docs.webplatform.org/wiki/WPD:Infrastructure/Components/WebPlatformDocsExtensionBundle WebPlatform Docs MediaWiki Extension bundle]; Generates breadcrumbs on the top of every pages'
);

$wgHooks['SkinBreadcrumb'][] = 'BreadcrumbMenu::fnDisplay';
$wgHooks['BeforePageDisplay'][] = 'BreadcrumbMenu::fnSetOutput';

class BreadcrumbMenu {
   public static $menuhtml = false;

   public static function fnDisplay( $skin ) {
      echo self::$menuhtml;
      return true;
   }

   public static function fnSetOutput( OutputPage &$out, Skin &$skin ) {
      global $wpdBundle;

      $page_path = explode('/', $skin->getTitle());

      for ($pp = 1; count($page_path) >= $pp; $pp++) {
         $path_part = implode('/', array_slice($page_path, 0, $pp));

         self::$menuhtml .=  '<li data-comment="Not removing underscore here"><a href="'.$wpdBundle['root_uri'].str_replace(' ','_', $path_part).'">'.str_replace('_', ' ',$page_path[$pp - 1]).'</a></li>';
      }

      return true;
   }
}
