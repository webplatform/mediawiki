<?php

class CompatViewTable extends AbstractCompatView
{
  const NOTICE_TEXT = 'Do you think this data can be improved? You can ask to add by <a href="%s">opening an issue</a> or <a href="%s">make a pull request</a>.';

  const ISSUE_LINK = 'https://github.com/webplatform/compatibility-data/issues/new';

  const REPO_LINK = 'https://github.com/webplatform/compatibility-data';

  /**
   * @inheritDoc
   */
  protected function compile()
  {
    $out = '';

    // Id must be unique, using the feature name hash
    // to be sure two tables on the same page doesn't
    // do naming collisions
    //$hashId = substr(md5($this->feature), 0, 5);

    // loop through desktop, mobile
    foreach ($this->contents as $browser_type_key => $feature_list) {
      //$longSummary = sprintf(self::SUMMARY, ucfirst($browser_type_key), $this->feature);
      $longTitle = sprintf(self::LONG_TITLE, ucfirst($browser_type_key), $this->feature);

      $out .= sprintf('<section data-browser-type="%s">', strtolower($browser_type_key));
      $out .= '<h3>'.ucfirst($browser_type_key).'</h3>';
      $out .= '<table class="compat-table compatibility">'; //summary="'.$longSummary.'">';

      // We want the first values of $feature_list
      // that has a list of browsers inside. In it
      // we want to list only once that list of browsers.
      $out .= '<thead><tr><th><abbr title="'.$longTitle.'">Features</abbr></th>';
      //$out .= '<thead><tr><th id="'.$hashId.'-feature-'.strtolower($browser_type_key).'"><abbr title="'.$longTitle.'">Features</abbr></th>';
      $a = array_values($feature_list);
      //$loopIndex = 1;
      ksort($a[0]); // Same as $browser_list, but we need the browser
                    //   list only once for the thead. Ideally, it
                    //   should be done in the importer.
                    //   see: https://github.com/webplatform/mdn-compat-importer/issues/4
      foreach(array_keys($a[0]) as $f) {
        $out .= $this->tagHelper(array('inner' => wfEscapeWikiText($f)), 'th');
        //$out .= $this->tagHelper(array('inner' => wfEscapeWikiText($f), 'id' => $hashId.'-c-'.$loopIndex++), 'th');
      }
      //unset($loopIndex);
      $out .= '</tr></thead>';

      $out .= '<tbody>';
      //$loopIndex = 1;
      //$loopIndexRow = 1;
      foreach($feature_list as $feature_name_key => $browser_list) {
        $out .= '<tr>';
        $out .= $this->tagHelper(array('inner' => wfEscapeWikiText($feature_name_key)),'th');
        //$out .= $this->tagHelper(array('inner' => wfEscapeWikiText($feature_name_key), 'id' => $hashId.'-r-'.$loopIndexRow),'th');

        ksort($browser_list); // See: https://github.com/webplatform/mdn-compat-importer/issues/4
        foreach($browser_list as $user_agent) {
          $out .= '<td>';
          //$out .= '<td headers="'.$hashId.'-feature-'.strtolower($browser_type_key).' '.$hashId.'-r-'.$loopIndexRow.' '.$hashId.'-c-'.$loopIndex++.'">';

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
        //$loopIndexRow++;
      }
      //unset($loopIndex);
      $out .= '</tbody>';
      $out .= '</table>';
      $out .= '</section>';
    }

    //$out .= '<pre><tt>'.print_r($this->contents,1).'</tt></pre>';  // DEBUG

    $qs = array();
    $qs['title'] = sprintf( 'Please improve data for topic: %s, feature: %s', $this->topic, $this->feature );
    $qs['labels'] = 'missing';
    //$qs['assignee'] = 'renoirb';
    $qs['body'] = 'Insert description here';
    $link = self::ISSUE_LINK . '?' . http_build_query( $qs, '', '&amp;' );

    $help = sprintf( self::NOTICE_TEXT, $link, self::REPO_LINK );

    $this->output = $help.$out;

    return true;
  }

}