<?php

/**
 * Rancher Site configuration.
 */
class Provision_Config_Rancher_SiteRancherCompose extends Provision_Config_RancherConfig {

  public $template = '';
  public $description = 'Rancher compose file for this environment.';

  function filename() {
    $filename =  $this->context->web_server->http_app_path . '/' . $this->context->project . '/'. $this->context->environment . '/rancher-compose.yml';
    drush_log('Writing to ' . $filename, 'devshop_log');
    return $filename;
  }
}