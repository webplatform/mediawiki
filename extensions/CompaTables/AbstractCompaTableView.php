<?php

// Requires MediaWiki's Html class in includes/Html.php

abstract class AbstractCompaTableView
{

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
      $this->noDataMessageHelper();

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

  public function getOutput()
  {
      // Use MediaWiki's Html class in includes/Html.php
      $tag = Html::rawElement(
          'div',
          array('class' => array('compat-parent', 'compat-ng', 'prout')),
          $this->output
        );

      return $tag;
  }

  public function getFeatureName()
  {
      return $this->feature;
  }

  /**
   * Based on caniuse.com's model:
   *
   *  .y - (Y)es, supported by default
   *  .a - (A)lmost supported (aka Partial support)
   *  .n - (N)o support, or disabled by default
   *  .p - No support, but has (P)olyfill
   *  .u - Support (u)nknown
   *  .x - Requires prefi(x) to work
   *
   * Ref: https://github.com/Fyrd/caniuse/blob/master/Contributing.md
   **/
  protected function supportHelper($support_string)
  {
      $out = array();
      $exploded = explode(',', $support_string);

      if($exploded !== false) {
          foreach($exploded as $support_token) {
              switch ($support_string) {
                  case 'p':
                    $out[] = 'No support, but has polyfill';
                    break;
                  case 'a':
                    $out[] = 'Partial support (almost)';
                    break;
                  case 'x':
                    $out[] = '<abbr title="Requires script polyfill library to work">polyfill</abbr>';
                    break;
                  case 'y':
                    $out[] = 'Yes';
                    break;
                  case 'n':
                    $out[] = '<abbr title="No support, or disabled by default">none</abbr>';
                    break;
                  case 'u':
                    $out[] = 'Unknown';
                    break;
                  default:
                    $out[] = 'Unknown';
                    break;
              }
          }
      }

      return $out;
  }


  protected function versionHelper($version_string)
  {
      if(strstr($version_string, '?') == false) {
        $out = $version_string;
      } else {
        $out = '<abbr title="Version unknown">?</abbr>';
      }

      return $out;
  }

  protected function noDataMessageHelper()
  {
      $this->output = '
      <div class="note"><p>
        <b>NOTE</b>
        No compatibility data found for feature "<i>'.$this->getFeatureName().'</i>"
      </p></div>';
  }

  public function getCacheKeyName()
  {
      return 'compatables:'.static::FORMAT.':'.$this->getFeatureName();
  }

  public function getHash()
  {
      return $this->hash;
  }

  public function serializeView()
  {
    return serialize(array(
      'timestamp' => $this->timestamp,
      'hash'      => $this->hash,
      'output'    => '<!-- from memcached -->'.$this->getOutput().'<!-- /from memcached -->'
    ));
  }
}