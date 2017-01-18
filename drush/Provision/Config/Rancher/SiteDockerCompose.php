<?php

/**
 * Rancher Site configuration.
 */
class Provision_Config_Rancher_SiteDockerCompose extends Provision_Config_RancherConfig {

  public $template = '';
  public $description = 'docker compose file for this environment.';

  function filename() {
    $filename =  $this->context->web_server->http_app_path . '/' . $this->context->project . '/'. $this->context->environment . '/docker-compose.yml';
    drush_log('Writing to ' . $filename, 'devshop_log');
    return $filename;
  }

  function generateYml(){

//    $project = (object) d('@project_' . $this->context->project)->project;
//    if (empty($project->environments)) {
//      return drush_set_error(DRUSH_APPLICATION_ERROR, dt('No environments found in your project. Save project settings to trigger a project verify.'));
//    }
//    $environment = (object) $project->environments[$this->context->environment];

//    $source_root = $environment->repo_root;
//$this->context->repo_root;



    if ($this->context->root != $this->context->repo_path) {
      $document_root_relative = str_replace($this->context->repo_path, '', $this->context->root);
      $document_root = $this->context->root;
    } else {
      $document_root_relative = '';
      $document_root = $this->context->root;
    }

    // @TODO: Load authorized keys from users allowed to acces the project.
    $ssh_authorized_keys = '';


    // Get Virtual Hosts array
    if (!empty($this->context->aliases)) {
      $hosts = implode(',', $this->context->aliases);
    }

    $compose = array();
    $compose['load'] = array(
      'image' => 'tutum/haproxy',
      'environment' => array(
        'VIRTUAL_HOST' => $hosts,
      ),
      'links' => array(
        'app',
      ),
      'expose' => array(
        '80/tcp',
      ),
      'ports' => array(
        '80',
      ),
    );
    $compose['app'] = array(
      'image' => 'terra/drupal',
      'tty' => true,
      'stdin_open' => true,
      'links' => array(
        'database',
      ),
      'volumes' => array(
        "{$this->context->repo_path}:/app",
      ),
      'environment' => array(
        'HOST_UID' => posix_getuid(),
        'HOST_GID' => posix_getgid(),
        'DOCUMENT_ROOT' => $document_root_relative,
      ),
      'expose' => array(
        '80/tcp',
      ),
    );
    $compose['database'] = array(
      'image' => 'mariadb',
      'tty' => true,
      'stdin_open' => true,
      'environment' => array(
        'MYSQL_ROOT_PASSWORD' => 'RANDOMIZEPLEASE',
        'MYSQL_DATABASE' => 'drupal',
        'MYSQL_USER' => 'drupal',
        'MYSQL_PASSWORD' => 'drupal',
      ),
    );
    $compose['drush'] = array(
      'image' => 'terra/drush',
      'tty' => true,
      'stdin_open' => true,
      'links' => array(
        'database',
      ),
      'ports' => array(
        '22',
      ),
      'volumes' => array(
        "$document_root:/var/www/html",
        "{$this->context->repo_path}:/source",
      ),
      'environment' => array(
        'AUTHORIZED_KEYS' => $ssh_authorized_keys,
      ),
    );
    
    return $compose;
  }
}
