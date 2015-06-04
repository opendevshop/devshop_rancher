<?php

/**
 * Implements hook_drush_init()
 */
function rancher_drush_init(){
  rancher_provision_register_autoload();
}

/**
 * Register our directory as a place to find Provision classes.
 *
 * This allows Provision to autoload our classes, so that we don't need to
 * specifically include the files before we use the class.
 */
function rancher_provision_register_autoload() {
  static $loaded = FALSE;
  if (!$loaded) {
    $loaded = TRUE;
    provision_autoload_register_prefix('Provision_', dirname(__FILE__));
  }
}

/**
 * Implements hook_provision_services()
 *
 * @TODO: This is needed to run the autoloader properly.  What's the point of using hook_drush_init() then!?
 */
function rancher_provision_services() {
  rancher_provision_register_autoload();
}
