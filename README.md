# WebPlaform Docs’ MediaWiki “distribution„

This is [WebPlatform Docs MediaWiki Extension bundle](http://docs.webplatform.org/wiki/WPD:Infrastructure/Components/WebPlatformDocsExtensionBundle), it contains the theme and various micro extensions we created to make [WebPlatform Docs](http://www.webplatform.org) pages w/ MediaWiki.

[WebPlatform Docs](http://docs.webplatform.org/) wiki runs on Wikimedia Foundation build branch (`wmf/*`), see [the current MediaWiki version we are using](http://docs.webplatform.org/wiki/Special:Version#sv-software).

Use this repository as a reference to upgrade WebPlatform Docs MediaWiki packages. In order to work, refer to  **VAGRANT.md** which explains how to work locally. We are using [Wikimedia’s MediaWiki-Vagrant](http://www.mediawiki.org/wiki/MediaWiki-Vagrant) as a local workspace with [Vagrant](https://www.vagrantup.com/) and [VirtualBox "provider"](https://docs.vagrantup.com/v2/providers/).

See **DEPLOYMENT_IMPROVEMENTS.md**, to see how to deploy on a publicly available server.



## Test Data

You can get WebPlatform’ test wiki data or if you are a trusted contributor even get a full database backup to work from.

Note that we will not give the live site database dump unless you are actually a contributor and trusted by the community. The community would have to agree on it firsthand. If you already are contributing, you should be aware who has server access and who to ask for it.



# Reference

## MediaWiki Configuration parameters

If you have short memory, some helpers on how to find configuration switches.

* http://www.semantic-mediawiki.org/wiki/Help:Configuration
* in MediaWiki core `mediawiki/includes/DefaultSettings.php`



## Semantic Mediawik

Since we are using `wmf/*` MediaWiki versions, we can look at [Wikitech’s version](https://wikitech.wikimedia.org/wiki/Special:Version#sv-software) they are using.

Do not forget to rebuild the data after an upgrade to take a look at [Repairing Semantic MediaWiki data](http://semantic-mediawiki.org/wiki/Help:Repairing_SMW's_data)



## Related documentation

* [SwiftCloudFiles](http://www.mediawiki.org/wiki/Extension:SwiftCloudFiles)
* [Wikimedia’s MediaWiki-Vagrant](http://www.mediawiki.org/wiki/MediaWiki-Vagrant), local workspace
* [Wikimedia’s Labs-Vagrant](https://wikitech.wikimedia.org/wiki/Labs-vagrant), to control from a local VM, a remote one on wikitech OpenStack cluster


# TODO

Things that aren’t ready yet, or that we should not forget.

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