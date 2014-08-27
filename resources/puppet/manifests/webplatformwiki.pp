#
# In `puppet/modules/local/manifests/webplatformwiki.pp`
#
# == Class: local::webplatformwiki
#
# See https://github.com/webplatform/VAGRANT.md for
# installation instructions.
#
# This file is expected to be copied into a clone of MediaWiki-Vagrant,
# in `puppet/modules/local/manifests/webplatformwiki.pp`
# along with an entry in `puppet/hieradata/*.yaml` with a line similar
# to:
#
#     ```yaml
#     classes:
#       - local::webplatformwiki
#     ```
#
# If build fails, do the following:
#  - Comment `mediawiki::skin { 'Vector': }` in `puppet/modules/mediawiki/manifests/init.pp`,
#    we dont need Vector anyway
#
class local::webplatformwiki {

    include ::role::svg

    # See ::role::wikitech
    # and also what versions they are using in
    # https://wikitech.wikimedia.org/wiki/Special:Version
    # But tweak here what we need

    $dir = '/vagrant/mediawiki'
    $wmfbranch = 'wmf/1.24wmf16'

    # See new extensions, try by enabling roles:
    # - eventlogging
    # - cirrussearch
    # - parsoid
    # - translate
    # - cite


    # Or, enable like so
    # include ::role::geshi
    # But in our case, we want to add configurations so we
    # use the class call instead of include
    mediawiki::extension { 'SyntaxHighlight_GeSHi':
        needs_update => true,
        settings => {
            wgSyntaxHighlightDefaultLang => "html5"
        }
    }

    #mediawiki::extension { 'EventLogging':
    #    priority => 20,
    #    settings => {
    #        wgEventLoggingBaseUri        => "http://localhost:8080/event.gif",
    #        wgEventLoggingDBname         => "wiki",
    #        wgEventLoggingFile           => 'udp://127.0.0.1:1234/EventLogging',
    #    },
    #}

    # Install, but disable it manually  ###########################
    #mediawiki::extension { 'NewSignupPage':
    #    needs_update => true,
    #    settings => [
    #        '$wgRegisterTrack = true;',
    #        '$wgUserStatsPointValues["referral_complete"] = 10;',
    #        '$wgAutoAddFriendOnInvite = true;',
    #        '$wgForceNewSignupPageInitialization = true;',
    #    ]
    #}
    #mediawiki::extension { 'SocialProfile':
    #    needs_update => true,
    #    settings => [
    #      '$wgUserProfileDisplay["friends"] = true;',
    #      '$wgUserProfileDisplay["foes"] = false;',
    #    ]
    #}
    # /Install, but disable it manually  ###########################

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
        'CategoryTree',
        'CheckUser',
        'Collection',
        'Nuke',
        'Renameuser',
        'TitleBlacklist',
        'EmailCapture',
        'StringFunctionsEscaped',
        'Narayam',
        'ReplaceText',
        'SemanticInternalObjects',
        'SwiftCloudFiles',
        'CodeEditor',
        'AdminLinks',
    ]:
        needs_update  => true,
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

    ## ########## Semantic MediaWiki related ################ #

    mediawiki::extension { 'SemanticMediaWiki':
        branch      =>  '1.8.0.5',
        priority    =>  5,
        settings    =>  [
            '$sfgRenameEditTabs = true;',
            'enableSemantics("webplatform");',
        ]
    }

    mediawiki::extension { 'SemanticForms':
        branch  =>  '~2.7',
    }

    mediawiki::extension { 'SemanticResultFormats':
        branch  =>  '~1.8',
    }

    ## If SMW would work with 1.24wmf branch, we could
    ## use `php::composer::require`
    ## - https://packagist.org/packages/mediawiki/semantic-result-formats
    ## - https://packagist.org/packages/mediawiki/sub-page-list
}