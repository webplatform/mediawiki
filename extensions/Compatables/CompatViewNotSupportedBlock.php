<?php

class CompatViewNotSupportedBlock extends AbstractCompatView
{
  const ERR_BLOCK = 'Requested table format "%s" is not supported.';

  const HTML_BLOCK = '<div class="note"><p>%s</p></div>';

  public function __construct($contents = null, array $meta)
  {
    parent::__construct($contents, $meta);

    $text = sprintf( self::ERR_BLOCK, $meta['format'] );

    $this->output = sprintf( self::HTML_BLOCK, $text );

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