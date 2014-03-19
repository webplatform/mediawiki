<?php

abstract class AbstractCompaTableView
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

  public function getOutput()
  {
      $now = new \DateTime();
      $dataComment = 'Generated on '.$now->format(\DateTime::W3C);

      $out = sprintf('<div data-comment="%s" class="compat-parent compat-ng compat-%s">', $dataComment, $this->feature);
      $out .= $this->output;
      $out .= '</div>';

      return $out;
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

  protected function noDataMessageBlock()
  {
      $this->output = sprintf(self::ERR_NO_COMPAT_FOUND, $this->feature);
  }

  protected function tagHelper($in, $tagName='div')
  {
      $tagAttribs = array();
      if(isset($in['classNames'])) {
        $tagAttribs['class'] = $in['classNames'];
      }
      if(isset($in['title'])) {
        $tagAttribs['title'] = $in['title'];
      }

      // Use MediaWiki's Html class in includes/Html.php
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
      'output'    => '<!-- Generated -->'.$this->getOutput().'<!-- /Generated -->'
    );
  }
}