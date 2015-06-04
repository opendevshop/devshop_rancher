<?php

/**
 * Rancher Site configuration.
 */
class Provision_Config_Rancher_Site extends Provision_Config_Rancher {
  public $template = 'docker-compose.yml.php';

  // The template file to use when the site has been disabled.
  public $description = 'docker compose file for this environment.';


  function filename() {
    drush_log('==========================================', 'ok');
    drush_log('Provision_Config_Rancher_Site::filename()', 'ok');
    return $this->data['server']->http_app_path . '/' . $this->uri . '/docker-compose.yml';
  }
}
