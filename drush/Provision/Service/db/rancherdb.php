<?php

class Provision_Service_db_rancherdb extends Provision_Service_db_mysql {
  protected $application_name = 'rancherdb';
  protected $has_restart_cmd = FALSE;

  function init_server() {
    parent::init_server();
  }

  /**
   * Verifies database connection and commands
   */
  function verify_server_cmd() {
  }

  /**
   * We always return true because the new service will not get created until
   * create_site_database()
   *
   * @return bool|\PDO
   */
  function connect() {
    return TRUE;
  }

  /**
   * This method is at the core of preparing the site.
   *
   * Use this method to trigger the creation of the Rancher environments.
   *
   * @param array $creds
   * @return bool
   */
  function create_site_database($creds = array()) {
    drush_log('Provision_Service_db_rancher::create_site_database()', 'devshop_log');
    return TRUE;
  }

  function can_create_database() {
    drush_log('Provision_Service_db_rancher::can_create_database()', 'devshop_log');
    return TRUE;
  }

  function can_grant_privileges() {
    drush_log('Provision_Service_db_rancher::can_grant_privileges()', 'devshop_log');
    return TRUE;
  }

  /**
   * Sync filesystem changes to the server hosting this service.
   */
  function sync($path = NULL, $additional_options = array()) {
    drush_log('Provision_Service_db_rancher::sync()', 'devshop_log');
//    return $this->server->sync($path, $additional_options);
  }
}
