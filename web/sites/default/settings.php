<?php

/**
 * @file
 * Load services definition file.
 */


global $settings;
global $base_url;

/**
 * Include pantheon settings first to then override its config.
 */
include __DIR__ . "/settings.pantheon.php";

$settings['config_sync_directory'] = '../config/default';

// Get rid of the thousands of attempted WP hacks from the logs.
if (
  php_sapi_name() != 'cli'
  && !empty($_SERVER["REQUEST_URI"])
  && ($request_9 = substr($_SERVER["REQUEST_URI"], 0, 9))
  && in_array(
    $request_9,
    [
      '/wp-conte',
      '/wp-admin',
      '/wp-login',
      '/wp-post.',
      '/wp-inclu',
    ]
  )
) {
  header($_SERVER["SERVER_PROTOCOL"] . " 418 I'm a teapot");
  echo 'I\'m a teapot.';
  exit();
}

if (isset($_ENV['LANDO_INFO'])) {
  $lando_settings = __DIR__ . "/settings.lando.php";
  if (file_exists($lando_settings)) {
    include $lando_settings;
  }
}

/**
 * If there is a local settings file, then include it.
 */
$local_settings = __DIR__ . "/settings.local.php";
if (file_exists($local_settings)) {
  include $local_settings;
}
