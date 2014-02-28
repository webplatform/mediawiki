#!/bin/bash

DIRMEDIAWIKI="/vagrant/project/wiki"
BRANCH="-b support-esi"  # Can be empty, if you want to use master

#
# Apply webplatform/mediawiki specific code
# on top of a MediaWiki repository clone.
#
# This script is made to apply changes coming
# from a different workspace (e.g. webplatform/mediawiki)
# and apply it inside a repository clone of MediaWiki
# therefore allowing us to have a small repository of
# our own changes to apply on top of the original
# installation. This script doesnt take care of deploying
# on the app server, it only is used to apply code
# as a patch would do.
#
# DISCLAIMER: This is not the most elegant
# way of doing things. But it works for now.
#
# @author Denis Ah-Kang <denis@w3.org>
# @author Renoir Boulanger <renoir@w3.org>
#

set -a

GITMEDIAWIKI="https://github.com/webplatform/mediawiki"

declare -A saltdir

saltdir[ext.CompaTables]="$DIRMEDIAWIKI/extensions/"
saltdir[ext.DismissableSiteNotice]="$DIRMEDIAWIKI/extensions/"
saltdir[ext.piwik]="$DIRMEDIAWIKI/extensions/"
saltdir[skins]="$DIRMEDIAWIKI/skins/"

usage ()
{
cat << EOF
Usage: deploy.sh [-c] CODEBASE 
This script allows you to deploy:
  - mediawiki extensions
  - mediawiki skins

Available CODEBASE:
`for key in ${!saltdir[@]}; do echo "- $key"; done`

EOF
}

if [ $# -ne 1 ]; then
  usage
  exit 0
elif [ "x${saltdir[$1]}" = "x" ]; then
  echo -e "\e[1;41mThis CODEBASE is not available\e[0m"
  usage
  exit 0
fi

if [[ $1 = ext.* ]]; then
  (echo -e "\e[1;42m==== Clone webplatform/mediawiki ====\e[0m"
   cd /tmp && git clone $BRANCH $GITMEDIAWIKI
   cd /tmp/mediawiki/extensions
   echo -e "\e[1;42m==== Replacing extension content in checkout repository ====\e[0m"
   rsync -az --delete ${1/ext./}/ ${saltdir[$1]}${1/ext./}/ && cd
   rm -rf /tmp/mediawiki
   )
 elif [[ $1 = skins ]]; then
  (echo -e "\e[1;42m==== Clone webplatform/mediawiki ====\e[0m"
   cd /tmp && git clone $BRANCH $GITMEDIAWIKI
   cd /tmp/mediawiki/
   echo -e "\e[1;42m==== Replacing skins content in checkout repository ====\e[0m"
   rsync -az --delete skins/common/ ${saltdir[$1]}/common/ 
   rsync -az --delete skins/webplatform/ ${saltdir[$1]}/webplatform/
   cp skins/WebPlatform.php ${saltdir[$1]}/WebPlatform.php
   rm -rf /tmp/mediawiki
   )
fi
