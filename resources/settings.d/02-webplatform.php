<?php

require_once("$IP/extensions/WebPlatformDocs/main.php");
require_once("$IP/extensions/EventLogging/EventLogging.php");
require_once("$IP/extensions/UserMerge/UserMerge.php");

//Enable ONLY when we want to export them
//Needs fixing before re-enabling
#require_once("$IP/extensions/Comments/Comments.php");
#if ( isset( $wgResourceModules['ext.comments'] ) ) {
#  require_once(__DIR__."/extensions/SectionComments/SectionComments.php");
#}

/*
## See DefaultSettings.php for details on each of them
## wfFixSessionID() in GlobalFunctions
$wgCookieExpiration = 180*86400;
$wgCookieDomain = "";
$wgCookiePath = "/";
$wgCookieSecure = "detect";
$wgDisableCookieCheck = false;
$wgCookiePrefix = false;
$wgCookieHttpOnly = true;
$wgCacheVaryCookies = array();
$wgSessionName = false;
*/

## Upcoming improvements
#$wgDisableOutputCompression = true;
#$wgDBssl = true;
#$wgSecureLogin = true;