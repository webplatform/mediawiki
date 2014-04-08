# WebPlaform's MediaWiki "distribution"

Custom utilities used within MediaWiki for [WebPlatform Docs](http://www.webplatform.org).

## Installation

This procedure describes how to install from scratch.

1. Clone code repository from `mediawiki/core`, from [MediaWiki Gerrit repository](https://gerrit.wikimedia.org/r/#/admin/projects/)

        git clone https://gerrit.wikimedia.org/r/mediawiki/core wiki

2. Checkout latest [`wmf/...` branch](https://gerrit.wikimedia.org/r/#/admin/projects/mediawiki/core,branches)

        cd wiki
        git checkout -t origin/wmf/1.23wmf1

3. Install submodules; It might take a while.

        git submodule init
        git submodule update

4. Clone *THIS* repository, and add the content to the latest core

        git clone git@github.com:webplatform/mediawiki.git wpd-mediawiki
        rsync -az wpd-mediawiki/skins/ wiki/skins/
        rsync -az wpd-mediawiki/extensions/ wiki/extensions/

5. Install missing extensions

    Execute each line one by one.

        git submodule add https://gerrit.wikimedia.org/r/mediawiki/extensions/AdminLinks extensions/AdminLinks
        git submodule add https://gerrit.wikimedia.org/r/mediawiki/extensions/Comments extensions/Comments
        git submodule add https://gerrit.wikimedia.org/r/mediawiki/extensions/EmailCapture extensions/EmailCapture
        git submodule add https://gerrit.wikimedia.org/r/mediawiki/extensions/LookupUser extensions/LookupUser
        git submodule add https://gerrit.wikimedia.org/r/mediawiki/extensions/Narayam extensions/Narayam
        git submodule add https://gerrit.wikimedia.org/r/mediawiki/extensions/NewSignupPage extensions/NewSignupPage
        git submodule add https://gerrit.wikimedia.org/r/mediawiki/extensions/ReplaceText extensions/ReplaceText
        git submodule add https://gerrit.wikimedia.org/r/mediawiki/extensions/SemanticInternalObjects extensions/SemanticInternalObjects
        git submodule add https://gerrit.wikimedia.org/r/mediawiki/extensions/SocialProfile extensions/SocialProfile
        git submodule add https://gerrit.wikimedia.org/r/mediawiki/extensions/StringFunctionsEscaped extensions/StringFunctionsEscaped

6. Configuration file not ready yet

    Note that you have to adjust your `LocalSettings.php` for each extension yourself. This procedure doesn't, yet, provide recommendations.