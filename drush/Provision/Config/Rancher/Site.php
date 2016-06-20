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
}
