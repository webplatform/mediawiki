<?php
$dir = dirname( __FILE__ ).'/';

$wgHooks['addCommentsFormDiv'][] = 'onAddCommentsFormDiv';
$wgHooks['OutputPageBeforeHTML'][] = 'onOutputPageBeforeHtml';

$wgExtensionMessagesFiles['WebplatformSectionCommentsSMW'] = $dir . 'SectionComments.i18n.php';

$wgResourceModules['ext.webplatformSectionCommentsSMW'] = array(
  'scripts' => array('scripts.js'),
  'messages' => array(),
  'dependencies' => array( 'ext.comments' ),
  'localBasePath' => dirname( __FILE__ ),
  'remoteExtPath' => 'WebplatformSectionCommentsSMW',
);

$webplatformSectionCommentsSMW = array(
  "form" => "CSS Property",
  "template" => "Flags"
);

global $wgAPIModules;
$wgAutoloadClasses['ApiWebplatformSectionCommentsSMW'] = $dir."api/ApiWebplatformSectionCommentsSMW.php";
$wgAPIModules['webplatformcommentssmw'] = 'ApiWebplatformSectionCommentsSMW';
/**
 * Just adding our modules
 * @global Parser $wgParser
 * @param OutputPage $out
 * @param String $text
 * @return boolean
 */
function onOutputPageBeforeHtml( &$out, &$text ) {
  $out->addModules( 'ext.webplatformSectionCommentsSMW' );
  return true;
}


/**
 * adding the flag table to the comments form
 *
 * @global SFFormPrinter $sfgFormPrinter from SMW
 * @global Article $wgArticle
 * @param String $sHtml
 * @return boolean
 */
function onAddCommentsFormDiv( &$sHtml ) {
  global $sfgFormPrinter, $wgArticle, $webplatformSectionCommentsSMW;
  $sHtml .= '<a id="comments-flag-link">'.wfMessage('comments-flag-link')->text().'</a>';
  $sHtml .= '<div id="comment-flags">';
  $sFormName = $webplatformSectionCommentsSMW['form'];
  //$sPageName = 'Comments';
  $oTitle = Title::newFromText( $sFormName , SF_NS_FORM );
  $oArticle = new Article( $oTitle, 0 );
  $sFormDefinition = $oArticle->getContent();
  $sFormDefinition = StringUtils::delimiterReplace( '<noinclude>', '</noinclude>', '', $sFormDefinition );
  $aHtml = $sfgFormPrinter->formHTML( $sFormDefinition, false, true, $oTitle->getArticleID(), $wgArticle->fetchContent() );//, $wgArticle->getTitle()->getArticleID(), $wgArticle->fetchContent(), $wgArticle->getTitle()->getText(), null );
  $aMatches = array();
  preg_match_all( '#<table.*?</table>#is', $aHtml[0], $aMatches );
  $index = null;
  foreach( $aMatches[0] as $key => $value ) {
    $bPos = strrpos( $value, $webplatformSectionCommentsSMW['template'].'[' );
    if ( $bPos !== false) {
      $index = $key;
      break;
    }
  }
  $sHtml .= $aMatches[0][$index];
  $sHtml .= '</div>';

  return true;
}
