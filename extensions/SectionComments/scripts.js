var WebplatformSectionCommentsSMW = {
  api : new mw.Api(),

  addSlideDownClickHandler : function(){
    $('#comments-flag-link').unbind( 'click' );
    $('#comments-flag-link').click( function() {
      $('#comment-flags').slideDown();
      WebplatformSectionCommentsSMW.scrollToBottom();
      WebplatformSectionCommentsSMW.addSlideUpClickHandler();

    });
  },
  addSlideUpClickHandler : function(){
    $('#comments-flag-link').unbind( 'click' );
    $('#comments-flag-link').click( function() {
      $('#comment-flags').slideUp();
      WebplatformSectionCommentsSMW.addSlideDownClickHandler();

    });
  },
  scrollToBottom: function() {
    var container = $('#commentsDialog'),
    scrollTo = $('#scrollTo');
    $('#commentsDialog').scrollTop(
      scrollTo.offset().top - container.offset().top + container.scrollTop()
    );
  },
  commentsSubmit: function() {
    var formObject = $('#commentsform').serializeArray();
    var keyValuePairs = [];
//    var count = 0;

    $( formObject ).each( function(){
      keyValuePairs.push('"' + this.name + '":"' +  this.value + '"');
//      count++
//      if( count > 25 ) {
//        return false;
//      }
    });
    var buildString = '{' + keyValuePairs.join(",") + '}';// build json object as string: {"key":"value","key2":"value2"}

    WebplatformSectionCommentsSMW.api.get( {
      action: 'webplatformcommentssmw',
      task: 'submitFlags',
      pageId: wgArticleId,
      flags: buildString
    }, {
      ok: function ( data ) {
        //console.log( data.webplatformcommentssmw );
      }
    });


  }
};

$(document).ready( function() {
  $('#comment-flags').hide();
  $('#commentsform').bind( "onCommentsSubmit", function(){
    WebplatformSectionCommentsSMW.commentsSubmit();
  });
  WebplatformSectionCommentsSMW.addSlideDownClickHandler();
});