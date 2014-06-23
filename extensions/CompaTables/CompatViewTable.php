<?php

class CompatViewTable extends AbstractCompatView
{
  //const SUMMARY = "This table shows %s browser feature %s support organized by list of sub-features and showing browser vendor and listing support level per version";

  const LONG_TITLE = '%s browser %s feature support';

  /**
   * @inheritDoc
   */
  protected function compile()
  {
    $out = '';

    // Id must be unique, using the feature name hash
    // to be sure two tables on the same page doesn't
    // do naming collisions
    $hashId = substr(md5($this->feature), 0, 5);

    // loop through desktop, mobile
    foreach ($this->contents as $browser_type_key => $feature_list) {
      //$longSummary = sprintf(self::SUMMARY, ucfirst($browser_type_key), $this->feature);
      $longTitle = sprintf(self::LONG_TITLE, ucfirst($browser_type_key), $this->feature);

      $out .= '<h3>'.ucfirst($browser_type_key).'</h3>';
      $out .= '<table class="compat-table">'; //summary="'.$longSummary.'">';

      // We want the first values of $feature_list
      // that has a list of browsers inside. In it
      // we want to list only once that list of browsers.
      $out .= '<thead><tr><th id="'.$hashId.'-feature-'.strtolower($browser_type_key).'"><abbr title="'.$longTitle.'">Features</abbr></th>';
      $a = array_values($feature_list);
      $loopIndex = 1;
      ksort($a[0]); // Same as $browser_list, but we need the browser
                    //   list only once for the thead. Ideally, it
                    //   should be done in the importer.
                    //   see: https://github.com/webplatform/mdn-compat-importer/issues/4
      foreach(array_keys($a[0]) as $f) {
        $out .= $this->tagHelper(array('inner' => wfEscapeWikiText($f), 'id' => $hashId.'-c-'.$loopIndex++), 'th');
      }
      unset($loopIndex);
      $out .= '</tr></thead>';

      $out .= '<tbody>';
      $loopIndex = 1;
      $loopIndexRow = 1;
      foreach($feature_list as $feature_name_key => $browser_list) {
        $out .= '<tr>';
        $out .= $this->tagHelper(array('inner' => wfEscapeWikiText($feature_name_key), 'id' => $hashId.'-r-'.$loopIndexRow),'th');

        ksort($browser_list); // See: https://github.com/webplatform/mdn-compat-importer/issues/4
        foreach($browser_list as $user_agent) {
          $out .= '<td headers="'.$hashId.'-feature-'.strtolower($browser_type_key).' '.$hashId.'-r-'.$loopIndexRow.' '.$hashId.'-c-'.$loopIndex++.'">';
          //$notes = null;

          if(isset($user_agent['notes'])) {
            //$notes = $user_agent['notes'];
            unset($user_agent['notes']);
          }

          $out .= '<dl>';
          foreach($user_agent as $ua_version => $ua_descriptor) {
            $out .= $this->tagHelper($this->versionText($ua_version),'dt');
            foreach($this->supportText($ua_descriptor) as $supportVersion) {
              if(isset($supportVersion['classNames'])) {
                $out .= '<dd>'.$this->tagHelper($supportVersion, 'abbr').'</dd>';
              } else {
                $out .= '<dd>'.$supportVersion['inner'].'</dd>';
              }
            }
          }
          $out .= '</dl>';
          $out .= '</td>';
        }
        $out .= '</tr>';
        $loopIndexRow++;
      }
      unset($loopIndex);
      $out .= '</tbody>';
      $out .= '</table>';
    }

    //$out .= '<pre><tt>'.print_r($this->contents,1).'</tt></pre>';  // DEBUG

    $this->output = $out;

    return true;
  }

}