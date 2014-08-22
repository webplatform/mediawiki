# Deployment improvement notes

What still needs to be done manually

## Add this to VHost

    Alias /w/skins/webplatform "/vagrant/mediawiki/extensions/WebPlatformDocs/skin"
    <Directory /vagrant/mediawiki/extensions/WebPlatformDocs/skin>
        Options FollowSymLinks
        AllowOverride None
    </Directory>

## SMW issue #515 hack

Comment method body, lines 85, 125

   vi extensions/SemanticMediaWiki/includes/datavalues/SMW_DV_WikiPage.php


## Make `mediawiki/LocalSettings.php` to contain only

    ```php
    <?php
    require_once( "$IP/../LocalSettings.php" ); // Or TestSettings.php
    ```

## Related documentation

* [SwiftCloudFiles](http://www.mediawiki.org/wiki/Extension:SwiftCloudFiles)

# Various notes


## TODO

* Disable Talk pages? http://www.mediawiki.org/wiki/Extension%3aTalkright `$wgDisableAnonTalk = false;`
* Cookie names and domain? see `$wgCookieExpiration`
* See neat plugin how it does things https://git.wikimedia.org/summary/mediawiki%2Fextensions%2FDumpHTML
* Notes tabs:
  * https://gist.github.com/renoirb/2296a50a33910ef8936a
  * https://gist.github.com/renoirb/9923697
  * http://meta.wikimedia.org/wiki/Help:User_style
  * http://www.mediawiki.org/wiki/Composer/Future_work
  * http://www.mediawiki.org/wiki/Manual:Skin.php
  * http://www.mediawiki.org/wiki/Manual:Skinning
  * http://www.mediawiki.org/wiki/Manual:Skinning/Tutorial
* Do not forget:
  * WebPlatformSearchAutocomplete
  * WebPlatformSectionCommentsSmw
  * Comments WpdCaptcha
* Code samples and GitHub extension?