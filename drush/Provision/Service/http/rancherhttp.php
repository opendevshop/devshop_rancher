<?php

class Provision_Service_http_rancherhttp extends Provision_Service_http_apache_ssl {
  protected $application_name = 'rancherhttp';
  protected $has_restart_cmd = FALSE;

  function verify_server_cmd() {
    drush_log('Provision_Service_http_rancherhttp::verify_server_cmd()', 'devshop_log');
  }

  function init_server() {
    parent::init_server();
    $this->configs['server'] = array();
    $this->configs['platform'] = array();
    $this->configs['site'] = array();
  }

  function init_platform() {
    parent::init_platform();
    $this->configs['server'] = array();
    $this->configs['platform'] = array();
    $this->configs['site'] = array();
  }

  function init_site() {
    parent::init_site();
    $this->configs['server'] = array();
    $this->configs['platform'] = array();
    $this->configs['site'] = array();
    $this->configs['site'][] = 'Provision_Config_Rancher_SiteDockerCompose';
    $this->configs['site'][] = 'Provision_Config_Rancher_SiteRancherCompose';
  }

  /**
   * Sync filesystem changes to the server hosting this service.
   */
  function sync($path = NULL, $additional_options = array()) {
    drush_log('Provision_Service_http_rancherhttp::sync()', 'devshop_log');
//    return $this->server->sync($path, $additional_options);
  }
}
