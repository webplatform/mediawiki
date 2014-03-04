<?php

// Hopefully, for memory sake, we
// only need two classes here.
//
// Would be better to use an
// UniversalClassLoader if we need
// to load more classes
require_once('AbstractCompaTableView.php');
require_once('CompatViewList.php');
require_once('CompatViewTable.php');
require_once('CompatViewNotSupportedBlock.php');

class Compatables
{

  /** @var array (unique ID => string) */
  public static $items = array(); // used by closure

  /**
   * [hasValidCache description]
   *
   * @param  string $cacheKey Cache key
   * @param  string $hash     Hash checksum from the originating JSON document
   *
   * @return mixed Either an object or false
   */
  public static function fromMemcache($cacheKey, $hash)
  {
    return false; // Cache purging needs more work

    global $wgMemc;
    $read = $wgMemc->get($cacheKey);

    if($read === false) {
      return false;
    }

    $data = unserialize($read);
    if(isset($data['hash']) && isset($data['output'])) {
      if($data['hash'] !== $hash) {
        $wgMemc->delete($cacheKey);

        return false;
      }

      return $data['output'];
    }

    return false;
  }

  protected static function toMemcache(AbstractCompaTableView $obj) {
    global $wgMemc;
    $key = $obj->getCacheKeyName();

    $wgMemc->add($key, $obj->serializeView());
  }

  /**
   * @param string $input
   * @param array $args
   * @param Parser $parser
   */
  public static function renderCompaTables( $input, array $args, Parser $parser ) {
    global $wgCompatablesUseESI, $wgUseTidy, $wgAlwaysUseTidy;

    $data = self::getCompatablesJson();
    $args['feature'] = isset( $args['feature'] ) ? $args['feature'] : '';
    $args['format'] = isset( $args['format'] ) ? $args['format'] : '';

    $out = '';
    if ( $input != '' ) {
      $out .= '<p class="compat-label">' . $input . '</p>';
    }

    $table = self::generateCompaTable($data, $args);
    if ( ( $wgUseTidy && $parser->getOptions()->getTidy() ) || $wgAlwaysUseTidy ) {
      $table = MWTidy::tidy( $table );
    }

    if ($wgCompatablesUseESI === true) {
      // @TODO: this breaks in ESI level if $url ends up http for https views
      $url = SpecialPage::getTitleFor( 'Compatables' )->getFullUrl( array(
        'feature' => $args['feature'], 'format' => $args['format'], 'foresi' => 1 ) );
      $url = wfExpandUrl( $url, PROTO_INTERNAL );
      // @TODO: if the JSON file is always updated the same day of the week, one
      // could do some math here to avoid IMS GETs from CDN.
      // @TODO: Varnish does not support TTL here :/
      $ttl = 3600; // revalidate TTL

      // @TODO: Varnish does not support <esi:try> nor alt fallback URLs
      // (https://www.varnish-cache.org/docs/3.0/tutorial/esi.html)
      $out .= self::getUniqPlaceholder( // protect from Tidy
        "\n<!--esi\n" .
        Xml::element( 'esi:include', array( 'src' => $url, 'ttl' => $ttl ) ) . "\n" .
        "-->\n" .
        "<esi:remove>\n" .
        $table . "\n" . // fallback if no ESI interpreter is around
        "</esi:remove>\n"
      );

      /*
      $out .= self::getUniqPlaceholder( // protect from Tidy
        "\n<esi:try>\n" .
        "<esi:attempt>\n" .
        Xml::element( 'esi:include', array( 'src' => $url, 'ttl' => $ttl ) ) . "\n" .
        "</esi:attempt>\n" .
        "<esi:except>\n" .
        // If this ends up with an error *or* no ESI interpreter is active, this
        // will still show (though perhaps be stale) and the <esi> tags won't render.
        // If the special page works and ESI is running, it will strip this out.
        "<!-- Error: Special:Compatables or ESI is not available; used fallback! -->\n" .
        $table . "\n" .
        "</esi:except>\n" .
        "</esi:try>\n"
      );
      */
    } else {
      $out .= $table;
      $parser->getOutput()->updateCacheExpiry( 6*3600 ); // worse cache hit rate
    }

    return $out;
  }

  /**
   * Get a uniq marker for $text that is safe from Tidy
   * @param string $text
   */
  private static function getUniqPlaceholder( $text ) {
    $id = wfRandomString( 32 );
    self::$items[$id] = $text;
    return "<!-- UNIQ-Compatables:$id:selbatapmoC-QINU -->";
  }

  /**
   * Unstrip the esi tags now that Tidy finished (which clobbers ESI tags)
   * @param string $out
   * @param string $text
   */
  public static function onParserAfterTidy( &$out, &$text ) {
    if ( count( self::$items ) ) {
      # Find all hidden content and restore to normal...
      # (e.g. "<!-- UNIQ-Compatables:0cf806d86f00bef17b5035d9b8c3d00e:selbatapmoC-QINU -->")
      $text = preg_replace_callback(
        "/<!-- UNIQ-Compatables:([a-f0-9]{32}):selbatapmoC-QINU -->/m",
        function( $m ) {
          return isset( Compatables::$items[$m[1]] ) ? Compatables::$items[$m[1]] : '';
        },
        $text
      );
    }
    return true;
  }

  /**
   * Nuke the uniq value to avoid hitting the preg_replace() for no reason
   * @param Parser $parser
   */
  public static function onParserClearState( Parser $parser ) {
    self::$items = array();
    return true;
  }

  /**
   * @return array
   */
  public static function getCompatablesJson() {
    global $wgCompatablesJsonFileUrl;

    $json_url = $wgCompatablesJsonFileUrl;
    $req = MWHttpRequest::factory( $json_url, array( 'method' => 'GET' ) );
    $status = $req->execute();
    if ( $status->isOK() ) {

      // This prevents us to make two HTTP calls for last-modified
      // and adds a timestamp property for later use.
      $date = $req->getResponseHeader('Last-Modified');
      $content = FormatJSON::decode( $req->getContent(), true );
      $data = array_merge($content, array('timestamp'=> $date, 'hash'=>md5($date)));

      if ( !$content ) {
        throw new MWException( "Unable to parse json file at {$json_url}." );
      }

      return $data;
    } else {
      throw new MWException( "Unable to GET json file at {$json_url}." );
    }
  }

  /**
   * @return string
   */
  public static function getCompatablesJsonTimestamp() {
    global $wgCompatablesJsonFileUrl;

    $json_url = $wgCompatablesJsonFileUrl;
    $req = MWHttpRequest::factory( $json_url, array( 'method' => 'HEAD' ) );
    $status = $req->execute();
    if ( $status->isOK() ) {
      return $req->getResponseHeader( 'Last-Modified' );
    } else {
      throw new MWException( "Unable to HEAD json file at {$json_url}." );
    }
  }

  /**
   * @param array $data Array from compatibility JSON file
   * @param array $args
   * @return string
   */
  public static function generateCompaTable( array $data, array $args ) {
    /** @var instanceof AbstractCompaTableView */
    $viewObject = null;
    $contents = null;
    $format = $args['format'];
    $className = 'CompatView'.ucfirst($format);

    // Should match patterin from AbstractCompaTableView::getCacheKeyName()
    $cacheKeyName = 'compatables:'.$className.':'.$args['feature'];
    $cachedView = self::fromMemcache($cacheKeyName, $data['hash']);

    // Return cached object as soon as possible!
    if($cachedView !== false) {
        return $cachedView;
    }

    // extracting data for feature
    if(isset($data['data'][$args['feature']])) {
      $tmp = $data['data'][$args['feature']];
      // Looping through contents array, that
      //   contains browser types (e.g. 'mobile','desktop')
      if(is_array($tmp['contents'])) {
        foreach($tmp['contents'] as $tmp_key => $tmp_value) {
          if(is_array($tmp_value) && count($tmp_value) >= 1) {
            $contents[$tmp_key] = $tmp_value;
          }
        }
      }
    }

    $meta['timestamp'] = $data['timestamp'];
    $meta['hash']      = $data['hash'];
    $meta['feature']   = $args['feature'];

    if(in_array($format, array('table','list'))) {
      $viewObject = new $className($contents, $meta);
    } else {
      $meta['format'] = $format;
      $viewObject = new CompatViewNotSupportedBlock($contents, $meta);
    }

    self::toMemcache($viewObject);

    return $viewObject->getOutput();
  }
}
