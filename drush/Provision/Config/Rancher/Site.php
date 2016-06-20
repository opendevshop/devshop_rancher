<?php

/**
 * Rancher Site configuration.
 */
class Provision_Config_Rancher_Site extends Provision_Config_Rancher {
  public $template = 'docker-compose.yml.php';

  // The template file to use when the site has been disabled.
  public $description = 'docker compose file for this environment.';

  function process() {
    parent::process();

    $project_name = d('@' . $this->uri)->project;
    $environment_name = d('@' . $this->uri)->environment;

    if ($environment = d('@project_' . $project_name)->getEnvironment($environment_name)) {

      $this->data['environment'] = $environment;

      // Prepare domains for VIRTUAL_HOST environment variable
      $this->data['virtual_hosts'] = implode(',', $environment->domains);

      // Doc Root
      $this->data['document_root'] = d('@project_' . $project_name)->project['drupal_path'];

      // MySQL Root password, generated.
      $this->data['mysql_root_password'] = provision_password(32);

      $this->data['mysql_credentials'] = $this->generate_site_credentials();

      // @TODO: detect automatically.
      $this->data['host_uid'] = '12345';
      $this->data['host_gid'] = '12345';

      $this->data['authorized_keys'] = file_get_contents('/var/aegir/.ssh/id_rsa.pub');
    }
    else {
      return;
    }
  }

  function filename() {
    return $this->data['server']->http_app_path . '/' . $this->uri . '/docker-compose.yml';
  }

  function generate_site_credentials() {
    $creds = array();
    // replace with service type
    $db_type = drush_get_option('db_type', function_exists('mysqli_connect') ? 'mysqli' : 'mysql');
    // As of Drupal 7 there is no more mysqli type
    if (drush_drupal_major_version() >= 7) {
      $db_type = ($db_type == 'mysqli') ? 'mysql' : $db_type;
    }

    //TODO - this should not be here at all
    $creds['db_type'] = drush_set_option('db_type', $db_type, 'site');

    // Because we are using docker, host and port are static
    $creds['db_host'] = drush_set_option('db_host','database', 'site');
    $creds['db_port'] = drush_set_option('db_port', 3306, 'site');
    
    $creds['db_passwd'] = drush_set_option('db_passwd', provision_password(), 'site');
    $creds['db_name'] = drush_set_option('db_name', 'drupal', 'site');
    $creds['db_user'] = drush_set_option('db_user', $creds['db_name'], 'site');

    return $creds;
  }
}
