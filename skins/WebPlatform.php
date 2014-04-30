<?php
/**
 * WebPlatform - Modern version of MonoBook with fresh look and many usability
 * improvements.
 *
 * @todo document
 * @file
 * @ingroup Skins
 */
if( !defined( 'MEDIAWIKI' ) ) {
	die( -1 );
}

/**
 * SkinTemplate class for Vector skin
 * @ingroup Skins
 */
class SkinWebPlatform extends SkinTemplate {
	var $skinname = 'webplatform',
		 $stylename = 'webplatform',
		 $template = 'WebPlatformTemplate',
		 $useHeadElement = true;
	/**
	 * Initializes output page and sets up skin-specific parameters
	 * @param $out OutputPage object to initialize
	 */
	public function initPage( OutputPage $out ) {
		global $wgLocalStylePath;
		parent::initPage( $out );
		// Append CSS which includes IE only behavior fixes for hover support -
		// this is better than including this in a CSS fille since it doesn't
		// wait for the CSS file to load before fetching the HTC file.
		$min = $this->getRequest()->getFuzzyBool( 'debug' ) ? '' : '.min';
		$out->addHeadItem( 'csshover',
					'<!--[if lt IE 7]><style type="text/css">body{behavior:url("' .
						htmlspecialchars( $wgLocalStylePath ) .
						"/{$this->stylename}/csshover{$min}.htc\")}</style><![endif]-->"
						);
		$out->addHeadItem('ie compatibility', '<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">');
		$out->addHeadItem('html5shiv', '<!--[if lt IE 9]><script src="http://docs.webplatform.org/w/skins/webplatform/html5shiv.js"></script><![endif]-->');
		$out->addHeadItem('viewport', '<meta name="viewport" content="width=device-width">');
		#$out->addHeadItem('ie8CSS', '<!--[if lt IE 9]><link rel="stylesheet" href="http://docs.webplatform.org/w/skins/webplatform/ie8.css"><![endif]-->');
		$out->addHeadItem('ie7CSS', '<!--[if lt IE 8]><link rel="stylesheet" href="http://docs.webplatform.org/w/skins/webplatform/ie7.css"><![endif]-->');
		$out->addModuleScripts( 'skins.webplatform' );
	}

	/**
	 * Load skin and user CSS files in the correct order
	 * fixes bug 22916
	 * @param $out OutputPage object
	 */
	function setupSkinUserCss( OutputPage $out ) {
		parent::setupSkinUserCss( $out );
		$out->addModuleStyles( 'skins.webplatform' );
	}
}
/**
 * QuickTemplate class for Vector skin
 * @ingroup Skins
 */
class WebPlatformTemplate extends BaseTemplate {
	/* Functions */
	/**
	 * Outputs the entire contents of the (X)HTML page
	 */
	public function execute() {

		global $wgVectorUseIconWatch;
		// Build additional attributes for navigation urls
		$nav = $this->data['content_navigation'];
		if ( $wgVectorUseIconWatch ) {
			$mode = $this->getSkin()->getTitle()->userIsWatching() ? 'unwatch' : 'watch';
			if ( isset( $nav['actions'][$mode] ) ) {
				$nav['views'][$mode] = $nav['actions'][$mode];
				$nav['views'][$mode]['class'] = rtrim( 'icon ' . $nav['views'][$mode]['class'], ' ' );
				$nav['views'][$mode]['primary'] = true;
				unset( $nav['actions'][$mode] );
			}
		}

		$xmlID = '';
		foreach ( $nav as $section => $links ) {
			foreach ( $links as $key => $link ) {
				if ( $section == 'views' && !( isset( $link['primary'] ) && $link['primary'] ) ) {
					$link['class'] = rtrim( 'collapsible ' . $link['class'], ' ' );
				}
				$xmlID = isset( $link['id'] ) ? $link['id'] : 'ca-' . $xmlID;
				$nav[$section][$key]['attributes'] =
									' id="' . Sanitizer::escapeId( $xmlID ) . '"';
				if ( $link['class'] ) {
					$nav[$section][$key]['attributes'] .=
											' class="' . htmlspecialchars( $link['class'] ) . '"';
					unset( $nav[$section][$key]['class'] );
				}
				if ( isset( $link['tooltiponly'] ) && $link['tooltiponly'] ) {
					$nav[$section][$key]['key'] =
											Linker::tooltip( $xmlID );
				} else {
					$nav[$section][$key]['key'] =
											Xml::expandAttributes( Linker::tooltipAndAccesskeyAttribs( $xmlID ) );
				}
			}
		}
		$this->data['namespace_urls'] = $nav['namespaces'];
		$this->data['view_urls'] = $nav['views'];
		$this->data['action_urls'] = $nav['actions'];
		$this->data['variant_urls'] = $nav['variants'];
		// Reverse horizontally rendered navigation elements
		if ( $this->data['rtl'] ) {
			$this->data['view_urls'] =
							array_reverse( $this->data['view_urls'] );
			$this->data['namespace_urls'] =
							array_reverse( $this->data['namespace_urls'] );
			$this->data['personal_urls'] =
							array_reverse( $this->data['personal_urls'] );
		}

		// Output HTML Page
		$this->html( 'headelement' ); ?>
			<div id="mw-page-base" class="noprint"></div>
				<div id="mw-head-base" class="noprint"></div>

				<!-- header -->
				<header id="mw-head" class="noprint">
					<div class="container">

						<!-- logo -->
						<div id="p-logo">
							<a href="//www.webplatform.org/" <?php echo Xml::expandAttributes( Linker::tooltipAndAccesskeyAttribs( 'p-logo' ) ) ?>></a>
						</div>
						<!-- /logo -->

						<?php $this->renderHeaderMenu(); ?>
						<?php $this->renderSearch(); ?>
					</div>
				</header>
				<!-- /header -->

				<nav id="sitenav">
				<div class="container">
          <ul class="links">
            <li><a href="<?php echo htmlspecialchars( $this->data['nav_urls']['mainpage']['href'] ) ?>" class="active">The Docs</a></li>
            <li><?php echo Linker::link( Title::newFromText('WPD:Community'), 'Connect'); ?></li>
            <li><?php echo Linker::link( Title::newFromText('WPD:Contributors_Guide'), 'Contribute'); ?></li>
            <li><a href="http://blog.webplatform.org/">BLOG</a></li>
            <li><a href="http://project.webplatform.org/">ISSUES</a></li>
					</ul>
				</div>
				</nav>

				<!-- content -->
				<div id="content" class="mw-body">
					<div class="container">
						<a id="top"></a>
						<div class="tool-area">
							<div id="hierarchy-menu">
								<ol id="breadcrumb-info" class="breadcrumbs">
									<li><a href="http://www.webplatform.org/">HOME</a></li>
									<li><a href="<?php echo htmlspecialchars( $this->data['nav_urls']['mainpage']['href'] ) ?>">DOCS</a></li>
									<?php wfRunHooks( 'SkinBreadcrumb', array( &$this ) ); ?>
								</ol>
							</div>

							<div class="toolbar">
								<?php $this->renderEditButton(); ?>
								<?php $this->renderWatchButton(); ?>
								<?php $this->renderToolMenu(); ?>
							</div>
						</div>
						<div id="page">
						  <div id="page-content">
								<?php /* wfRunHooks( 'SkinTOC', array( &$this ) );*/ ?>

								<div id="main-content">
									 <div id="mw-js-message" style="display:none;"<?php $this->html( 'userlangattributes' ) ?>></div>
									 <?php if ( $this->data['sitenotice'] ): ?>
									 <!-- sitenotice -->
									 <div id="siteNotice"><?php $this->html( 'sitenotice' ) ?></div>
									 <!-- /sitenotice -->
									 <?php endif; ?>
									 <!-- firstHeading -->
									 <h1 id="firstHeading" class="firstHeading"><span dir="auto"><?php echo basename($this->getSkin()->getTitle()) ?></span></h1>
									 <!-- /firstHeading -->

									 <!--  -->

									 <!-- subtitle -->
									 <div id="contentSub"<?php $this->html( 'userlangattributes' ) ?>><?php $this->html( 'subtitle' ) ?></div>
									 <!-- /subtitle -->

									 <!-- bodyContent -->
									 <div id="bodyContent">

										  <?php if ( $this->data['undelete'] ): ?>
										  <!-- undelete -->
										  <div id="contentSub2"><?php $this->html( 'undelete' ) ?></div>
										  <!-- /undelete -->
										  <?php endif; ?>

										  <?php if( $this->data['newtalk'] ): ?>
										  <!-- newtalk -->
										  <div class="usermessage"><?php $this->html( 'newtalk' )  ?></div>
										  <!-- /newtalk -->
										  <?php endif; ?>

										  <?php if ( $this->data['showjumplinks'] ): ?>
										  <!-- jumpto -->
										  <div id="jump-to-nav" class="mw-jump">
												<?php $this->msg( 'jumpto' ) ?> <a href="#mw-head"><?php $this->msg( 'jumptonavigation' ) ?></a>,
												<a href="#p-search"><?php $this->msg( 'jumptosearch' ) ?></a>
										  </div>
										  <!-- /jumpto -->
										  <?php endif; ?>


										  <!-- bodycontent -->
										  <?php $this->html( 'bodycontent' ) ?>
										  <!-- /bodycontent -->


										  <?php if ( $this->data['printfooter'] ): ?>
										  <!-- printfooter -->
										  <div class="printfooter">
										  <?php $this->html( 'printfooter' ); ?>
										  </div>
										  <!-- /printfooter -->
										  <?php endif; ?>

										  <?php if ( $this->data['catlinks'] ): ?>
										  <!-- catlinks -->
										  <?php $this->html( 'catlinks' ); ?>
										  <!-- /catlinks -->
										  <?php endif; ?>

										  <?php if ( $this->data['dataAfterContent'] ): ?>
										  <!-- dataAfterContent -->
										  <?php $this->html( 'dataAfterContent' ); ?>
										  <!-- /dataAfterContent -->
										  <?php endif; ?>

										  <div class="visualClear"></div>

										  <!-- debughtml -->
										  <?php $this->html( 'debughtml' ); ?>
										  <!-- /debughtml -->
									 </div>
									 <!-- /bodyContent -->
								</div>

								<div class="topics-nav">
										<ul>
											<li><a href="/wiki/beginners">Beginners</a></li>
											<li><a href="/wiki/concepts">Concepts</a></li>
											<li><a href="/wiki/html">HTML</a></li>
											<li><a href="/wiki/css">CSS</a></li>
											<li><a href="/wiki/concepts/accessibility">Accessibility</a></li>
											<li><a href="/wiki/javascript">JavaScript</a></li>
											<li><a href="/wiki/dom">DOM</a></li>
											<li><a href="/wiki/svg">SVG</a></li>
										</ul>
								</div>

								<!-- /main content -->
								<div class="clear"></div>
						  </div>
						  <!-- /page content -->
					 </div>
					 <!-- /page -->
				</div>
				<!-- /container -->
		</div>
		<!-- /content -->

		<!-- footer -->
		<footer id="mw-footer"<?php $this->html( 'userlangattributes' ) ?>>
		  <div class="container">
			 <div id="footer-wordmark">
				<a href="/wiki/Template:CC-by-3.0" class="license">
				  <img src="/w/skins/webplatform/images/cc-by-black.svg" width="120" height="42"
						 alt="Content available under CC-BY, except where otherwise noted.">
				</a>

				<a href="http://www.webplatform.org/"><span id="footer-title">WebPlatform<span id="footer-title-light">.org</span></span></a>

			 </div>


				<ul class="stewards">
					<li class="steward-w3c"><a href="http://www.webplatform.org/stewards/w3c">W3C</a></li>
					<li class="steward-adobe"><a href="http://www.webplatform.org/stewards/adobe">Adobe</a></li>
					<li class="steward-facebook"><a href="http://www.webplatform.org/stewards/facebook">facebook</a></li>
					<li class="steward-google"><a href="http://www.webplatform.org/stewards/google">Google</a></li>
					<li class="steward-hp"><a href="http://www.webplatform.org/stewards/hp">HP</a></li>

					<li class="steward-intel"><a href="http://www.webplatform.org/stewards/intel">Intel</a></li>
					<li class="steward-microsoft"><a href="http://www.webplatform.org/stewards/microsoft">Microsoft</a></li>
					<li class="steward-mozilla"><a href="http://www.webplatform.org/stewards/mozilla">Mozilla</a></li>
					<li class="steward-nokia"><a href="http://www.webplatform.org/stewards/nokia">Nokia</a></li>
					<li class="steward-opera"><a href="http://www.webplatform.org/stewards/opera">Opera</a></li>
				</ul>
			</div>
		</footer>
		<!-- /footer -->
		<?php $this->printTrail(); ?>
	</body>
	</html>
<?php
}
private function renderHeaderMenu() {
?>
	<div id="p-personal" class="dropdown <?php if ( count( $this->data['personal_urls'] ) == 0 ) echo ' emptyPortlet'; ?>">
		<?php
			foreach( $this->getPersonalTools() as $key => $item ) {
				if ($key == 'userpage' || $key == 'login') {
					$link = $item['links'][0];
					echo "<a href='$link[href]' class='$link[class]' id='{$link['single-id']}'>$link[text]</a>";
				}
			}
		?>
	  <ul class="user-dropdown">
		  <?php
			  foreach( $this->getPersonalTools() as $key => $item ) {
				  if ($key !== 'userpage' && $key !== 'login') {
					  echo $this->makeListItem( $key, $item );
				  }
			  }
		  ?>
	  </ul>
	</div>
<?php
}

private function renderWatchButton() {
	if (isset($this->data['action_urls']['watch'])) {
		$link = $this->data['action_urls']['watch'];
	}
	else if (isset($this->data['action_urls']['unwatch'])) {
		$link = $this->data['action_urls']['unwatch'];
	}
	else {
		return;
	}

	$pt = $this->data['personal_urls'];
?>
	<div class="dropdown">
		<a href="<?php echo htmlspecialchars( $link['href'] ) ?>"
			<?php $this->html( 'userlangattributes' ) ?>
			<?php echo $link['key'] ?>
			<?php
				if (strpos($link['attributes'], 'class=') > 0) {
					echo str_replace('class="', 'class="watch button ', $link['attributes']);
				}
				else {
					echo 'class="watch button"';
				}
			?>>
			<?php echo htmlspecialchars( $link['text'] ) ?>
		</a>
		<ul>
			<?php
			if (isset($pt['watchlist']['href'])) { echo $this->makeListItem( 'href', $pt['watchlist'] ); }
			?>
		</ul>
	</div>
<?
}

private function renderEditButton() {
	 $cn = $this->data['content_navigation'];
	 $sb = $this->getSidebar();
	 if (isset($this->data['view_urls']['edit'])) {
		$link = $this->data['view_urls']['edit'];

		if (isset($this->data['view_urls']['form_edit'])) {
			$link = $this->data['view_urls']['form_edit'];
		}
	 } else {
		$link = array( "href" => "/wiki/Special:UserLogin", "id" => "ca-edit", "text" => "Edit");
	 }
  ?>
	<div class="dropdown">
		<a href="<?php echo $link['href'] ?>" id="<?php echo $link['id'] ?>" class="highlighted edit button">
		 	<?php echo $link['text'] ?>
		</a>
		<ul>
			<?php
			if (isset($cn['views']['form_edit'])) { echo $this->makeListItem( 'form_edit', $cn['views']['form_edit'] ); }
			if (isset($cn['views']['edit'])) { echo $this->makeListItem( 'edit', $cn['views']['edit'] ); }
			if (isset($sb['TOOLBOX']['content']['upload'])) { echo $this->makeListItem( 'upload', $sb['TOOLBOX']['content']['upload'] ); }
			if (isset($cn['views']['history'])) { echo $this->makeListItem( 'history', $cn['views']['history'] ); }
			if (isset($cn['views']['view'])) { echo $this->makeListItem( 'view', $cn['views']['view'] ); }
			if (isset($cn['actions']['move'])) { echo $this->makeListItem( 'move', $cn['actions']['move'] ); }
		if (isset($cn['actions']['protect'])) { echo $this->makeListItem( 'protect', $cn['actions']['protect'] ); }
		if (isset($cn['actions']['unprotect'])) { echo $this->makeListItem( 'unprotect', $cn['actions']['unprotect'] ); }
		if (isset($cn['actions']['delete'])) { echo $this->makeListItem( 'delete', $cn['actions']['delete'] ); }
		if (isset($cn['actions']['purge'])) { echo $this->makeListItem( 'purge', $cn['actions']['purge'] ); }
			?>
		</ul>
	</div>
<?php
}

private function renderToolMenu() {
	$cn = $this->data['content_navigation'];
	$sb = $this->getSidebar();
?>
	<div class="dropdown">
		<a class="tools button">
		 	Tools
		</a>
		<ul>
			<?php
			if (isset($sb['TOOLBOX']['content']['whatlinkshere'])) { echo $this->makeListItem( 'whatlinkshere', $sb['TOOLBOX']['content']['whatlinkshere'] ); }
			if (isset( $sb['TOOLBOXEND']['content'] )) { echo '<li>' . preg_replace('#^<ul.+?>|</ul>#', '', $sb['TOOLBOXEND']['content']); }
			if (isset($sb['TOOLBOX']['content']['recentchangeslinked'])) { echo $this->makeListItem( 'recentchangeslinked', $sb['TOOLBOX']['content']['recentchangeslinked'] ); }
			if (isset($sb['navigation']['content'][3])) { echo $this->makeListItem( 3, $sb['navigation']['content'][3] ); }
			if (isset($sb['TOOLBOX']['content']['specialpages'])) { echo $this->makeListItem( 'specialpages', $sb['TOOLBOX']['content']['specialpages'] ); }
			if (isset($cn['namespaces']['talk'])) { echo $this->makeListItem( 'talk', $cn['namespaces']['talk'] ); }
			if (isset($sb['navigation']['content'][5])) { echo $this->makeListItem( 5, $sb['navigation']['content'][5] ); }
			?>
		</ul>
	</div>
<?php
}

	/**
	 * Render one or more navigations elements by name, automatically reveresed
	 * when UI is in RTL mode
	 *
	 * @param $elements array
	 */

	private function renderSearch() {
	?>
		<div id="p-search">
			<h5<?php $this->html( 'userlangattributes' ) ?>><label for="searchInput"><?php $this->msg( 'search' ) ?></label></h5>
			<form action="<?php $this->text( 'wgScript' ) ?>" id="searchform">
				<div id="search">
					<?php echo $this->makeSearchInput( array( 'id' => 'searchInput', 'type' => 'input', 'placeholder' => 'Search...')); ?>
					<?php echo $this->makeSearchButton( 'fulltext', array( 'id' => 'mw-searchButton', 'class' => 'searchButton', 'value' => ' ' ) ); ?>
					<input type='hidden' name="title" value="<?php $this->text( 'searchtitle' ) ?>"/>
				</div>
			</form>
		</div>
	<?php
		echo "\n<!-- /search -->\n";
	}
}
