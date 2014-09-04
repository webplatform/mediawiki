<?php

if ( !defined( "MEDIAWIKI" ) ) die( 'This file is a MediaWiki extension, it is not a valid entry point' );

/**
 * WebPlatform Skin Class definition
 *
 * @file
 * @ingroup Skins
 */
class SkinWebPlatform extends SkinTemplate {
	public $skinname  = 'webplatform';
	public $stylename = 'WebPlatform';
	public $template  = 'WebPlatformTemplate';
  public $useHeadElement = true;

	/**
	 * Initializes output page and sets up skin-specific parameters
	 * @param $out OutputPage object to initialize
	 */
	public function initPage( OutputPage $out ) {
		global $wpdBundle;

		parent::initPage($out);
		$out->addHeadItem('ie7js-cond-ie7', '<!--[if lt IE 7]><script src="//www.webplatform.org/assets/bower_components/ie7-js/lib/IE7.js"></script><![endif]-->');
		$out->addHeadItem('ie7js-cond-ie8', '<!--[if lt IE 8]><script src="//www.webplatform.org/assets/bower_components/ie7-js/lib/IE8.js"></script><![endif]-->');
		$out->addHeadItem('ie7js-cond-ie9', '<!--[if lt IE 9]><script src="//www.webplatform.org/assets/bower_components/ie7-js/lib/IE9.js"></script><![endif]-->');
		$out->addHeadItem('html5shiv', '<!--[if lt IE 9]>'.$wpdBundle['uri'].'/skin/html5shiv.js"></script><![endif]-->');
		$out->addHeadItem('viewport', '<meta name="viewport" content="width=device-width">');
		#$out->addHeadItem('ie8CSS', '<!--[if lt IE 9]><link rel="stylesheet" href="'.$wpdBundle['uri'].'/skin/ie8.css"><![endif]-->');
		$out->addHeadItem('ie7CSS', '<!--[if lt IE 8]><link rel="stylesheet" href="'.$wpdBundle['uri'].'/skin/ie7.css"><![endif]-->');
		$out->addModuleScripts('skins.webplatform');
	}

	/**
	 * Load skin and user CSS files in the correct order
	 * fixes bug 22916
	 * @param $out OutputPage object
	 */
	function setupSkinUserCss(OutputPage $out) {
		parent::setupSkinUserCss($out);
		$out->addModuleStyles('skins.webplatform');
	}

}
