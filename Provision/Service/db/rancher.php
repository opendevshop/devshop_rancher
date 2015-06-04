<?php

class Provision_Service_db_rancher extends Provision_Service_db {
  protected $application_name = 'rancher';
  protected $has_restart_cmd = FALSE;

  function init_server() {
    parent::init_server();
    drush_log('Provision_Service_db_rancher::init_server()', 'ok');
  }

  /**
   * Verifies database connection and commands
   */
  function verify_server_cmd() {
    drush_log('Provision_Service_db_rancher::init_server()', 'ok');
  }
}
