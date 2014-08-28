# Working with Vagrant

This should describe how you can work locally on WebPlatform Docs wiki [using MediaWiki-Vagrant](http://www.mediawiki.org/wiki/MediaWiki-Vagrant).

## To use

We will start by installing [MediaWiki-Vagrant](http://www.mediawiki.org/wiki/MediaWiki-Vagrant), and clone this workspace inside it.

Copied [MediaWiki-Vagrant *Quick Start*](http://www.mediawiki.org/wiki/MediaWiki-Vagrant#Quick_start) instructions for brievety:

    ```bash
    git clone https://github.com/wikimedia/mediawiki-vagrant.git mediawiki-vagrant
    cd mediawiki-vagrant
    git submodule update --init --recursive
    ./setup.sh
    vagrant up
    ```

If you want to know more, take a look at [MediaWiki-Vagrant *Quick Start*](http://www.mediawiki.org/wiki/MediaWiki-Vagrant#Quick_start) instructions.

**NOTE** this step do take time!

If it fails, do not worry, its most likely caused to submodules. Its a known issue that MediaWiki-Vagrant is made to be run against bleeding edge branch, and in our case, with `wmf/*` branches that still uses submodules heaviliy it will throw error like this.

    Error: git clone --recurse-submodules  --branch 'wmf/1.24wmf16' https://gerrit.wikimedia.org/r/p/mediawiki/core.git /vagrant/mediawiki returned 1 instead of one of [0]

At the end of `vagrant up` we’ll fix that and subsequent `vagrant provision` will be fixed.


### BEFORE running `vagrant up`, do the following:

* Create a file `puppet/hieradata/local.yaml`, paste contents:

    ```yaml
    mediawiki::branch: "wmf/1.24wmf16"

    classes:
      - local::webplatformwiki
    ```

* Create a file `puppet/modules/local/manifests/webplatformwiki.pp`, paste contents from file from THIS project, the file is inside `resources/puppet/webplatformwiki.pp`.

Note that eventually this project will also be installed through Composer and a few steps here will be adjusted.


## AFTER `vagrant up`

* If you got an error similar to (`Error: git clone --recurse-submodules ...`), let’s get our submodules manually


    ```bash
    vagrant ssh
    cd /vagrant/mediawiki
    git submodule update --init --recursive
    ```


* Try provision the Vagrant VM again

    ```bash
    vagrant provision
    ```

  There should be no errors anymore


* Clone this repository as `mediawiki/extensions/WebPlatformDocs/`

    ```bash
    git clone -b 201408-upgrade https://github.com/webplatform/mediawiki.git mediawiki/extensions/WebPlatformDocs
    ```

  NOTE: This is in the meantime we get our own two MW extensions (WebPlatform MediaWiki Extension Bundle AND WebPlatform SSO Extension), in the meantime no commiteable code on deployment server anymore (!).

* Clone the SSO project as `mediawiki/extensions/WebPlatformAuth/`

    ```bash
    git clone -b webplatform-sso https://github.com/webplatform/mediawiki-fxa-sso.git mediawiki/extensions/WebPlatformAuth
    ```

* Copy the `resources/settings.d/*.php` files from THIS project into our new MediaWiki-Vagrant clone `settings.d/` folder

    ```bash
    cp -r mediawiki/extensions/WebPlatformDocs/resources/settings.d/ settings.d/
    ```



# References

## Previous attempts

Each iteration of this documentation will have a snapshot of the files used to generate the VM.

* [2014082700](https://gist.github.com/renoirb/27a911fd67118f640e51)