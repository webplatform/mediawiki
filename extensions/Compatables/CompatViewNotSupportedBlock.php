<?php

class CompatViewNotSupportedBlock extends AbstractCompatView
{
  const ERR_BLOCK = '<div class="note"><p>Requested table format "%s" is not supported</p></div>';

  public function __construct($contents = null, array $meta)
  {
    parent::__construct($contents, $meta);

    $this->output = sprintf(self::ERR_BLOCK, $meta['format']);

    return $this;
  }

  /**
   * @inheritDoc
   */
  protected function compile()
  {
    // do nothing
  }
}