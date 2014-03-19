<?php

class CompatViewList extends AbstractCompaTableView
{
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