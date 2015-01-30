<?php

if ( !defined( "MEDIAWIKI" ) ) die( 'This file is a MediaWiki extension, it is not a valid entry point' );

if (!isset($GLOBALS['siteTopLevelDomain'])) {
  $GLOBALS['siteTopLevelDomain'] = 'webplatform.org';
}

$wpdBundle["path"]     = __DIR__;
$wpdBundle["root_uri"] = str_replace('$1', '', $wgArticlePath);
$wpdBundle["context"]  = basename(dirname(__FILE__));
$wpdBundle["uri"]      = "{$wgScriptPath}/extensions/".$wpdBundle["context"];

## Very-small extensions
require_once(__DIR__."/extensions/TopicTitle.php");
require_once(__DIR__."/extensions/EditSectionIcon.php");
require_once(__DIR__."/extensions/NoTOC.php");
require_once(__DIR__."/extensions/BreadcrumbMenu.php");

## Piwik
require_once(__DIR__."/extensions/Piwik.php");
$wgPiwikURL = 'stats.'.$GLOBALS['siteTopLevelDomain'];

## Compatibility tables
require_once(__DIR__."/extensions/Compatables/Compatables.php");
$wgCompatablesCssFileUrl = $wpdBundle["uri"]."/extensions/Compatables/compat.css";

## The skin
require_once(__DIR__."/skin/main.php");

## Remove normal table of contents
## $wgDefaultUserOptions["showtoc"] = 0;

// Defaults is that we do not want to be indexed
if(!isset($wgDefaultRobotPolicy)){
  $wgDefaultRobotPolicy = 'noindex,nofollow';
}

$wgCrossSiteAJAXdomains = "*";

$wgStylePath = "{$wgScriptPath}/skins";
$wgFavicon = "/favicon.ico";

## The relative URL path to the logo.  Make sure you change
## this from the default, or else you’ll overwrite your
## logo when you upgrade!
$wgLogo = $wpdBundle["uri"]."/skin/logo.png";

## Custom Namespaces
define("NS_WPD", 3000);
define("NS_WPD_TALK", 3001);
define("NS_STEWARDS", 3010);
define("NS_STEWARDS_TALK", 3011);
define("NS_META", 3020);
define("NS_META_TALK", 3021);

$wgEmergencyContact = "notifier-docs@".$GLOBALS['siteTopLevelDomain'];
$wgPasswordSender   = "notifier-docs@".$GLOBALS['siteTopLevelDomain'];

$wgExtraNamespaces[NS_WPD] = "WPD";
$wgExtraNamespaces[NS_WPD_TALK] = "WPD_talk";
$wgExtraNamespaces[NS_STEWARDS] = "Stewards";
$wgExtraNamespaces[NS_STEWARDS_TALK] = "Stewards_talk";
$wgExtraNamespaces[NS_META] = "Meta";
$wgExtraNamespaces[NS_META_TALK] = "Meta_talk";

## Subpages
$wgNamespacesWithSubpages[NS_MAIN] = true;
$wgNamespacesWithSubpages[NS_WPD] = true;
$wgNamespacesWithSubpages[NS_STEWARDS] = true;
$wgNamespacesWithSubpages[NS_META] = true;

$wgContentNamespaces[] = NS_MAIN;
$wgContentNamespaces[] = NS_WPD;

# https://www.mediawiki.org/wiki/Manual:$wgDefaultRobotPolicy
#$wgNamespaceRobotPolicies[NS_WPD_TALK] = 'noindex,nofollow';
#$wgNamespaceRobotPolicies[NS_META] = 'noindex,nofollow';
#$wgNamespaceRobotPolicies[NS_META_TALK] = 'noindex,nofollow';

## Allow lowercase page titles
$wgCapitalLinks = false;

## Allow users to read the request account page, so they can request accounts
$wgWhitelistRead = array("Special:RequestAccount","Main Page");

## General rights
$wgGroupPermissions["*"]["read"] = true;
$wgGroupPermissions["*"]["createaccount"] = true;
$wgGroupPermissions["*"]["createpage"] = false;
$wgGroupPermissions["*"]["createtalk"] = false;    // New!
$wgGroupPermissions["user"]["createtalk"] = false; // ^
$wgGroupPermissions["*"]["edit"] = false;
$wgGroupPermissions["user"]["createpage"] = false;
$wgGroupPermissions["autoconfirmed"]["createpage"] = true;
$wgAutoConfirmAge = 1*3600*24; // 1 day
$wgAutoConfirmCount = 10;

$wgRCMaxAge = 365*24*3600; // 10 years


## UPO means: this is also a user preference option
$wgShowIPinHeader      = false;
$wgDisableCounters     = true;
$wgAllowUserCss        = false;
$wgAllowUserJs         = false;
$wgEnotifUserTalk      = true;  // UPO
$wgEnotifWatchlist     = true;  // UPO
$wgEmailAuthentication = true;
$wgEmailConfirmToEdit  = true;
$wgAllowUserCssPrefs   = false;
$wgUseSiteJs           = false;
$wgUseSiteCss          = false;
$wgUseAjax             = true;
$wgAjaxWatch           = true;
$wgMiserMode           = true;

## Jobs are run by cron, disable jobs run via page requests
$wgJobRunRate = 0;

## Why that, don’t know, still better than .php3, or .php5
$wgScriptExtension = ".php";

## MySQL specific settings
$wgDBprefix         = "";

## MySQL table options to use during installation or update
$wgDBTableOptions   = "ENGINE=InnoDB, DEFAULT CHARSET=binary";

## Experimental charset support for MySQL 5.0.
$wgDBmysql5 = false;

## InstantCommons allows wiki to use images from http://commons.wikimedia.org
$wgUseInstantCommons  = false;

## If you use ImageMagick (or any other shell command) on a
## Linux server, this will need to be set to the name of an
## available UTF-8 locale
$wgShellLocale = "en_US.utf8";

## Site language code, should be one of the list in ./languages/Names.php
$wgLanguageCode = "en";

## For attaching licensing metadata to pages, and displaying an
## appropriate copyright notice / icon. GNU Free Documentation
## License and Creative Commons licenses are supported so far.
## Set to the title of a wiki page that describes your license/copyright
$wgRightsUrl  = "http://creativecommons.org/licenses/by/3.0/";
$wgRightsText = "Creative Commons Attribution license";
$wgRightsIcon = $wpdBundle["uri"]."/skin/images/cc-by-black.svg";
$wgRightsPage = "MediaWiki:Site-terms-of-service";
$wgLicenseTerms = "MediaWiki:Site-terms-of-service";

## TODO:
## * Import comments; it only requires the Comments extension
## * Remove SectionComments from this extension, it should not be enabled anymore (doesn’t work since update 2014-09-01)
## * Remove .gitmodules requirement for Comments Extension
#require_once("$IP/extensions/Comments/Comments.php");
#$wgCommentsEnabledNS = array(NS_MAIN);
#require_once("$IP/extensions/WebPlatformDocs/extensions/SectionComments/SectionComments.php");
