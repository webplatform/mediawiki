<?php

class CompatViewTable extends AbstractCompaTableView
{
  /**
   * @inheritDoc
   */
  protected function compile()
  {
    $out = '';

    // loop through desktop, mobile
    foreach ($this->contents as $browser_type_key => $feature_list) {

      $out .= '<h3>'.ucfirst($browser_type_key).'</h3>';
      $out .= '<table class="compat-table">';

      // We want the first values of $feature_list
      // that has a list of browsers inside. In it
      // we want to list only once that list of browsers.
      $out .= '<thead><tr><th>Features</th>';
      $a = array_values($feature_list);
      foreach(array_keys($a[0]) as $f) {
        $out .= $this->tagHelper(array('inner'=>$f), 'th');
      }
      $out .= '</tr></thead>';

      $out .= '<tbody>';
      foreach($feature_list as $feature_name_key => $browser_list) {
        $out .= '<tr>';
        $out .= '<th>'.$feature_name_key.'</th>';

        foreach($browser_list as $f) {
          $out .= '<td>';
          $notes = null;

          if(isset($f['notes'])) {
            $notes = $f['notes'];
            unset($f['notes']);
          }

          $out .= '<dl>';
          foreach($f as $version => $support_string_descriptor) {
            // I know, an anonymous div; lets
            //   live with it for now.
            $out .= $this->tagHelper($this->versionText($version),'dt');
            foreach($this->supportText($support_string_descriptor) as $supportVersion) {
              $out .= '<dd>'.$this->tagHelper($supportVersion, 'abbr').'</dd>';
            }
          }
          $out .= '</dl>';
          $out .= '</td>';
        }
        $out .= '</tr>';
      }
      $out .= '</tbody>';
      $out .= '</table>';
    }

    //$out .= '<pre><tt>'.print_r($this->contents,1).'</tt></pre>';  // DEBUG

    $this->output = $out;

    return true;
  }

}