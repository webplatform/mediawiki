<?php

/**
 * Inserts Piwik script into MediaWiki pages for tracking and adds some stats
 *
 * Source: http://www.mediawiki.org/wiki/Extension:Piwik_Integration
 *
 * @author Isb1009 <isb1009 at gmail dot com>
 * @author DaSch <dasch@daschmedia.de>
 * @author Youri van den Bogert <yvdbogert@archixl.nl>
 * @copyright © 2008-2010 Isb1009
 * @licence GNU General Public Licence 2.0
 */

if ( !defined( 'MEDIAWIKI' ) ) die( 'This file is a MediaWiki extension, it is not a valid entry point' );

$wgExtensionCredits['other'][] = array(
  'path'        => __FILE__,
  'name'        => 'Piwik Integration',
  'author'      => array(
                    'Isb1009',
                    '[http://www.daschmedia.de DaSch]',
                    '[https://github.com/YOUR1 Youri van den Bogert]',
                    '[https://renoirboulanger.com Renoir Boulanger]'
                  ),
  'url'         => 'http://docs.webplatform.org/wiki/WPD:Infrastructure/Components/WebPlatformDocsExtensionBundle',
  'description' => '[http://docs.webplatform.org/wiki/WPD:Infrastructure/Components/WebPlatformDocsExtensionBundle WebPlatform Docs MediaWiki Extension bundle]; Piwik Tracking'
);

$wgHooks['SkinAfterBottomScripts'][]  = 'PiwikHooks::PiwikSetup';


class PiwikHooks {

  /**
   * Initialize the Piwik Hook
   *
   * @param string $skin
   * @param string $text
   * @return bool
   */
  public static function PiwikSetup ($skin, &$text = '')
  {
    $text .= PiwikHooks::AddPiwik( $skin->getTitle() );
    return true;
  }

  /**
   * Add piwik script
   * @param string $title
   * @return string
   */
  public static function AddPiwik ($title) {

    global $wgPiwikIDSite, $wgPiwikURL, $wgPiwikIgnoreSysops,
         $wgPiwikIgnoreBots, $wgUser, $wgScriptPath,
         $wgPiwikCustomJS, $wgPiwikActionName, $wgPiwikUsePageTitle,
         $wgPiwikDisableCookies;

    // Is piwik disabled for bots?
    if ( $wgUser->isAllowed( 'bot' ) && $wgPiwikIgnoreBots ) {
      return "<!-- Piwik extension is disabled for bots -->";
    }

/**
 * Piwik tracking within MediaWiki
 *
 * Load with this, later: mw.loader.using('mw.user',setupTracking);
 *
 * Will help tracking Edit and Save of a page
 **/
    $wgPiwikCustomJS[] = <<<'JS'


          /*
           * ********************** TO BE MOVED, SOON ***********************
           *
           *   Rewrite in progress: https://gist.github.com/renoirb/6929061
           *
           **/

          function getQueryParameterByName(name) {
              name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
              var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
                  results = regex.exec(location.search);
              return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
          }

          if (getQueryParameterByName('pk_campaign').length > 1 && getQueryParameterByName('pk_campaign').match(/DocSprint/)) {
              _paq.push(['setCustomVariable', 2, 'event', getQueryParameterByName('pk_campaign'), 'visit']);
          }

          jQuery('body').on('click', '#main-content #wpSave', function(){
              var title = jQuery(this).attr('title') || null;
              if (typeof title === 'string') {
                  _paq.push(['setCustomVariable', 1, 'label', title, 'page']);
              }
              _paq.push(['trackGoal', 2]); // idGoal=2 > Saves a page
          });

          jQuery('body').on('click', '.tool-area .toolbar .dropdown:first-child a', function(){
              var title = jQuery(this).attr('title') || null;
              if (typeof title === 'string') {
                  _paq.push(['setCustomVariable', 1, 'label', title, 'page']);
              }
              _paq.push(['trackGoal', 1]); // idGoal=1 > Clicks edit page
          });

          /* *********************** /TO BE MOVED, SOON *********************** */


JS;

    // Do NOT fail the attempt if it failed, please.
    try {
      if ( $wgUser->isAnon() === false ) {
        $userName = $wgUser->getName();
        $wgPiwikCustomJS[] = '          _paq.push(["setCustomVariable",1,"username","'.$userName.'", "visit"]);'.PHP_EOL;
      }
    } catch(Exception $e) {  }


    // Ignore Wiki System Operators
    //if ( $wgUser->isAllowed( 'protect' ) && $wgPiwikIgnoreSysops ) {
    //  return "<!-- Piwik tracking is disabled for users with 'protect' rights (i.e., sysops) -->";
    //}

    if($wgPiwikIDSite === 0) {
      return '';
    }

    // Missing configuration parameters
    if ( empty( $wgPiwikIDSite ) || empty( $wgPiwikURL ) ) {
      return "<!-- You need to set the settings for Piwik -->";
    }

    if ( $wgPiwikUsePageTitle ) {
      $wgPiwikPageTitle = $title->getPrefixedText();

      $wgPiwikFinalActionName = $wgPiwikActionName;
      $wgPiwikFinalActionName .= $wgPiwikPageTitle;
    } else {
      $wgPiwikFinalActionName = $wgPiwikActionName;
    }

    // Check if disablecookies flag
    if ($wgPiwikDisableCookies) {
      $disableCookiesStr = PHP_EOL . '  _paq.push(["disableCookies"]);';
    } else $disableCookiesStr = null;

    // Check if we have custom JS
    if (!empty($wgPiwikCustomJS)) {

      // Check if array is given
      // If yes we have multiple lines/variables to declare
      if (is_array($wgPiwikCustomJS)) {

        // Make empty string with a new line
        $customJs = PHP_EOL;

        // Store the lines in the $customJs line
        foreach ($wgPiwikCustomJS as $customJsLine) {
          $customJs .= $customJsLine;
        }

      // CustomJs is string
      } else $customJs = PHP_EOL . $wgPiwikCustomJS;

    // Contents are empty
    } else $customJs = null;

    // Prevent XSS
    $wgPiwikFinalActionName = Xml::encodeJsVar( $wgPiwikFinalActionName );

    // Piwik script
    $script = <<<PIWIK
        <!-- Piwik -->
        <script type="text/javascript">
          var _paq = _paq || [];{$disableCookiesStr}{$customJs}
          _paq.push(['trackPageView']);
          _paq.push(['enableLinkTracking']);
          (function() {
            var u=(("https:" == document.location.protocol) ? "https" : "http") + "://{$wgPiwikURL}/"
            _paq.push(['setTrackerUrl', u+'js/']);
            _paq.push(['setSiteId', {$wgPiwikIDSite}]);
            var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0]; g.type='text/javascript';
            g.defer=true; g.async=true; g.src=u+'js/'; s.parentNode.insertBefore(g,s);
          })();
        </script>
        <!-- End Piwik Code -->
<!-- Piwik Image Tracker -->
<noscript><img src="//{$wgPiwikURL}/piwik.php?idsite={$wgPiwikIDSite}&amp;rec=1" style="border:0" alt="" /></noscript>
<!-- End Piwik -->
PIWIK;

    return $script;

  }
}

if(!isset($wgPiwikIDSite)) {
  $wgPiwikIDSite = "1";
}
if(!isset($wgPiwikURL)) {
  $wgPiwikURL = "stats.".$GLOBALS['siteTopLevelDomain'];
}

$wgPiwikIgnoreSysops = true;
$wgPiwikIgnoreBots = true;
$wgPiwikCustomJS = "";
$wgPiwikUsePageTitle = false;
$wgPiwikActionName = "";
$wgPiwikDisableCookies = false;

