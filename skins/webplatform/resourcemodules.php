<?php
/*
 * Definition of resources (CSS and Javascript) required for this skin.
 * This file must be included from LocalSettings.php since that is the only way
 * that this file is included by loader.php
 */
global $wgResourceModules, $wgStylePath, $wgStyleDirectory;
$wgResourceModules['skins.webplatform'] = array(
	'styles' => array(
		'webplatform/screen.css' => array( 'media' => 'screen' ),
                'webplatform/temporary.css' => array( 'media' => 'screen' ),
                'webplatform/webplatformPrint.css' => array( 'media' => 'print' ),
		'webplatform/screen-950.css' => array( 'media' => 'screen and (max-width: 950px)' ),
		'webplatform/screen-640.css' => array( 'media' => 'screen and (max-width: 705px)' ),
		'webplatform/screen-520.css' => array( 'media' => 'screen and (max-width: 520px)' ),
	),
	'scripts' => array(
    'webplatform/webplatform.js'
	),
	'remoteBasePath' => $wgStylePath,
	'localBasePath' => dirname( dirname( __FILE__ ) ),
);
