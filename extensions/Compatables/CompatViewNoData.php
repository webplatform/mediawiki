<?php

class CompatViewNoData extends AbstractCompatView
{
  const ERR_BLOCK = 'There is no data available for topic "%s", feature "%s". If you think that there should be data available, consider <a href="%s">opening an issue</a>.';

  const HTML_BLOCK = '<div class="note"><p>%s</p></div>';

  const ISSUE_LINK = 'https://github.com/webplatform/compatibility-data/issues/new';

  public function __construct($contents = null, array $meta)
  {
    parent::__construct($contents, $meta);

    // same as AbstractCompatView::noDataMessageBlock, merge #TODO
    // and watchout for the $text $this->feature === $meta['feature'], but depends
    // where you are.
    $qs = array();
    $qs['title'] = sprintf( 'No data available for topic: %s, feature: %s', $meta['topic'], $meta['feature'] );
    $qs['labels'] = 'missing';
    //$qs['assignee'] = 'renoirb';
    $qs['body'] = 'Insert details here';

    $link = self::ISSUE_LINK . '?' . http_build_query( $qs, '', '&amp;' );
    $text = sprintf( self::ERR_BLOCK, $meta['topic'], $meta['feature'], $link );

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
