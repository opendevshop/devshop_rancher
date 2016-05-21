<?php

class Provision_Service_http_rancherhttp extends Provision_Service_http_apache_ssl {
  protected $application_name = 'rancherdb';
  protected $has_restart_cmd = FALSE;

  function init_server() {
    drush_log('Provision_Service_http_rancher::init_server()', 'devshop_log');
    parent::init_server();
    $this->configs['site'][] = 'Provision_Config_Rancher_Site';
  }


  function verify_server_cmd() {
    drush_log('Provision_Service_http_rancher::verify_server_cmd()', 'devshop_log');
  }

  /**
   * Sync filesystem changes to the server hosting this service.
   */
  function sync($path = NULL, $additional_options = array()) {
    drush_log('Provision_Service_http_rancher::sync()', 'devshop_log');
//    return $this->server->sync($path, $additional_options);
  }
}
