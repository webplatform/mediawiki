<?php

if ( !defined( "MEDIAWIKI" ) ) die( 'This file is a MediaWiki extension, it is not a valid entry point' );

/**
 * WebPlatform Docs Page Template
 *
 * @file
 * @ingroup Skins
 */
class WebPlatformTemplate extends BaseTemplate {

  protected $nav;

  protected function init() {
    global $wgVectorUseIconWatch;

    // Build additional attributes for navigation urls
    $this->nav = $this->data['content_navigation'];
    if ( $wgVectorUseIconWatch ) {
      $mode = $this->getSkin()->getTitle()->userIsWatching() ? 'unwatch' : 'watch';
      if ( isset( $this->nav['actions'][$mode] ) ) {
        $this->nav['views'][$mode] = $this->nav['actions'][$mode];
        $this->nav['views'][$mode]['class'] = rtrim( 'icon ' . $this->nav['views'][$mode]['class'], ' ' );
        $this->nav['views'][$mode]['primary'] = true;
        unset( $this->nav['actions'][$mode] );
      }
    }

    $xmlID = '';
    foreach ( $this->nav as $section => $links ) {
      foreach ( $links as $key => $link ) {
        if ( $section == 'views' && !( isset( $link['primary'] ) && $link['primary'] ) ) {
          $link['class'] = rtrim( 'collapsible ' . $link['class'], ' ' );
        }
        $xmlID = isset( $link['id'] ) ? $link['id'] : 'ca-' . $xmlID;
        $this->nav[$section][$key]['attributes'] =
                  ' id="' . Sanitizer::escapeId( $xmlID ) . '"';
        if ( $link['class'] ) {
          $this->nav[$section][$key]['attributes'] .=
                      ' class="' . htmlspecialchars( $link['class'] ) . '"';
          unset( $this->nav[$section][$key]['class'] );
        }
        if ( isset( $link['tooltiponly'] ) && $link['tooltiponly'] ) {
          $this->nav[$section][$key]['key'] =
                      Linker::tooltip( $xmlID );
        } else {
          $this->nav[$section][$key]['key'] =
                      Xml::expandAttributes( Linker::tooltipAndAccesskeyAttribs( $xmlID ) );
        }
      }
    }
    $this->data['namespace_urls'] = $this->nav['namespaces'];
    $this->data['view_urls'] = $this->nav['views'];
    $this->data['action_urls'] = $this->nav['actions'];
    $this->data['variant_urls'] = $this->nav['variants'];
    // Reverse horizontally rendered navigation elements
    if ( $this->data['rtl'] ) {
      $this->data['view_urls'] =
              array_reverse( $this->data['view_urls'] );
      $this->data['namespace_urls'] =
              array_reverse( $this->data['namespace_urls'] );
      $this->data['personal_urls'] =
              array_reverse( $this->data['personal_urls'] );
    }
  }

  /**
   * Template filter callback for MonoBook skin.
   * Takes an associative array of data set from a SkinTemplate-based
   * class, and a wrapper for MediaWiki's localization database, and
   * outputs a formatted page.
   *
   * @access private
   */
  public function execute() {
    global $wgArticlePath, $wpdBundle, $wgRightsIcon;

    // Suppress warnings to prevent notices about missing indexes in $this->data
    wfSuppressWarnings();
    $this->html('headelement'); ?>
        <div id="mw-page-base" class="noprint"></div>
        <div id="mw-head-base" class="noprint"></div>

        <header id="mw-head" class="noprint">
          <div class="container">
            <!-- logo -->
            <div id="p-logo">
              <a href="//www.webplatform.org/" <?php echo Xml::expandAttributes( Linker::tooltipAndAccesskeyAttribs(
              'p-logo' ) ) ?>></a>
            </div><!-- /logo -->
            <?php $this->renderHeaderMenu(); ?>
            <?php $this->renderSearch(); ?>
          </div>
        </header>
        <nav id="sitenav">
          <div class="container">
            <ul class="links">
              <li>
                <a href="<?php echo htmlspecialchars($this->data['nav_urls']['mainpage']['href']) ?>"
                class="active">THE DOCS</a>
              </li>
              <li><?php echo Linker::link(Title::newFromText('WPD:Community'), 'CONNECT'); ?></li>
              <li><?php echo Linker::link(Title::newFromText('WPD:Contributors_Guide'), 'CONTRIBUTE'); ?></li>
              <li><a href="//blog.webplatform.org/">BLOG</a></li>
              <li><a href="//project.webplatform.org/">ISSUES</a></li>
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
                  <li><a href="//www.webplatform.org/">HOME</a></li>
                  <li><a href="<?php echo htmlspecialchars($this->data['nav_urls']['mainpage']['href']) ?>">DOCS</a></li>
                  <?php wfRunHooks('SkinBreadcrumb', array(&$this)); ?>
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
                  <div id="mw-js-message" style="display:none;" <?php $this->html('userlangattributes'); ?>></div>
                  <?php /* <?php if ($this->data['sitenotice']): ?>
                  <!-- sitenotice -->
                  <div id="siteNotice">
                    <?php $this->html( 'sitenotice' ) ?></div>
                  <!-- /sitenotice -->
                  <?php endif; ?>*/ ?>
                  <!-- firstHeading -->
                  <h1 id="firstHeading" class="firstHeading"><span dir="auto"><?php echo basename($this->getSkin()->getTitle()) ?></span></h1>
                  <!-- /firstHeading -->
                  <!-- subtitle -->
                  <div id="contentSub" <?php $this->html( 'userlangattributes' ) ?>>
                    <?php $this->html( 'subtitle' ) ?></div>
                  <!-- /subtitle -->
                  <!-- bodyContent -->
                  <div id="bodyContent">
                    <?php if ( $this->data['undelete'] ): ?>
                    <!-- undelete -->
                    <div id="contentSub2"><?php $this->html( 'undelete' ) ?></div>
                    <!-- /undelete -->
                    <?php endif; ?>
                    <?php /* if( $this->data['newtalk'] ): ?>
                    <!-- newtalk -->
                    <div class="usermessage"><?php $this->html( 'newtalk' ) ?></div>
                    <!-- /newtalk -->
                    <?php endif; */ ?>
                    <?php if ( $this->data['showjumplinks'] ): ?>
                    <!-- jumpto -->
                    <div id="jump-to-nav" class="mw-jump">
                      <?php $this->msg( 'jumpto' ) ?>
                      <a href="#mw-head"><?php $this->msg( 'jumptonavigation' ) ?></a>,
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
                  </div><!-- /bodyContent -->
                </div>
                <div class="topics-nav">
                  <ul>
                    <li><a href="<?php echo $wpdBundle['root_uri']; ?>beginners">Beginners</a></li>
                    <li><a href="<?php echo $wpdBundle['root_uri']; ?>concepts">Concepts</a></li>
                    <li><a href="<?php echo $wpdBundle['root_uri']; ?>html">HTML</a></li>
                    <li><a href="<?php echo $wpdBundle['root_uri']; ?>css">CSS</a></li>
                    <li><a href="<?php echo $wpdBundle['root_uri']; ?>concepts/accessibility">Accessibility</a></li>
                    <li><a href="<?php echo $wpdBundle['root_uri']; ?>javascript">JavaScript</a></li>
                    <li><a href="<?php echo $wpdBundle['root_uri']; ?>dom">DOM</a></li>
                    <li><a href="<?php echo $wpdBundle['root_uri']; ?>svg">SVG</a></li>
                  </ul>
                </div><!-- /main content -->
                <div class="clear"></div>
              </div><!-- /page content -->
            </div><!-- /page -->
          </div><!-- /container -->
        </div><!-- /content -->
        <!-- footer -->
        <footer id="mw-footer" <?php $this->html( 'userlangattributes' ) ?>>
          <div class="container">
            <div id="footer-wordmark">
              <a href="<?php echo $wpdBundle['root_uri']; ?>Template:CC-by-3.0" class="license">
                <img src="<?php echo $wgRightsIcon; ?>" width="120" height="42" alt="Content available under CC-BY, except where otherwise noted." />
              </a>
              <a href="//www.webplatform.org/"><span id="footer-title">WebPlatform<span id="footer-title-light">.org</span></span></a>
            </div>
            <ul class="stewards">
              <li class="steward-w3c"><a href="//www.webplatform.org/stewards/w3c">W3C</a></li>
              <li class="steward-adobe"><a href="//www.webplatform.org/stewards/adobe">Adobe</a></li>
              <li class="steward-facebook"><a href="//www.webplatform.org/stewards/facebook">facebook</a></li>
              <li class="steward-google"><a href="//www.webplatform.org/stewards/google">Google</a></li>
              <li class="steward-hp"><a href="//www.webplatform.org/stewards/hp">HP</a></li>
              <li class="steward-intel"><a href="//www.webplatform.org/stewards/intel">Intel</a></li>
              <li class="steward-microsoft"><a href="//www.webplatform.org/stewards/microsoft">Microsoft</a></li>
              <li class="steward-mozilla"><a href="//www.webplatform.org/stewards/mozilla">Mozilla</a></li>
              <li class="steward-nokia"><a href="//www.webplatform.org/stewards/nokia">Nokia</a></li>
              <li class="steward-opera"><a href="//www.webplatform.org/stewards/opera">Opera</a></li>
            </ul>
          </div>
        </footer>
        <!-- /footer -->

    <?php
    $this->printTrail();
    echo Html::closeElement( 'body' );
    echo Html::closeElement( 'html' );
    wfRestoreWarnings();
  } // end of execute() method


  /*************************************************************************************************/


  private function renderHeaderMenu() {
    ?><!-- renderHeaderMenu --><div id="p-personal" class="dropdown <?php if ( count( $this->data['personal_urls'] ) == 0 ) echo ' emptyPortlet'; ?>">
      <?php
        foreach( $this->getPersonalTools() as $key => $item ) {
          if ($key == 'userpage' || $key == 'login') {
            $link = $item['links'][0];
            $attribs['href'] = $link['href'];
            $attribs['id'] = $link['single-id'];
            if(isset($link['class'])){
              $attribs['class'] = $link['class'];
            }
            echo Html::rawElement('a', $attribs, $link['text']);
            unset($attribs);
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
    </div><!-- /renderHeaderMenu --><?php
  } // end of renderHeaderMenu() method


  /*************************************************************************************************/


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

    ?><!-- renderWatchButton --><div class="dropdown">
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
    </div><!-- /renderWatchButton --><?
  } // end of renderWatchButton() method


  /*************************************************************************************************/


  private function renderEditButton() {
    global $wpdBundle;

    $cn = $this->data['content_navigation'];
    $sb = $this->getSidebar();

    // The Default edit link
    $link = $cn['views']['edit'];
    $title = $this->getSkin()->getTitle();

    if ($title->quickUserCan( 'edit' ) === true) {
      // If formedit exists, all the better
      if (isset($cn['views']['form_edit'])) {
        $link = $cn['views']['form_edit'];
      }
    } else {
      // Oops, no session, lets ensure we are logged in
      $link = array( "href" => $wpdBundle['root_uri'] . "Special:UserLogin", "id" => "ca-edit", "text" => "Edit");
    }

    ?><!-- renderEditButton --><div class="dropdown">
      <a href="<?php echo $link['href'] ?>" id="<?php echo $link['id'] ?>" class="highlighted edit button">
        <?php echo $link['text'] ?>
      </a>
      <ul><?php
        //if (isset($cn['views']['form_edit'])) { echo $this->makeListItem( 'form_edit', $cn['views']['form_edit'] ); }
        if (isset($cn['views']['edit'])) { echo $this->makeListItem( 'edit', $cn['views']['edit'] ); }
        if (isset($sb['TOOLBOX']['content']['upload'])) { echo $this->makeListItem( 'upload', $sb['TOOLBOX']['content']['upload'] ); }
        if (isset($cn['views']['history'])) { echo $this->makeListItem( 'history', $cn['views']['history'] ); }
        if (isset($cn['views']['view'])) { echo $this->makeListItem( 'view', $cn['views']['view'] ); }
        if (isset($cn['actions']['move'])) { echo $this->makeListItem( 'move', $cn['actions']['move'] ); }
        if (isset($cn['actions']['protect'])) { echo $this->makeListItem( 'protect', $cn['actions']['protect'] ); }
        if (isset($cn['actions']['unprotect'])) { echo $this->makeListItem( 'unprotect', $cn['actions']['unprotect'] ); }
        if (isset($cn['actions']['delete'])) { echo $this->makeListItem( 'delete', $cn['actions']['delete'] ); }
        if (isset($cn['actions']['purge'])) { echo $this->makeListItem( 'purge', $cn['actions']['purge'] ); }
        ?></ul>
    </div><!-- /renderEditButton --><?php
  } // end of renderEditButton() method


  /*************************************************************************************************/


  private function renderToolMenu() {
    $cn = $this->data['content_navigation'];
    $sb = $this->getSidebar();
    ?><!-- renderToolMenu --><div class="dropdown">
      <a class="tools button">Tools</a>
      <ul>
        <li><a href="//code.webplatform.org/" target="_blank" title="Use this to add code examples">Code sample editor</a></li>
        <?php
        if (isset($sb['TOOLBOX']['content']['whatlinkshere'])) { echo $this->makeListItem( 'whatlinkshere', $sb['TOOLBOX']['content']['whatlinkshere'] ); }
        if (isset( $sb['TOOLBOXEND']['content'] )) { echo '<li>' . preg_replace('#^<ul.+?>|</ul>#', '', $sb['TOOLBOXEND']['content']); }
        if (isset($sb['TOOLBOX']['content']['recentchangeslinked'])) { echo $this->makeListItem( 'recentchangeslinked', $sb['TOOLBOX']['content']['recentchangeslinked'] ); }
        if (isset($sb['navigation']['content'][3])) { echo $this->makeListItem( 3, $sb['navigation']['content'][3] ); }
        if (isset($sb['TOOLBOX']['content']['specialpages'])) { echo $this->makeListItem( 'specialpages', $sb['TOOLBOX']['content']['specialpages'] ); }
        if (isset($sb['navigation']['content'][5])) { echo $this->makeListItem( 5, $sb['navigation']['content'][5] ); }
        ?>
      </ul>
    </div><!-- /renderToolMenu --><?php
  } // end of renderToolMenu() method


  /*************************************************************************************************/

  private function renderSearch() {
    ?><!-- renderSearch --><div id="p-search">
      <h5<?php $this->html( 'userlangattributes' ) ?>><label for="searchInput"><?php $this->msg( 'search' ) ?></label></h5>
      <form action="<?php $this->text( 'wgScript' ) ?>" id="searchform">
        <div id="search">
          <?php echo $this->makeSearchInput( array( 'id' => 'searchInput', 'type' => 'input', 'placeholder' => 'Search...')); ?>
          <?php echo $this->makeSearchButton( 'fulltext', array( 'id' => 'mw-searchButton', 'class' => 'searchButton', 'value' => ' ' ) ); ?>
          <input type='hidden' name="title" value="<?php $this->text( 'searchtitle' ) ?>"/>
        </div>
      </form>
    </div><!-- /renderSearch --><?php
  } // end of renderSearch() method


  /*************************************************************************************************/


} // end of class
