<?php

abstract class AbstractCompatView
{
  const ERR_NO_COMPAT_FOUND = '<div class="note"><p>No compatibility data found for feature "<i>%s</i>"</p></div>';

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
      $a['classNames'] = 'compat-parent,compat-ng,compat-'.$this->feature;
      $a['dataAttribs']['data-comment'] = 'Generated on '.$now->format(\DateTime::W3C);
      $a['dataAttribs']['data-hash'] = $this->hash;
      $a['dataAttribs']['data-timestamp'] = $this->timestamp;
      $a['dataAttribs']['data-cacheKey'] = $this->cacheKey;
      $a['dataAttribs']['data-feature'] = $this->feature;
      if(isset($this->meta['format'])) {
          $a['dataAttribs']['data-canonical'] = $wgCompatablesSpecialUrl.'?feature='.$this->feature.'&format='.$this->meta['format'];
      }

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
                              'classNames' => 'compat-shaded'
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
                              'classNames' => 'compat-prefix prefix'
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
                              'classNames' => 'compat-shaded'
                            );
                    break;
                  case 'u':
                  default:
                    $out[] = array(
                              'inner'=>'Unknown',
                              'classNames' => 'compat-shaded'
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
        $out['classNames'] = 'compat-shaded';
      }

      return $out;
  }

  protected function noDataMessageBlock()
  {
      $this->output = sprintf(self::ERR_NO_COMPAT_FOUND, $this->feature);
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
          case 'classNames':
            $tagAttribs['class'] = explode(',',$inputv);
          break;
          case 'dataAttribs':
            $tagAttribs = array_merge($tagAttribs, $inputv);
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
      'cacheKey'  => $this->cacheKey,
      'output'    => '<!-- Generated --><nowiki>'.$this->getOutput().'</nowiki><!-- /Generated -->'
    );
  }
}