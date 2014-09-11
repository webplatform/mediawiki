<?php

abstract class AbstractCompatView
{
  //const SUMMARY = "This table shows %s browser feature %s support organized by list of sub-features and showing browser vendor and listing support level per version";

  const LONG_TITLE = '%s browser %s feature support';

  const ERR_BLOCK = 'There is no data available for topic "%s", feature "%s". If you think that there should be data available, consider <a href="%s">opening an issue</a>.';

  const HTML_BLOCK = '<div class="note"><p>%s</p></div>';

  const ISSUE_LINK = 'https://github.com/webplatform/compatibility-data/issues/new';

  /**
   * Selected Compatibility table
   *
   * @var array
   */
  protected $contents = array();

  /**
   * Useful metadata required for i18n and
   * display purposes
   **/
  protected $meta = array();

  /**
   * Generated output
   *
   * Enforcing to null to be sure
   * that if no output at all we know
   * something went wrong.
   *
   * #SanityCheck later if setting a
   * variable sets it to null by default.
   *
   * @type string The HTML to give
   **/
  protected $output = null;

  /**
   * URI to the source JSON document
   */
  protected $source;

  /**
   * Table content version tag
   */
  protected $timestamp;

  /**
   * md5 hash based on the timestamp
   */
  protected $hash;

  /**
   * Cache key
   */
  protected $cacheKey;

  /**
   * Topic name
   *
   * In which broad category is the feature
   * categorized into?
   */
  protected $topic;

  /**
   * Feature name
   *
   * Slugified version of the name
   * as an identifier.
   */
  protected $feature;

  /**
   * Initialize the content
   *
   * @param array  $contents Exceprt compat data specific for the feature
   * @param array  $meta     Meta data coming from the request to generate
   * @param string $ts       Timestamp string coming from the originating JSON file date
   */
  public function __construct($contents = null, array $meta){

    foreach($meta as $k => $v) {
        if(property_exists($this, $k)) {
          $this->{$k} = $v;
        }
    }

    // If we do not have
    // contents, lets stop right away
    if($contents === null) {
      $this->noDataMessageBlock();

      return $this;
    }

    $this->contents  = $contents;
    $this->meta      = $meta;

    // Bootstrap everything
    $this->compile();

    return $this;
  }

  /**
   * Run the table compilation
   *
   * Fill the $output as a string.
   *
   * @return bool Whether the compilation was completed
   */
  abstract protected function compile();

  protected function getOutput()
  {
      global $wgCompatablesSpecialUrl;

      $now = new \DateTime();

      $a['inner'] = $this->output;
      $a['classNames'] = array('compat-parent', 'compat-ng', 'compat-'.$this->feature, 'compat-topic-'.$this->topic);
      $a['dataAttribs']['data-comment'] = 'Generated on '.$now->format(\DateTime::W3C);
      $a['dataAttribs']['data-hash'] = $this->hash;
      $a['dataAttribs']['data-timestamp'] = $this->timestamp;
      $a['dataAttribs']['data-cacheKey'] = $this->cacheKey;
      $a['dataAttribs']['data-source'] = $this->source;
      $a['dataAttribs']['data-jsonselect'] = ':root .'.$this->topic.' .'.$this->feature;

      $b = array();
      $params = array('feature', 'topic', 'format');
      foreach($params as $p) {
        if(isset($this->{$p})) {
          $b[$p] = $this->{$p};
          $a['dataAttribs']['data-'.$p] = $this->{$p};
        }
      }
      $a['dataAttribs']['data-canonical'] = $wgCompatablesSpecialUrl.'?'.http_build_query($b);

      return $this->tagHelper($a, 'div');
  }

  /**
   * Based on caniuse.com's model:
   *
   *  y - (Y)es, supported by default
   *  a - (A)lmost supported (aka Partial support)
   *  n - (N)o support, or disabled by default
   *  p - No support, but has (P)olyfill
   *  u - Support (u)nknown
   *  x - Requires prefi(x) to work
   *
   * Ref: https://github.com/Fyrd/caniuse/blob/master/Contributing.md
   **/
  protected function supportText($support_string)
  {
      $out = array();
      $exploded = explode(',', $support_string);

      if($exploded !== false) {
          foreach($exploded as $support_token) {
              switch ($support_string) {
                  case 'p':
                    $out[] = array(
                              'inner'=>'No support, but has polyfill',
                              'classNames' => array('compat-shaded')
                            );
                    break;
                  case 'a':
                    $out[] = array(
                              'inner'=>'Partial support (almost)'
                            );
                    break;
                  case 'x':
                    $out[] = array(
                              'title'=>'Requires script polyfill library to work',
                              'inner'=>'prefix',
                              'classNames' => array('compat-prefix', 'prefix')
                            );
                    break;
                  case 'y':
                    $out[] = array(
                              'inner'=>'Yes'
                            );
                    break;
                  case 'n':
                    $out[] = array(
                              'title'=>'No support, or disabled by default',
                              'inner'=>'none',
                              'classNames' => array('compat-shaded')
                            );
                    break;
                  case 'u':
                  default:
                    $out[] = array(
                              'inner'=>'Unknown',
                              'classNames' => array('compat-shaded')
                            );
                    break;
              }
          }
      }

      return $out;
  }

  protected function versionText($version_string)
  {
      if(strstr($version_string, '?') == false) {
        $out['inner'] = $version_string;
      } else {
        $out['inner'] = '?';
        $out['title'] = 'Version unknown';
        $out['classNames'] = array('compat-shaded');
      }

      return $out;
  }

  protected function noDataMessageBlock()
  {
    // same as CompatViewNoData::__construct, merge #TODO
    // and watchout for the $text $this->feature === $meta['feature'], but depends
    // where you are.
    $qs = array();
    $qs['title'] = sprintf( 'No data available for topic: %s, feature: %s', $meta['topic'], $meta['feature'] );
    $qs['labels'] = 'missing';
    //$qs['assignee'] = 'renoirb';
    $qs['body'] = 'Insert details here';

    $link = self::ISSUE_LINK . '?' . http_build_query( $qs, '', '&amp;' );
    $text = sprintf( self::ERR_BLOCK, $this->topic, $this->feature, $link );

    $this->output = sprintf( self::HTML_BLOCK, $text );
  }

  protected function tagHelper($in, $tagName='div')
  {
      $tagAttribs = array();
      foreach($in as $inputk => $inputv) {
        switch($inputk) {
          case 'id':
            $tagAttribs['id'] = $inputv;
          break;
          case 'title':
            $tagAttribs['title'] = $inputv;
          break;
          case 'classNames':    // Casting as an array
            $tagAttribs['class'] = (array) $inputv;
          break;
          case 'dataAttribs':
            $tagAttribs = array_merge($tagAttribs, (array) $inputv);
          break;

          // Not validating if its in
          // a <td> or <th> tag.
          case 'headers':
            $tagAttribs['headers'] = $inputv;
          break;
          case 'scope':
            $tagAttribs['scope'] = $inputv;
          break;
        }
      }

      // Use MediaWiki's Html class
      // in includes/Html.php
      $tag = Html::rawElement(
          $tagName,
          $tagAttribs,
          $in['inner']
      );

      return $tag;
  }

  public function toArray()
  {
    return array(
      'timestamp' => $this->timestamp,
      'hash'      => $this->hash,
      'topic'     => $this->topic,
      'feature'   => $this->feature,
      'cacheKey'  => $this->cacheKey,
      'output'    => '<nowiki>'.$this->getOutput().'</nowiki>'
    );
  }
}
