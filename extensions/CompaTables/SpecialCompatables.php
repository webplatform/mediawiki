<?php

class SpecialCompatables extends UnlistedSpecialPage
{
	public function __construct() {
		parent::__construct( 'Compatables' );
	}

	public function execute( $par ) {
		global $wgCompatablesUseESI, $wgUseTidy, $wgAlwaysUseTidy;

		$this->setHeaders();

		$args['feature']  = $this->getRequest()->getVal( 'feature' );
		$args['format']   = $this->getRequest()->getVal( 'format' );
		$args['cacheKey'] = wfMemcKey('compatables', $args['format'], $args['feature']);

		// Handle purge requests from admins...
		// @TODO: Varnish, which only supports a few bits of ESI, can not handle this
		// (https://www.varnish-cache.org/docs/3.0/tutorial/esi.html)
		// (https://www.varnish-cache.org/trac/wiki/Future_ESI)
		if ( $this->getRequest()->getVal( 'action' ) === 'purge' ) {
			if ( $wgCompatablesUseESI && $this->getUser()->isAllowed( 'purgecompatables' ) ) {

				Compatables::memcacheRemove($args['cacheKey']);

				// Get the ESI URL prefix to purge
				$urlPrefix = SpecialPage::getTitleFor( 'Compatables' )->getFullUrl();
				$urlPrefix = wfExpandUrl( $urlPrefix, PROTO_INTERNAL );
				// Include as an in-band ESI invalidation request
				$this->getOutput()->addHtml(
					"\n<esi:invalidate>\n" .
					"<?xml version=\"1.0\"?>\n" .
					"<!DOCTYPE INVALIDATION SYSTEM \"internal:///WCSinvalidation.dtd\">\n" .
					"<INVALIDATION VERSION=\"WCS-1.1\">\n" .
						"<OBJECT>\n" .
							Xml::element( 'ADVANCEDSELECTOR', array( 'URIPREFIX' => $urlPrefix ) ) .
							"\n<ACTION REMOVALTTL=\"0\"/>\n" .
						"</OBJECT>\n" .
					"</INVALIDATION>\n" .
					"</esi:invalidate>\n"
				);

				$this->getOutput()->addWikiMsg( 'compatables-purged' );

				return;
			} else {
				throw new PermissionsError( 'purgecompatables' );
			}
		}

		// 1 hour server-side cache max before revalidate
		$this->getOutput()->setSquidMaxage( Compatables::MAX_AGE );

		// Try to handle IMS GET requests from CDN efficiently
		//   $data['timestamp'] has been added, to not have to do
		//   2 HTTP queries
		$data = Compatables::getData();

		if ( $this->getOutput()->checkLastModified( $data['timestamp'] ) ) {
			return; // nothing to send (cache hit)
		}

    /**   *****************************   **/
		$cached = Compatables::memcacheRead( $args['cacheKey'], $data['hash'] );
		if( $cached !== false ) {
			$table = $cached['output'];
		} else {
			$generated = Compatables::generateCompaTable( $data, $args );

      if ( ( $wgUseTidy && $parser->getOptions()->getTidy() ) || $wgAlwaysUseTidy ) {
        $generated['output'] = MWTidy::tidy( $generated['output'] );
      }

			Compatables::memcacheSave( $args['cacheKey'], $generated );

			$table = $generated['output'];
		}
    /**   *****************************   **/


		if ( $this->getRequest()->getBool( 'foresi' ) ) {
			// $this->getOutput()->addHtml( "<!DOCTYPE html><html>$table</html>" );
			$this->getOutput()->setArticleBodyOnly( true );
		} else {
			$this->getOutput()->addHtml( $table );
		}
	}
}