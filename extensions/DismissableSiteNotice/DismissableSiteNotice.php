<?php
if ( !defined( 'MEDIAWIKI' ) ) die();

$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'DismissableSiteNotice',
	'author' => array(
		'Brion Vibber',
		'Kevin Israel',
	),
	'descriptionmsg' => 'sitenotice-desc',
	'url' => 'https://www.mediawiki.org/wiki/Extension:DismissableSiteNotice',
);

$wgMessagesDirs['DismissableSiteNotice'] = __DIR__ . '/i18n';
$wgExtensionMessagesFiles['DismissableSiteNotice'] = __DIR__ . '/DismissableSiteNotice.i18n.php';

$wgResourceModules['ext.dismissableSiteNotice'] = array(
	'localBasePath' => __DIR__ . '/modules',
	'remoteExtPath' => 'DismissableSiteNotice/modules',
	'scripts' => 'ext.dismissableSiteNotice.js',
	'styles' => 'ext.dismissableSiteNotice.css',
	'dependencies' => array(
		'jquery.cookie',
		'mediawiki.util',
	),
	'targets' => array( 'desktop', 'mobile' ),
	'position' => 'top',
);

/**
 * @param string $notice
 * @param Skin $skin
 * @return bool true
 */
$wgHooks['SiteNoticeAfter'][] = function( &$notice, $skin ) {
	global $wgMajorSiteNoticeID;

	if ( !$notice ) {
		return true;
	}

	// No dismissal for anons
	if ( $skin->getUser()->isAnon() ) {
		// Hide the sitenotice from search engines (see bug 9209 comment 4)
		// XXX: Does this actually work?
		$notice = Html::inlineScript( Xml::encodeJsCall( 'document.write', array( $notice ) ) );
		return true;
	}

	// Cookie value consists of two parts
	$major = (int) $wgMajorSiteNoticeID;
	$minor = (int) $skin->msg( 'sitenotice_id' )->inContentLanguage()->text();

	$out = $skin->getOutput();
	$out->addModules( 'ext.dismissableSiteNotice' );
	$out->addJsConfigVars( 'wgSiteNoticeId', "$major.$minor" );

	$notice = Html::rawElement( 'div', array( 'class' => 'mw-dismissable-notice' ),
		Html::rawElement( 'div', array( 'class' => 'mw-dismissable-notice-close' ),
			$skin->msg( 'sitenotice_close-brackets' )
			->rawParams(
				Html::element( 'a', array( 'href' => '#' ), $skin->msg( 'sitenotice_close' )->text() )
			)
			->escaped()
		) .
		Html::rawElement( 'div', array( 'class' => 'mw-dismissable-notice-body' ), $notice )
	);

	return true;
};

// Default settings
$wgMajorSiteNoticeID = 1;
