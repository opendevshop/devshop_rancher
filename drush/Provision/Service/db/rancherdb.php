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

    // This method usually fires off the other methods can_create_databases and can_grant_privileges.
    // As long as it doesn't call the parent method, we don't need to override those two.
    drush_log('Provision_Service_db_rancher::verify_server_cmd()', 'devshop_log');
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

    // Write Docker compose file
    $file = new Provision_Config_Rancher_Site($this->name);
    $file->write();
    
    // Find docker compose folder and run docker-compose up in it.
    $cwd = d($this->context->db_server)->http_app_path . '/' . d()->project . '_' . d()->environment;

    return d()->service('Process')->process('docker-compose up -d', $cwd, dt('Launching Containers'));

  }

  /**
   * Called when a site is rolled back or deleted.
   *
   * Use this method to trigger the destruction of rancher environments.
   *
   * @param array $creds
   * @return bool
   */
  function destroy_site_database($creds = array()) {
    // Find docker compose folder and run docker-compose up in it.
    $cwd = d($this->context->db_server)->http_app_path . '/' . d()->project . '_' . d()->environment;

    // Kill Containers
    d()->service('Process')->process('docker-compose kill', $cwd, dt('Destroying Containers'));

    // Remove containers
    d()->service('Process')->process('docker-compose rm -f -v -a', $cwd, dt('Destroying Containers'));

  }

  /**
   * Sync filesystem changes to the server hosting this service.
   */
  function sync($path = NULL, $additional_options = array()) {
    drush_log('Provision_Service_db_rancher::sync()', 'devshop_log');
//    return $this->server->sync($path, $additional_options);
  }
}
