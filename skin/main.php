<?php

if ( !defined( "MEDIAWIKI" ) ) die( 'This file is a MediaWiki extension, it is not a valid entry point' );

/**
 * Extension now supports defining a Skin, see docs/skin.txt
 */

$wgExtensionCredits["skin"][] = array(
  "path" => __FILE__,
  "name" => "WebPlatform",
  "version" => "1.1",
  "description" => "[http://docs.webplatform.org/wiki/WPD:Infrastructure/Components/WebPlatformDocsExtensionBundle WebPlatform Docs extension bundle]; WebPlatform.org MediaWiki skin",
  "url" => "http://docs.webplatform.org/wiki/WPD:Infrastructure/Components/WebPlatformDocsExtensionBundle",
  "author" => array(
    "[https://renoirboulanger.com Renoir Boulanger]",
    "TODO, find authors for credits"
  )
);

// Register files
$wgAutoloadClasses["SkinWebPlatform"] = __DIR__."/SkinWebPlatform.php";
$wgAutoloadClasses["WebPlatformTemplate"] = __DIR__."/WebPlatformTemplate.php";

// Register skin
$wgValidSkinNames["webplatform"] = "WebPlatform";
$wgDefaultSkin = "webplatform";
$wgHiddenPrefs[] = "skin";

// Register modules
$wgResourceModules["skins.webplatform"] = array(
  "styles" => array(
    "screen.css" => array("media" => "screen"),
    "temporary.css" => array("media" => "screen"),
    "webplatformPrint.css" => array("media" => "print"),
    "screen-950.css" => array("media" => "screen and (max-width: 950px)"),
    "screen-640.css" => array("media" => "screen and (max-width: 705px)"),
    "screen-520.css" => array("media" => "screen and (max-width: 520px)")
  ),
  "scripts" => array(
    "prism.js",
    "webplatform.js",
    //"tracking.js",
    //"sso.js"  #RBx
  ),
  "remoteBasePath" => $wpdBundle["uri"],
  "localBasePath"  => __DIR__
);

/*
if(isset($wgWebPlatformAuth['client'])) {
  $wgResourceModules["skins.webplatform"]["scripts"][] = "sso.js"; # RBx
}
*/