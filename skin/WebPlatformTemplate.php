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

  protected function t( $str ) {
    return $this->translator->translate( $str );
  }

  protected function init() {
    $navIterator = $this->data['content_navigation'];
    $newNav = array();

    if ( isset( $navIterator['views']['form_edit'] )) {
      $newNav['form_edit'] = $navIterator['views']['form_edit'];
      unset($navIterator['views']['form_edit']);
    }

    if ( isset( $navIterator['views']['edit'] )) {
      $newNav['edit'] = $navIterator['views']['edit'];
      unset($navIterator['views']['edit']);
    }

    if ( in_array('bureaucrat', $this->getSkin()->getUser()->getGroups() ) === false ) {
      unset($newNav['edit']);
    }

    $sb = $this->getSidebar();
    if ( isset( $sb['TOOLBOX']['content']['upload'] ) ) {
      $newNav['upload'] = $sb['TOOLBOX']['content']['upload'];
    }

    $watchMode = $this->getSkin()->getTitle()->userIsWatching() ? 'unwatch' : 'watch';
    if ( isset( $this->nav['actions'][$watchMode] ) ) {
      $newNav[$watchMode] = $this->nav['actions'][$watchMode];
      $newNav[$watchMode]['class'] = rtrim( 'icon ' . $newNav[$watchMode]['class'], ' ' );
      $newNav[$watchMode]['primary'] = true;
    }

    if ( isset( $navIterator['views']['view'] )) {
      unset($navIterator['views']['view']);
    }

    if ( isset( $navIterator['views']['history'] ) ) {
      $navIterator['views']['info'] = $navIterator['views']['history'];
      $navIterator['views']['info']['href'] = str_replace('history', 'info', $navIterator['views']['info']['href']);
      unset($navIterator['views']['rel'], $navIterator['views']['info']['id']);
      $navIterator['views']['info']['text'] = $this->t('pageinfo-toolboxlink');
    }

    $this->nav = array_merge($newNav, $navIterator['views'], $navIterator['actions']);
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
    global $wgArticlePath, $wpdBundle, $wgRightsIcon, $siteTopLevelDomain, $wgWebPlatformAuth;

    $this->init(); // Because its too early at __construct time

    // Suppress warnings to prevent notices about missing indexes in $this->data
    wfSuppressWarnings();
    $this->html('headelement'); ?>
        <div id="mw-page-base" class="noprint"></div>
        <div id="mw-head-base" class="noprint"></div>
        <header id="mw-head" class="noprint">
          <div class="container">
            <!-- logo -->
            <div id="p-logo">
              <a href="//www.<?php echo $siteTopLevelDomain; ?>/" <?php echo Xml::expandAttributes( Linker::tooltipAndAccesskeyAttribs(
              'p-logo' ) ) ?>></a>
            </div><!-- /logo -->
            <?php echo $this->renderHeaderMenu(); ?>
            <?php echo $this->renderSearch(); ?>
          </div>
        </header>
        <nav id="sitenav">
          <div class="container">
            <ul class="links">
              <li><a href="<?php echo htmlspecialchars($this->data['nav_urls']['mainpage']['href']) ?>" class="active">THE DOCS</a></li>
              <li><?php echo Linker::link(Title::newFromText('WPD:Community'), 'CONNECT'); ?></li>
              <li><?php echo Linker::link(Title::newFromText('WPD:Contributors_Guide'), 'CONTRIBUTE'); ?></li>
              <li><a href="//blog.<?php echo $siteTopLevelDomain; ?>/">BLOG</a></li>
              <li><a href="//project.<?php echo $siteTopLevelDomain; ?>/">ISSUES</a></li>
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
                  <li><a href="//www.<?php echo $siteTopLevelDomain; ?>/">HOME</a></li>
                  <li><a href="<?php echo htmlspecialchars($this->data['nav_urls']['mainpage']['href']) ?>">DOCS</a></li>
                  <?php wfRunHooks('SkinBreadcrumb', array(&$this)); ?>
                </ol>
              </div>
              <div class="toolbar">
                <?php echo $this->renderEditButton(); ?>
                <?php echo $this->renderToolMenu(); ?>
              </div>
            </div>
            <div id="page">
              <div id="page-content">
                <?php /* wfRunHooks( 'SkinTOC', array( &$this ) );*/ ?>
                <div id="main-content">
                  <div id="mw-js-message" style="display:none;" <?php $this->html('userlangattributes'); ?>></div>
                  <?php if ($this->data['sitenotice']): ?>
                  <div id="siteNotice">
                    <?php $this->html( 'sitenotice' ) ?></div>
                  <!-- /sitenotice -->
                  <?php endif; ?>
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
              <a href="//www.<?php echo $siteTopLevelDomain; ?>/"><span id="footer-title">WebPlatform<span id="footer-title-light">.org</span></span></a>
            </div>
            <ul class="stewards">
              <li class="steward-w3c"><a href="//www.<?php echo $siteTopLevelDomain; ?>/stewards/w3c">W3C</a></li>
              <li class="steward-adobe"><a href="//www.<?php echo $siteTopLevelDomain; ?>/stewards/adobe">Adobe</a></li>
              <li class="steward-facebook"><a href="//www.<?php echo $siteTopLevelDomain; ?>/stewards/facebook">facebook</a></li>
              <li class="steward-google"><a href="//www.<?php echo $siteTopLevelDomain; ?>/stewards/google">Google</a></li>
              <li class="steward-hp"><a href="//www.<?php echo $siteTopLevelDomain; ?>/stewards/hp">HP</a></li>
              <li class="steward-intel"><a href="//www.<?php echo $siteTopLevelDomain; ?>/stewards/intel">Intel</a></li>
              <li class="steward-microsoft"><a href="//www.<?php echo $siteTopLevelDomain; ?>/stewards/microsoft">Microsoft</a></li>
              <li class="steward-mozilla"><a href="//www.<?php echo $siteTopLevelDomain; ?>/stewards/mozilla">Mozilla</a></li>
              <li class="steward-nokia"><a href="//www.<?php echo $siteTopLevelDomain; ?>/stewards/nokia">Nokia</a></li>
              <li class="steward-opera"><a href="//www.<?php echo $siteTopLevelDomain; ?>/stewards/opera">Opera</a></li>
            </ul>
          </div>
        </footer>
        <!-- /footer -->
<?php if(isset($wgWebPlatformAuth['client'])) { ?>
        <script>
        $.ajax({
          url: 'https://notes.<?php echo $siteTopLevelDomain; ?>/embed.js',
          dataType: 'script',
          cache: true
        });
        </script>
<?php }

    $this->printTrail();
    echo Html::closeElement( 'body' );
    echo Html::closeElement( 'html' );
    wfRestoreWarnings();

  } // end of execute() method


  /*************************************************************************************************/


  private function renderHeaderMenu() {
    $tools = $this->getPersonalTools();

    $template = '<div id="p-personal" class="dropdown%s">%s%s</div>';

    $template_arg_1 = ( count( $this->data['personal_urls'] ) == 0 ) ? ' emptyPortlet' : '';
    $template_arg_2 = '%s';
    $template_arg_3 = '<ul class="user-dropdown">%s</ul>';

    $template_arg_2_array = array();
    $template_arg_3_array = array();

    if ( isset( $tools['mytalk'] ) ) {
      unset( $tools['mytalk'] );
    }

    foreach( $tools as $key => $item ) {
      if ( $key === 'userpage' || $key === 'login' ) {
        $link = $item['links'][0];
        $attribs['href'] = $link['href'];
        $attribs['id'] = $link['single-id'];
        if( isset( $link['class'] ) ) {
          $attribs['class'] = $link['class'];
        }
        $template_arg_2_array[] = Html::rawElement( 'a', $attribs, $link['text'] );
        unset( $attribs );
      } else {
        $template_arg_3_array[] = $this->makeListItem( $key, $item );
      }
    }

    $template_arg_2 = sprintf( $template_arg_2, join( '', $template_arg_2_array ) );
    $template_arg_3 = sprintf( $template_arg_3, join( '', $template_arg_3_array ) );

    return sprintf( $template, $template_arg_1, $template_arg_2, $template_arg_3 );

  } // end of renderHeaderMenu() method


  /*************************************************************************************************/


  private function renderToolMenu() {

    $sb    = $this->getSidebar();
    $label = 'Tools';
    $iter  = array();

    $iter[] = '<li><a href="//code.'.$GLOBALS['siteTopLevelDomain'].'/" target="_blank" title="Use this to add code examples">Code sample editor</a></li>';

    if ( isset( $sb['TOOLBOX']['content']['whatlinkshere'] ) ) {
      $iter[] = $this->makeListItem( 'whatlinkshere', $sb['TOOLBOX']['content']['whatlinkshere'] );
    }
    if ( isset( $sb['TOOLBOXEND']['content'] ) ) {
      $iter[] = '<li>' . preg_replace( '#^<ul.+?>|</ul>#', '', $sb['TOOLBOXEND']['content']);
    }
    if ( isset( $sb['TOOLBOX']['content']['recentchangeslinked'] ) ) {
      $iter[] = $this->makeListItem( 'recentchangeslinked', $sb['TOOLBOX']['content']['recentchangeslinked'] );
    }
    if ( isset( $sb['navigation']['content'][3] ) ) {
      $iter[] = $this->makeListItem( 3, $sb['navigation']['content'][3] );
    }
    if ( isset( $sb['TOOLBOX']['content']['specialpages'] ) ) {
      $iter[] = $this->makeListItem( 'specialpages', $sb['TOOLBOX']['content']['specialpages'] );
    }
    if ( isset( $sb['navigation']['content'][5] ) ) {
      $iter[] = $this->makeListItem( 5, $sb['navigation']['content'][5] );
    }

    $listItems = join('', $iter);

    return sprintf('<div class="dropdown"><a class="tools button">%s</a><ul>%s</ul></div>', $label, $listItems);

  } // end of renderToolMenu() method


  /*************************************************************************************************/


  private function renderSearch() {

    $searchLabel = $this->t( 'search' );
    $action      = $this->t( 'wgScript' );

    $inputs      = $this->makeSearchInput( array( 'id' => 'searchInput', 'type' => 'input', 'placeholder' => $searchLabel.'...') );
    $inputs     .= $this->makeSearchButton( 'fulltext', array( 'id' => 'mw-searchButton', 'class' => 'searchButton', 'value' => ' ' ) );

    $searchtitle = $this->t( 'searchtitle' );

    return <<<HEREDOC
      <div id="p-search">
        <form action="{$action}" id="searchform">
          <div id="search">{$inputs}
            <input type='hidden' name="title" value="{$searchtitle}"/>
          </div>
        </form>
      </div>
HEREDOC;

  } // end of renderSearch() method


  /*************************************************************************************************/


  private function renderEditButton() {

    $navItems = $this->nav;
    $templateMenu = '<div class="dropdown">%s<ul>%s</ul></div>';

    $firstLink = array_shift($navItems);
    $firstLink['class'] = 'highlighted edit button';
    $link = Html::rawElement('a', $firstLink, $this->t('edit'));

    $list = array();
    foreach($navItems as $k => $menuItem) {
      $list[] = $this->makeListItem( $k, $menuItem );
    }

    if ( count( $list ) === 0 ) {
      return '';
    }

    return sprintf($templateMenu, $link, join('',$list));

  }

} // end of class
