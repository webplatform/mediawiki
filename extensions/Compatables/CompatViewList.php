<?php

class CompatViewList extends AbstractCompatView
{
  const NOT_READY_BLOCK = '<div class="note"><p>List view is not available at this time. Please use format="table" instead.</p></div>';

  /**
   * List row template
   *
   * Placeholders to replace, WIP
   *
   * - %supportClassName (lowercased version of %supportName)
   * - %supportName
   *
   * - %uaName
   * - %uaClassName (lowercased version of %uaName)
   * - %uaTypeClassName (either: mobile, desktop)
   */
  const LIST_ROW_TEMPLATE = '<dt class="%supportClassName %uaClassName %uaTypeClassName"><span>%uaName</span></dt><dd class="%supportClassName">%supportName</dd>';

  /**
   * @inheritDoc
   */
  protected function compile()
  {

    /*
     * WIP!
     *
    // loop through desktop, mobile
    foreach ($this->contents as $browser_type_key => $feature_list) {
      //$longSummary = sprintf(self::SUMMARY, ucfirst($browser_type_key), $this->feature);
      $longTitle = sprintf(self::LONG_TITLE, ucfirst($browser_type_key), $this->feature);

      $out .= sprintf('<section data-browser-type="%s">', strtolower($browser_type_key));
      $out .= sprintf('<h3>%s</h3>', ucfirst($browser_type_key));
      $out .= '<dl class="compat-list compatibility">';

      // stuff...

      $out .= '</dl>';
      $out .= '</section>';
    }
    */

    /*
     * Original list pattern
     *
     * TODO:
     * - Create markup to have nested lists (wasn’t like this before)
     * - See CompatViewTable to reproduce in list format
     *
    $out = '<dl class="compat-list">';
    // supportClass: ['unknown','partial','supported']
    // see: https://github.com/webplatform/mediawiki/blob/9a60defd7f91779d5011825887dccfcbb2bcf4eb/extensions/CompaTables/compatables.php
    $out .= '<dt class="' . $supportclass . ' ' . $ua . '"><span>' . $browserinfo[$ua]['browser'] . '</span></dt><dd class="' . $supportclass . '">' . $supportclass . '</dd>';
    if ('Desktop' == $device['title'] && $finalitem == $ua ) {
        $out .= '<dt class="MOBILE_SUPPORT mobiles"><span>Mobiles</span></dt><dd class="MOBILE_SUPPORT">MOBILE_SUPPORT';
    } else {
        $out .= '</dd></dl>';
    }
    */

    $out = self::NOT_READY_BLOCK;

    $this->output = $out . sprintf('<!-- Here would be a dl for topic "%s", feature "%s" #TODO -->', $this->topic, $this->feature);

    return true;
  }
}