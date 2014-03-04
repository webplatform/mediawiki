<?php

require_once('AbstractCompaTableView.php');

class CompatViewTable extends AbstractCompaTableView
{
  const FORMAT = 'CompatViewTable';

  /**
   * @inheritDoc
   */
  protected function compile()
  {
    $out = '';
    $th = function($in){ return '<th>'.$in.'</th>'; };
    $td = function($in){ return '<td>'.$in.'</td>'; };
    $dd = function($in){ return '<dd>'.$in.'</dd>'; };
    $dt = function($in){ return '<dt>'.$in.'</dt>'; };

    // loop through desktop, mobile
    foreach ($this->contents as $browser_type_key => $feature_list) {

      $out .= '<h3>'.ucfirst($browser_type_key).'</h3>';
      $out .= '<table class="compat-table">';

      // We want the first values of $feature_list
      // that has a list of browsers inside. In it
      // we want to list only once that list of browsers.
      $out .= '<thead><tr><th>Features</th>';
      $a = array_values($feature_list);
      $out .= join("", array_map($th, array_keys($a[0])));
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
            $out .= '<dt>'.$this->versionHelper($version).'</dt>';
            $out .= join('', array_map($dd, $this->supportHelper($support_string_descriptor)));
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