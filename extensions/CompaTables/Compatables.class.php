<?php

class Compatables
{

  /** @var array (unique ID => string) */
  public static $items = array(); // used by closure

  protected static $allowed_formats = array('table','list');

  const TTL = 3600;


  /**
   * Purge key in Memcache
   *
   * @param  string $cacheKey Cache key
   *
   * @return null
   */
  public static function saveMemcacheKey($cacheKey, $data)
  {
    global $wgMemc;

    $wgMemc->set( $cacheKey, serialize($data), self::TTL );
  }

  /**
   * Purge key in Memcache
   *
   * @param  string $cacheKey Cache key
   *
   * @return null
   */
  public static function purgeMemcacheKey($cacheKey)
  {
    global $wgMemc;

    $wgMemc->delete($cacheKey);
  }

  /**
   * Check and return data from Memcache
   *
   * @param  array  $cacheKey
   * @param  string $hash     Hash checksum from the originating JSON document
   *
   * @return mixed Either an array or false
   */
  public static function fromMemcache($cacheKey, $hash=null)
  {
    global $wgMemc, $wgRequest;

    $cachedView = $wgMemc->get($cacheKey);

    if($cachedView !== false) {
      $unserialized = unserialize($cachedView);
      if(isset($unserialized['hash']) && isset($unserialized['output'])) {
        if($unserialized['hash'] !== $hash) {
          $wgMemc->delete($cacheKey);

          return false;
        }

        return $unserialized;
      }
    }

    return false;
  }

  /**
   * Render CompaTable HTML code
   *
   * Reads from JSON file, triggers generation if required
   * and optionally adds ESI tags.
   *
   * @param string $input
   * @param array  $args
   * @param Parser $parser
   */
  public static function renderCompaTables( $input, array $args, Parser $parser ) {
    global $wgCompatablesUseESI, $wgUseTidy, $wgAlwaysUseTidy;
    $out = '';

    $args['feature'] = isset( $args['feature'] ) ? $args['feature'] : '';
    $args['format']  = isset( $args['format'] ) ? $args['format'] : '';
    $cacheKey        = wfMemcKey('compatables', $args['format'], $args['feature']);

    /**   *****************************   **/
    $data = self::getData();
    $cached = self::fromMemcache($cacheKey, $data['hash']);
    if( $cached !== false ) {
      $table = $cached['output'];
    } else {
      $generated = self::generateCompaTable( $data, $args );

      if ( ( $wgUseTidy && $parser->getOptions()->getTidy() ) || $wgAlwaysUseTidy ) {
        $generated['output'] = MWTidy::tidy( $generated['output'] );
      }

      self::saveMemcacheKey( $cacheKey, $generated );

      $table = $generated['output'];
    }
    /**   *****************************   * */

    if ( $input != '' ) {
      $out .= '<p class="compat-label">' . $input . '</p>';
    }

    if ( $wgCompatablesUseESI === true ) {
      $urlArgs['feature'] = $args['feature'];
      $urlArgs['format']  = $args['format'];
      $urlArgs['foresi']  = 1;
      // @TODO: this breaks in ESI level if $url ends up http for https views
      $urlHelper = SpecialPage::getTitleFor( 'Compatables' )->getFullUrl( $urlArgs );
      $out .= self::applyEsiTags($table, wfExpandUrl( $urlHelper, PROTO_INTERNAL ));
    } else {
      $out .= $table;
      $parser->getOutput()->updateCacheExpiry( 6*3600 ); // worse cache hit rate
    }

    return $out;
  }

  /**
   * Add ESI tags to markup
   *
   * @param  string $table String representation of the HTML to make ESI
   *
   * @return string The HTML block surrounded ESI tags
   */
  private static function applyEsiTags( $table, $url )
  {
      $out = null;

      // @TODO: if the JSON file is always updated the same day of the week, one
      // could do some math here to avoid IMS GETs from CDN.
      // @TODO: Varnish does not support TTL here :/
      //

      $params['src'] = $url;
      $params['ttl'] = self::TTL;
      // @TODO: Varnish does not support <esi:try> nor alt fallback URLs
      // (https://www.varnish-cache.org/docs/3.0/tutorial/esi.html)
      $out .= self::getUniqPlaceholder( // protect from Tidy
        PHP_EOL . "<!--esi" . PHP_EOL .
        Xml::element( 'esi:include', $params ) . PHP_EOL .
        "-->" . PHP_EOL .
        "<esi:remove>" . PHP_EOL .
        $table . PHP_EOL . // fallback if no ESI interpreter is around
        "</esi:remove>" . PHP_EOL
      );

      /*
      $out .= self::getUniqPlaceholder( // protect from Tidy
        PHP_EOL . "<esi:try>" . PHP_EOL .
        "<esi:attempt>" . PHP_EOL .
        Xml::element( 'esi:include', $params ) . PHP_EOL .
        "</esi:attempt>" . PHP_EOL .
        "<esi:except>" . PHP_EOL .
        // If this ends up with an error *or* no ESI interpreter is active, this
        // will still show (though perhaps be stale) and the <esi> tags won't render.
        // If the special page works and ESI is running, it will strip this out.
        "<!-- Error: Special:Compatables or ESI is not available; used fallback! -->" . PHP_EOL .
        $table . PHP_EOL .
        "</esi:except>" . PHP_EOL .
        "</esi:try>" . PHP_EOL
      );
      */

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
  public static function getData() {
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
  public static function getFileTimestamp() {
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
  public static function generateCompaTable( array $data, array $args )
  {

    $viewParameters['timestamp'] = $data['timestamp'];
    $viewParameters['hash']      = $data['hash'];
    $viewParameters['feature']   = $args['feature'];
    $viewParameters['format']    = $args['format'];

    // extracting data for feature
    $contents = null;
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

    if(in_array($viewParameters['format'], self::$allowed_formats)) {
      $className = 'CompatView'.ucfirst($viewParameters['format']);
      $viewObject = new $className($contents, $viewParameters);
    } else {
      $viewObject = new CompatViewNotSupportedBlock($contents, $viewParameters);
    }

    return $viewObject->toArray();
  }
}
