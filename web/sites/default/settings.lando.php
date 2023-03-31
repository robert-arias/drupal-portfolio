<?php

/**
 * @file
 * ${fileDescription}
 */

$databases['default']['default'] = [
  'database' => 'drupal10',
  'username' => 'drupal10',
  'password' => 'drupal10',
  'prefix' => '',
  'host' => 'database',
  'port' => '3306',
  'namespace' => 'Drupal\\mysql\\Driver\\Database\\mysql',
  'driver' => 'mysql',
  'autoload' => 'core/modules/mysql/src/Driver/Database/mysql/',
];

$settings['hash_salt'] = 'kygY@fuJ9_X9REt!0qzBAX0pMmi2=FKvch8=wPq0)Th-ZcHRwzAHTjqZAx?sW';

$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.yml';
$settings['skip_permissions_hardening'] = TRUE;
$settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';
$settings['cache']['bins']['page'] = 'cache.backend.null';
$settings['skip_permissions_hardening'] = TRUE;

$config['config_split.config_split.lando']['status'] = TRUE;
$config['environment_indicator.indicator']['bg_color'] = '#337355';
$config['environment_indicator.indicator']['fg_color'] = '#337355';

$settings['trusted_host_patterns'] = [
  '^'.getenv('LANDO_APP_NAME').'\.lndo\.site$',      # lando proxy access
  '^localhost$',                                     # localhost access
  '^'.getenv('LANDO_APP_NAME').'\.localtunnel\.me$', # lando share access
  '^192\.168\.1\.100$'                               # LAN IP access
];
