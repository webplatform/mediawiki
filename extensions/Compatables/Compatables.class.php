<?php

class Compatables
{

  /** @var array (unique ID => string) */
  public static $items = array(); // used by closure

  protected static $allowed_formats = array('table','list');

  const TTL = 3600;

  const MAX_AGE = 3600;

  /**
   * Serialize and save data to memcache
   *
   * Note that it also sets a time to live for the
   * cached version set to self::TTL
   *
   * @param string $cacheKey Cache key
   * @param mixed  $data     Data to send to memcached, will use serialize();
   *
   * @return null
   */
  public static function memcacheSave($cacheKey, $data)
  {
    $cache = wfGetCache( CACHE_ANYTHING );
    //$cache->setDebug( true ); // DEBUG

    $cache->set( $cacheKey, serialize($data), self::TTL );
  }

  /**
   * Delete entry from memcache from given cache key
   *
   * @param  string $cacheKey Cache key
   *
   * @return null
   */
  public static function memcacheRemove($cacheKey)
  {
    $cache = wfGetCache( CACHE_ANYTHING );
    //$cache->setDebug( true ); // DEBUG
    wfDebugLog( 'CompaTables', 'Deleted "' . $cacheKey .'"' );

    $cache->delete( $cacheKey );
  }

  /**
   * Check and return data from memcache
   *
   * Second option allows to specify a hash to compare and
   * delete the entry if it mismatch.
   *
   * If specified hash mismatch the saved one, it deletes
   * the entry and returns false as if it didn't find anything.
   *
   * @param  array $cacheKey
   * @param  mixed $hash     Optional; String hash checksum from the originating JSON document.
   *
   * @return mixed Either an array or false
   */
  public static function memcacheRead($cacheKey, $hash=null)
  {
    $cache = wfGetCache( CACHE_ANYTHING );
    //$cache->setDebug( true ); // DEBUG

    $cachedView = $cache->get($cacheKey);

    if($cachedView !== false) {
      $unserialized = unserialize($cachedView);
      if(isset($unserialized['hash']) && isset($unserialized['output'])) {
        if( $unserialized['hash'] !== $hash && $hash !== null ) {
          $cache->delete( $cacheKey );
          wfDebugLog( 'CompaTables', 'Attempted to read "' . $cacheKey .'" but the hash did not match, deleted it.');

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

    $args['topic']    = isset( $args['topic']   ) ? $args['topic']   : '';
    $args['feature']  = isset( $args['feature'] ) ? $args['feature'] : '';
    $args['format']   = isset( $args['format'] ) ? $args['format'] : '';
    $args['cacheKey'] = wfMemcKey('compatables', $args['format'], $args['topic'], $args['feature']);

    /**   *****************************   **/
    $data = self::getData();
    if ( $data !== null ) {

      $cached = self::memcacheRead($args['cacheKey'], $data['hash']);
      if( $cached !== false ) {
        $table = $cached['output'];
      } else {
        $generated = self::generateCompaTable( $data, $args );

        if ( ( $wgUseTidy && $parser->getOptions()->getTidy() ) || $wgAlwaysUseTidy ) {
          $generated['output'] = MWTidy::tidy( $generated['output'] );
        }

        self::memcacheSave( $args['cacheKey'], $generated );

        $table = $generated['output'];
      }
      /**   *****************************   * */

      // We are ignoring <compatibility>input would be here</compatibility>
      // because its useless for now.
      //if ( $input != '' ) {
      //  $out .= '<p>' . $input . '</p>';
      //}

      if ( $wgCompatablesUseESI === true ) {
        $urlArgs['topic']   = $args['topic'];
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
    } else {
      wfDebugLog( 'CompaTables', 'Could not generate table, data is either empty or had problems.' );
      $out = '<!-- Compatables: Could not generate table, data might be empty or had problems with caching -->';
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

  public static function onBeforePageDisplay( OutputPage &$out, Skin &$skin )
  {
    global $wgCompatablesCssFileUrl;
    $out->addStyle($wgCompatablesCssFileUrl);
    return true;
  }

  /**
   * @return array
   */
  public static function getData() {
    global $wgCompatablesJsonFileUrl;

    // See https://github.com/webplatform/mediawiki/issues/16 #TODO
    $cache = wfGetCache( CACHE_ANYTHING );
    $key = wfMemcKey( 'webplatformdocs', 'compatables', 'data', 'full' );
    $data = $cache->get( $key );

    if ( $data !== false ) {
      wfDebugLog( 'CompaTables', 'Got compat/data.json contents from cache' );
    } else {
      wfDebugLog( 'CompaTables', 'Made an HTTP request to ' . $wgCompatablesJsonFileUrl );

      try {
        $data = self::getJsonFile();
      } catch(Exception $e) {
        wfDebugLog( 'CompaTables', 'Could not get compat data JSON file' );
        $data = null;
      }

      if ( $data !== null ) {
        $cache->set( $key, $data, 60 * 60 * 12 );
      }
    }

    return $data;
  }

  protected static function getJsonFile() {
    global $wgCompatablesJsonFileUrl;

    $json_url = $wgCompatablesJsonFileUrl;
    $req = MWHttpRequest::factory( $json_url, array( 'method' => 'GET' ) );
    $status = $req->execute();
    if ( $status->isOK() ) {

      // This prevents us to make two HTTP calls for last-modified
      // and adds a timestamp property for later use.
      $date = $req->getResponseHeader('Last-Modified');
      $content = FormatJson::decode( $req->getContent(), true );
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
   * Search the $compatArray, return a CompatView* object
   *
   * Let us say we have a tag in the wiki with the following attributes:
   *
   *    <compatibility topic="css" feature="border-radius" format="table" />
   *
   * And that we have a JSON file that has a similar format.
   *
   *     {
   *       data: {
   *         css: {
   *           'border-radius': {
   *             contents: {
   *               desktop: {},
   *               mobile: {}
   *             },
   *             links: []
   *           }
   *         }
   *       }
   *     }
   *
   * $compatArray is actually that JSON string, passed through json_encode().
   *
   * We can then dig into that array tree like this:
   *
   *     $compatArray['data']['css']['border-radius']['contents'];
   *
   * Then, we test if an array exists at that location, like so:
   *
   *     $compatArray['data'][$topic][$feature]['contents']
   *
   * If we have an array at 'contents', we can continue.
   *
   * @param array $compatArray Array json_encode()-ed from compatibility JSON file
   * @param array $args
   *
   * @return AbstractCompatView
   */
  public static function generateCompaTable( array $compatArray, array $args )
  {
    /* @var $viewObject instanceof AbstractCompatView */
    $viewObject = null;

    // Will hold stuff we send to the view object
    $viewArgs = array();

    // Will hold what we came for
    $contents = null;

    // Will hold the raw data to prepare
    $tableData = null;

    // What were the <compatibility topic="" feature="" format="" />
    // attributes.
    $viewArgs['topic']     = $args['topic'];
    $viewArgs['feature']   = $args['feature'];
    $viewArgs['format']    = $args['format'];

    // Some source info to be able to review in generated tables HTML
    $viewArgs['cacheKey']  = $args['cacheKey'];
    $viewArgs['source']    = $GLOBALS['wgCompatablesJsonFileUrl'];
    $viewArgs['timestamp'] = $compatArray['timestamp'];
    $viewArgs['hash']      = $compatArray['hash'];

    // Finding appropriate data
    if(isset($compatArray['data'][$viewArgs['topic']][$viewArgs['feature']])) {
      $tableData = $compatArray['data'][$viewArgs['topic']][$viewArgs['feature']];
    }

    // If we have data, we are good to continue!
    if(is_array($tableData) && isset($tableData['contents'])) {
        // Loop through contents array, that
        //   contains browser types (e.g. 'mobile','desktop')
        foreach($tableData['contents'] as $tmp_key => $tmp_value) {
          if(is_array($tmp_value) && count($tmp_value) >= 1) {
            $contents[$tmp_key] = $tmp_value;
          }
        }
    }

    if($tableData === null) {
      $viewObject = new CompatViewNoData($contents, $viewArgs);
      $logCtx = substr(str_replace('"', ' ', json_encode($viewArgs,true)),1,-1);
      wfDebugLog( 'CompaTables', 'No compat data found for ' . $logCtx );
    } elseif(in_array($viewArgs['format'], self::$allowed_formats)) {
      // Based on Format (e.g. list, table) will call
      // a class (e.g. CompatViewList for format=list) to handle the
      // HTML generation logic
      // E.g. CompatViewList, CompatViewTable
      $className = 'CompatView'.ucfirst($viewArgs['format']);
      $viewObject = new $className($contents, $viewArgs);
    } else {
      $viewObject = new CompatViewNotSupportedBlock($contents, $viewArgs);
    }

    try {
      $logMsg = 'Finished generating';
      $logCtx = substr(str_replace('"', ' ', json_encode($viewArgs,true)),1,-1);
      wfDebugLog( 'CompaTables', $logMsg . $logCtx );
    } catch( Exception $e ) {
      // Will not fail here, make sure its fine to remove #TODO
    }

    return $viewObject->toArray();
  }
}
