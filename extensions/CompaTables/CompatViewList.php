<?php

class CompatViewList extends AbstractCompatView
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