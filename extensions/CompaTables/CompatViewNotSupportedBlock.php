<?php

require_once('AbstractCompaTableView.php');

class CompatViewNotSupportedBlock extends AbstractCompaTableView
{
  const FORMAT = 'CompatViewNotSupportedBlock';
  const ERRORBLOCK = '<div class="note"><p><strong>NOTE</strong>Compatibility tables: Requested table format "%s" is not supported</p></div>';

  public function __construct($contents = null, array $meta)
  {
    parent::__construct($contents, $meta);

    $this->output = sprintf(self::ERRORBLOCK, $meta['format']);

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