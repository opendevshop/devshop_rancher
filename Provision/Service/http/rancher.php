<?php

class Provision_Service_http_rancher extends Provision_Service_http_public {
  protected $application_name = 'rancher';
  protected $has_restart_cmd = FALSE;

  function init_server() {
//    parent::init_server();

    drush_log('Provision_Service_http_rancher::init_server()', 'ok');
  }


  function verify_server_cmd() {
    if (!is_null($this->application_name)) {

//      // Ensure that the base apache configuration folder is at least permissive
//      // for users other than the owner, sub folders and files can further
//      // restrict access normally.
//      provision_file()->create_dir($this->server->http_app_path, dt("Webserver custom pre-configuration"), 0711);
//
//      provision_file()->create_dir($this->server->http_pred_path, dt("Webserver custom pre-configuration"), 0700);
//      $this->sync($this->server->http_pred_path);
//      provision_file()->create_dir($this->server->http_postd_path, dt("Webserver custom post-configuration"), 0700);
//      $this->sync($this->server->http_postd_path);
//
//      provision_file()->create_dir($this->server->http_platformd_path, dt("Webserver platform configuration"), 0700);
//      $this->sync($this->server->http_platformd_path, array(
//        'exclude' => $this->server->http_platformd_path . '/*',  // Make sure remote directory is created
//      ));
//
//      provision_file()->create_dir($this->server->http_vhostd_path , dt("Webserver virtual host configuration"), 0700);
//      $this->sync($this->server->http_vhostd_path, array(
//        'exclude' => $this->server->http_vhostd_path . '/*',  // Make sure remote directory is created
//      ));
//
//      provision_file()->create_dir($this->server->http_subdird_path, dt("Webserver subdir configuration"), 0700);

//      $this->sync($this->server->http_subdird_path, array(
//        'exclude' => $this->server->http_subdird_path . '/*',  // Make sure remote directory is created
//      ));
    }

//    parent::verify_server_cmd();

//    $this->create_config($this->context->type);
//    $this->parse_configs();

    drush_log('Provision_Service_http_rancher::verify_server_cmd()', 'ok');
  }

  /**
   * Sync filesystem changes to the server hosting this service.
   */
  function sync($path = NULL, $additional_options = array()) {
    drush_log('Provision_Service_http_rancher::sync()', 'ok');

//    return $this->server->sync($path, $additional_options);
  }
}
