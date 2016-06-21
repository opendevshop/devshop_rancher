<?php

use Symfony\Component\Yaml\Yaml;

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

    // Find docker compose folder and run docker-compose up in it.
    $cwd = d($this->context->db_server)->http_app_path . '/' . d()->project . '_' . d()->environment;

    d()->service('Process')->process('docker-compose up -d', $cwd, dt('Launching Containers'));

    // Detect new hostname for the database container.
    $container_name = d()->project . d()->environment;
    $info = d()->service('Process')->process("docker inspect {$container_name}_database_1", $cwd, dt('Detecting IP'), array(), FALSE);

    $data = json_decode($info)[0];

    $ip_address = $data->NetworkSettings->IPAddress;

    drush_log('Container IP Address found.' .$ip_address, 'devshop_ok');

//    $config = new Provision_Config_Drupal_Settings(d()->name, drush_get_context('site'));
//    $config->write();
    
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

  function generate_site_credentials() {
    $creds = array();
    // replace with service type
    $db_type = drush_get_option('db_type', function_exists('mysqli_connect') ? 'mysqli' : 'mysql');
    // As of Drupal 7 there is no more mysqli type
    if (drush_drupal_major_version() >= 7) {
      $db_type = ($db_type == 'mysqli') ? 'mysql' : $db_type;
    }


    // Our DB Credentials were saved into docker-compose.yml. Get them from there.
    $yml = file_get_contents(d($this->context->db_server)->http_app_path . '/' . d()->project . '_' . d()->environment . '/docker-compose.yml');

    $data = Yaml::parse($yml);

    drush_log(print_r($data, 1), 'ok');

//    $creds['db_type'] = drush_set_option('db_type', $db_type, 'site');
//    $creds['db_host'] = drush_set_option('db_host', $this->server->remote_host, 'site');
//    $creds['db_port'] = drush_set_option('db_port', $this->server->db_port, 'site');
//    $creds['db_passwd'] = drush_set_option('db_passwd', provision_password(), 'site');
//    $creds['db_name'] = drush_set_option('db_name', $this->suggest_db_name(), 'site');
//    $creds['db_user'] = drush_set_option('db_user', $creds['db_name'], 'site');
//
    return $creds;
  }

}
