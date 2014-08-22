<?php

if ( !defined( "MEDIAWIKI" ) ) die( 'This file is a MediaWiki extension, it is not a valid entry point' );

/**
 * Extension now supports defining a Skin, see docs/skin.txt
 */

$wgExtensionCredits["skin"][] = array("path" => __FILE__,
  "name" => "WebPlatform",
  "description" => "[http://docs.webplatform.org/wiki/WPD:Infrastructure/Components/WebPlatformDocsExtensionBundle Part of WebPlatformDocs extension bundle]; WebPlatform.org MediaWiki skin",
  "url" => "http://docs.webplatform.org/wiki/WPD:Infrastructure/Components/WebPlatformDocsExtensionBundle",
  "author" => array("TODO, find authors for credits")
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
    "sso.js"
  ),
  "remoteBasePath" => $wpdBundle["uri"],
  "localBasePath"  => __DIR__
);
