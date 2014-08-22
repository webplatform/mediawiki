# Working with Vagrant

This should describe how you can work locally on WebPlatform Docs wiki [using MediaWiki-Vagrant](http://www.mediawiki.org/wiki/MediaWiki-Vagrant).

## Notes

Follow directives at [MediaWiki-Vagrant *Quick Start*](http://www.mediawiki.org/wiki/MediaWiki-Vagrant#Quick_start) instructions.

### Before running `vagrant up`, do the following:

* Create a file `puppet/hieradata/local.yaml`, paste contents:

    ```yaml
    mediawiki::branch: "wmf/1.24wmf16"

    classes:
      - local::webplatformwiki
    ```

* Create a file `puppet/modules/local/webplatformwiki.pp`, paste contents:

    ```
    # == Class: local::webplatformwiki
    class local::webplatformwiki {

        include ::role::svg

        $dir = '/vagrant/mediawiki'

        # Or, enable like so
        # include ::role::geshi
        mediawiki::extension { 'SyntaxHighlight_GeSHi':
            needs_update => true,
            settings => {
                wgSyntaxHighlightDefaultLang => "html5"
            }
        }

        mediawiki::extension { 'Comments':
            priority => 5,
            needs_update => true,
            settings => [
                '$wgCommentsEnabledNS = array(NS_MAIN);',
            ]
        }

        mediawiki::extension { 'Contributors':
            needs_update => true,
            settings => [
              '$wgContributorsLimit = 11;',
              '$wgContributorsThreshold = 5;',
            ]
        }
        mediawiki::extension { [
            'EmailCapture',
            'Nuke',
            'Renameuser',
            'CategoryTree',
            'StringFunctionsEscaped',
            'Gadgets',
            'Narayam',
            'ReplaceText',
            'SemanticInternalObjects',
            'SwiftCloudFiles',
        ]:
            needs_update  => true,
        }

        exec { 'composer-require-semantic-media-wiki':
            command => 'composer require "mediawiki/semantic-media-wiki" "~2.0"',
            cwd     => $dir,
            unless  => 'grep "semantic-media-wiki" composer.json',
            require => File['/usr/local/bin/composer'],
            environment => [
              'COMPOSER_HOME=/vagrant/cache/composer',
              'COMPOSER_CACHE_DIR=/vagrant/cache/composer',
            ]
        }

        exec { 'composer-require-admin-links':
            command => 'composer require "enterprisemediawiki/admin-links" "dev-master"',
            cwd     => $dir,
            unless  => 'grep "admin-links" composer.json',
            require => File['/usr/local/bin/composer'],
            environment => [
              'COMPOSER_HOME=/vagrant/cache/composer',
              'COMPOSER_CACHE_DIR=/vagrant/cache/composer',
            ]
        }

        exec { 'composer-require-semantic-result-formats':
            command => 'composer require "mediawiki/semantic-result-formats" "~2.0"',
            cwd     => $dir,
            unless  => 'grep "semantic-result-formats" composer.json',
            require => File['/usr/local/bin/composer'],
            environment => [
              'COMPOSER_HOME=/vagrant/cache/composer',
              'COMPOSER_CACHE_DIR=/vagrant/cache/composer',
            ]
        }

        exec { 'composer-require-sub-page-list':
            command => 'composer require "mediawiki/sub-page-list" "~1.1"',
            cwd     => $dir,
            unless  => 'grep "sub-page-list" composer.json',
            require => File['/usr/local/bin/composer'],
            environment => [
              'COMPOSER_HOME=/vagrant/cache/composer',
              'COMPOSER_CACHE_DIR=/vagrant/cache/composer',
            ]
        }

        exec { 'composer-require-semantic-forms':
            command => 'composer require "mediawiki/semantic-forms" "~2.7"',
            cwd     => $dir,
            unless  => 'grep "semantic-forms" composer.json',
            require => File['/usr/local/bin/composer'],
            environment => [
              'COMPOSER_HOME=/vagrant/cache/composer',
              'COMPOSER_CACHE_DIR=/vagrant/cache/composer',
            ]
        }

        exec { 'composer-require-mediawiki-github':
            command => 'composer require "jeroen-de-dauw/mediawiki-github" "@dev"',
            cwd     => $dir,
            unless  => 'grep "mediawiki-github" composer.json',
            require => File['/usr/local/bin/composer'],
            environment => [
              'COMPOSER_HOME=/vagrant/cache/composer',
              'COMPOSER_CACHE_DIR=/vagrant/cache/composer',
            ]
        }

        mediawiki::extension { 'ConfirmEdit':
            priority => 5,
            needs_update => true,
            settings => [
              '$wgCaptchaTriggers["edit"]          = false;',
              '$wgCaptchaTriggers["create"]        = false;',
              '$wgCaptchaTriggers["addurl"]        = false;',
              '$wgCaptchaTriggers["createaccount"] = true;',
              '$wgCaptchaTriggers["badlogin"]      = false;',
            ]
        }

        mediawiki::extension { 'ParserFunctions':
            needs_update => true,
            settings => [
                '$wgPFEnableStringFunctions = true;'
            ]
        }

        mediawiki::extension { 'WikiEditor':
            needs_update => true,
            settings => [
                '$wgDefaultUserOptions["usebetatoolbar"] = 1;',
                '$wgDefaultUserOptions["usebetatoolbar-cgd"] = 1;',
            ]
        }

        mediawiki::extension { 'AbuseFilter':
            needs_update => true,
            settings => [
                '$wgGroupPermissions["sysop"]["abusefilter-modify"] = true;',
                '$wgGroupPermissions["*"]["abusefilter-log-detail"] = true;',
                '$wgGroupPermissions["*"]["abusefilter-view"] = true;',
                '$wgGroupPermissions["*"]["abusefilter-log"] = true;',
                '$wgGroupPermissions["sysop"]["abusefilter-private"] = true;',
                '$wgGroupPermissions["sysop"]["abusefilter-modify-restricted"] = true;',
                '$wgGroupPermissions["sysop"]["abusefilter-revert"] = true;',
            ]
        }

        mediawiki::extension { 'LookupUser':
            needs_update => true,
            settings => [
                '$wgGroupPermissions["*"]["lookupuser"] = false;',
                '$wgGroupPermissions["sysop"]["lookupuser"] = true;',
            ]
        }
    }
    ```

## AFTER having run `vagrant up`

* Clone this repository as `mediawiki/extensions/WebPlatformDocs/`

* Create a file in `settings.d/01-webplatform.php`, paste contents:

  NOTE that this is where I put what is common to ALL instances.

    ```php
    <?php

    // Config options, see:
    // * http://www.semantic-mediawiki.org/wiki/Help:Configuration
    // * includes/DefaultSettings.php

    require_once("$IP/extensions/WebPlatformDocs/main.php");
    ```