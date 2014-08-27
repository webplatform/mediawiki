# Deployment improvement notes

This is what has to be run to deploy on production servers.

Note that what is commited here is what’s needed to run in production based off of the configuration we get from MediaWiki-Vagrant build.

This is to have as less difference as possible between production and development configuration.


## Add this to VHost


### In the MediaWiki-Vagrant VM

    Alias /w/skins/webplatform "/vagrant/mediawiki/extensions/WebPlatformDocs/skin"
    <Directory /vagrant/mediawiki/extensions/WebPlatformDocs/skin>
        Options FollowSymLinks
        AllowOverride None
    </Directory>

### In production

Look at WebPlatform’s deployment server in `/srv/salt/apache/docs` in WPD’s salt states. But it should already be managed, here is just notes for posterity.

    Alias /w/skins/webplatform "/srv/webplatform/wiki/wpwiki/mediawiki/extensions/WebPlatformDocs/skin"
    <Directory /srv/webplatform/wiki/wpwiki/mediawiki/extensions/WebPlatformDocs/skin>
        Options FollowSymLinks
        AllowOverride None
        Require all granted
    </Directory>

In production we have two wikis, `wpwiki` (for /wiki/) and `wptestwiki` (for /test/) for production and template tests purposes.


## Copy a few files

    cp -r mediawiki/extensions/WebPlatformDocs/resources/settings.d/ settings.d/

