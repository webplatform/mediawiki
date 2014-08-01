/**
 * Read address GET parameters and return value assigned to the key 
 *
 * Credits: http://stackoverflow.com/questions/901115/how-can-i-get-query-string-values#answer-901144
 **/
function getQueryParameterByName(name) {
    name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}
 
/**
 * Piwik tracking within MediaWiki
 * 
 * Will help tracking Edit and Save of a page
 **/
function setupTracking() {
    var _paq = window._paq || [], 
        campaign = getQueryParameterByName('pk_campaign');
 
    if (campaign.length > 1 && campaign.match(/DocSprint/)) {
        _paq.push(['setCustomVariable', 2, 'event', campaign, 'visit']);                                                                                     
    }   
 
    if (typeof mw == 'object' && mw.user.isAnon() === false) {
      _paq.push(['setCustomVariable', 1, 'username', mw.user.getName(), 'visit']);
    }
    
    jQuery('body').on('click', '#main-content #wpSave', function(){
       var title = jQuery(this).attr('title') || null;
           
       if (typeof title === 'string') {
          _paq.push(['setCustomVariable', 1, 'label', title, 'page']);
       }
 
       _paq.push(['trackGoal', 2]); // idGoal=2 > Saves a page
    });
    
    jQuery('body').on('click', '.tool-area .toolbar .dropdown:first-child a', function(){
       var title = jQuery(this).attr('title') || null;
 
       if (typeof title === 'string') {
          _paq.push(['setCustomVariable', 1, 'label', title, 'page']);
       }
 
       _paq.push(['trackGoal', 1]); // idGoal=1 > Clicks edit page
    });
 
    if (typeof window._paq !== 'object') {
        window._paq = _paq;
    }   
 
    if (typeof window.console === 'object') {
      console.log('Tracking started');
    }   
}

jQuery(document).ready(setupTracking);

