<?php

/** @var string Location of compatability JSON file */
$wgCompatablesJsonFileUrl = 'http://docs.webplatform.org/compat/data.json';

/** @var bool Whether to use Edge-Side-Includes for tables */
$wgCompatablesUseESI = false;

$wgAvailableRights[] = 'purgecompatables';

$wgGroupPermissions['sysop']['purgecompatables'] = true;
