<?php

require_once('AbstractCompaTableView.php');

class CompatViewList extends AbstractCompaTableView
{
  const FORMAT = 'CompatViewList';

  /**
   * @inheritDoc
   */
  protected function compile()
  {
    $out = '';
    $this->output = $out;

    return true;
  }

}