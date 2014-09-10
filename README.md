# WebPlaform Docs’ MediaWiki "distribution"

This is [WebPlatform Docs MediaWiki Extension bundle](http://docs.webplatform.org/wiki/WPD:Infrastructure/Components/WebPlatformDocsExtensionBundle), it contains the theme and various micro extensions we created to make [WebPlatform Docs](http://www.webplatform.org) pages w/ [MediaWiki](https://github.com/wikimedia/mediawiki-core).

The [WebPlatform Project](http://www.webplatform.org/) [documentation pages is a wiki](http://docs.webplatform.org/wiki/) running with Wikimedia Foundation [Wikipedia continuous deployment branch `wmf/*`](https://github.com/wikimedia/mediawiki-core/branches).

**NOTE**: You can see current release we are running on our [Special:Version page](http://docs.webplatform.org/wiki/Special:Version#sv-software).

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
* See publicly visible configuration parameters [in this gist](https://gist.github.com/WebPlatformDocs/9e79a3e6816063103649)


## Semantic Mediawik

Since we are using `wmf/*` MediaWiki versions, we can look at [Wikitech’s version](https://wikitech.wikimedia.org/wiki/Special:Version#sv-software) they are using.

Do not forget to rebuild the data after an upgrade to take a look at [Repairing Semantic MediaWiki data](http://semantic-mediawiki.org/wiki/Help:Repairing_SMW's_data)



## Some Extensions documentation bookmarks

* [SwiftCloudFiles](http://www.mediawiki.org/wiki/Extension:SwiftCloudFiles)
* [Wikimedia’s MediaWiki-Vagrant](http://www.mediawiki.org/wiki/MediaWiki-Vagrant), local workspace
* [Wikimedia’s Labs-Vagrant](https://wikitech.wikimedia.org/wiki/Labs-vagrant), to control from a local VM, a remote one on wikitech OpenStack cluster

## Scratchpad

See [the project wiki](https://github.com/webplatform/mediawiki/wiki)
