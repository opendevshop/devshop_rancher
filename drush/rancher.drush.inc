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

/**
 * implementation of provision_verify
 */
function drush_rancher_pre_provision_verify() {
    if (d()->type === 'platform' && d()->web_server->http_service_type == 'rancherhttp') {

      // Write Docker Compose config file.
      $environment = d()->project->project->environments[d()->environment];
      $DockerCompose = new Provision_Config_Rancher_SiteDockerCompose(d()->name, $environment);
      $DockerCompose->write();

      // Write Rancher Compose config file.
      $rancherCompose = new Provision_Config_Rancher_SiteRancherCompose(d()->name, d()->project);
      $rancherCompose->write();
    }
}
