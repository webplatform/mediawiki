<?php
class ApiWebplatformSectionCommentsSMW extends ApiBase {

  public function execute() {
    $params = $this->extractRequestParams();
    if( $params['task'] === 'submitFlags' ) {
       $this->submitFlags( $params );
    }
    $this->getResult()->addValue( null, $this->getModuleName(), $params );
  }



  public function getVersion() {
    return __CLASS__ . ': $Id$';
  }

  public function getAllowedParams() {
    return array(
      'pageId' => null,
      'task' => null,
      'comment_id' => null,
      'flags' => null,
    );
  }

  public function submitFlags( &$params ) {
    global $wgUser, $webplatformSectionCommentsSMW;
    $aTemp = json_decode( $params['flags'], true );
    $aProperties = array();

    foreach( $aTemp as $key => $value ) {
      $aTempKey = array();
      if( preg_match( '#.*?\[(.*?)\]\[.*?\]#', $key, $aTempKey ) && $value != '1') {
        $aProperties[ $aTempKey[1] ][] = $value;
      }
    }
    $sbuiltString = '';
    foreach( $aProperties as $key => $value ) {
      $sbuiltString .= "\n|".$key.'=';
      $aTemp = array();
      foreach( $value as $key => $val ) {
        $aTemp[] = $val;
      }
      $sbuiltString .= implode( ',', $aTemp );
    }


    $oArticle = Article::newFromID( $params['pageId'] );
    $sContent = $oArticle->fetchContent();
    $sNewContent = preg_replace('#(\{\{'.$webplatformSectionCommentsSMW['template'].').*?(\}\})#s', "$1$sbuiltString\n$2", $sContent );

    $aData = array(
            'wpTextbox1' => $sNewContent,
            'wpSummary' => 'no summary',
            'wpStarttime' => 'nostarttime',
            'wpEdittime' => 'noedittime',
            'wpEditToken' => $wgUser->isLoggedIn() ? $wgUser->editToken() : EDIT_TOKEN_SUFFIX,
            'wpSave' => '',
            'action' => 'submit',
          );
    $oRequest = new FauxRequest( $aData, true );
    $oEditor = new EditPage( $oArticle );
    $oEditor->importFormData( $oRequest );

    // Try to save the page!
    $aResultDetails = array();
    $oSaveResult = $oEditor->internalAttemptSave( $aResultDetails );
    // Return value was made an object in MW 1.19
    if ( is_object( $oSaveResult ) ) {
      $sSaveResultCode = $oSaveResult->value;
    } else {
      $sSaveResultCode = $oSaveResult;
    }
    $params['html_response'] = $sSaveResultCode;
  }
}