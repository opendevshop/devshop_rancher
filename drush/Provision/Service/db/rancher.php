<?php

class Provision_Service_db_rancherdb extends Provision_Service_db_mysql {
  protected $application_name = 'rancherdb';
  protected $has_restart_cmd = FALSE;

  function init_server() {
    parent::init_server();
    drush_log('Provision_Service_db_rancher::init_server()', 'ok');
  }

  /**
   * Verifies database connection and commands
   */
  function verify_server_cmd() {
    drush_log('Provision_Service_db_rancher::verify_server_cmd()', 'ok');
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
    drush_log('Provision_Service_db_rancher::create_site_database()', 'ok');
    return TRUE;
  }

  function can_create_database() {
    drush_log('Provision_Service_db_rancher::can_create_database()', 'ok');
    return TRUE;
  }

  function can_grant_privileges() {
    drush_log('Provision_Service_db_rancher::can_grant_privileges()', 'ok');
    return TRUE;
  }
}
