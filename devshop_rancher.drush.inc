<?php


/**
 * Implements drush_HOOK_pre_COMMAND() for hosting_task
 *
 * Verifies access to the rancher server API.
 */
function drush_devshop_rancher_pre_hosting_task() {

  $task = &drush_get_context('HOSTING_TASK');

  // On verify, connect to Rancher Server API
  if ($task->task_type == 'verify' && isset($task->ref->services['rancher'])) {
    drush_log('Checking Rancher API access...', 'devshop_command');

    if ($task->ref->services['rancher']->verify()) {
      drush_log('Successfully connected to Rancher server!', 'devshop_ok');
    }
    else {
      drush_set_error('DRUSH_RANCHER_ERROR', 'Unable to connect to Rancher API. Check your credentials and try again.');
    }
  }

  // Environment: Install
  // Create a Rancher Environment
  // @TODO: Move this stuff to a class.
  if ($task->task_type == 'install' && isset($task->ref->rancher_server)) {

    // Load up server node
    $site = $task->ref;
    $server = node_load($site->db_server);
    $platform = node_load($site->platform);

    // Lookup "projects"
    $projects_request = $server->services['rancher']->get('projects');
    if (!method_exists($projects_request, 'getBody')) {
      return drush_set_error('DEVSHOP_RANCHER_ERROR', 'Error when trying to retrieve Projects.');
    }

    $projects_data = json_decode($projects_request->getBody(TRUE));

    // Just use the default project for now.
    $project = array_shift($projects_data->data);

    // Create an environment in this project.
    $environment_name = strtr($site->environment->name, array(
      '_' => '-',
    ));

    $environment = new stdClass();
    $environment->name = $site->project->name . '-' . $environment_name;
    $environment->description = "Environment: {$environment_name} Project: {$site->project->name}";

    $environment_request = $server->services['rancher']->post("projects/{$project->id}/environments", $environment);
    if (!method_exists($environment_request, 'getBody')) {
      return drush_set_error('DEVSHOP_RANCHER_ERROR', 'Error when trying to post an environment.');
    }

    $environment_object = json_decode($environment_request->getBody(TRUE));

    // Save Rancher object to environment

    // Ensure correct data types
    $site->environment->settings->rancher_object = $environment_object;
    $site->environment->settings = (array) $site->environment->settings;

    // Prepare record for saving
    $info = new stdClass();
    $info->project_nid = $site->environment->project_nid;
    $info->name = $site->environment->name;
    $info->git_ref = $site->environment->git_ref;
    $info->site = $site->environment->site;
    $info->platform = $site->environment->platform;
    $info->settings = serialize($site->environment->settings);

    // Save environment record.
    if (drupal_write_record('hosting_devshop_project_environment', $info, array('project_nid', 'name'))) {
      drush_log("Created environment, record saved: " . print_r($environment_object, 1), 'ok');
    }
    else {
      drush_log('Something went wrong when saving environment record.', 'warning');
    }

    // Create Services
    // @TODO: Devise a service template system.
    $services = array();

    // Service: database
    $service = new stdClass();
    $service->environmentId = $environment_object->id;
    $service->name = 'database';
    $service->scale = 1;
    $service->launchConfig = new stdClass();
    $service->launchConfig->imageUuid = 'docker:mariadb';
    $service->launchConfig->environment = new stdClass();
    $service->launchConfig->environment->MYSQL_ROOT_PASSWORD = provision_password();
    $service->launchConfig->environment->MYSQL_DATABASE = 'drupal';
    $service->launchConfig->environment->MYSQL_USER = 'drupal';
    $service->launchConfig->environment->MYSQL_PASSWORD = 'drupal';

    $service_request = $server->services['rancher']->post("projects/{$project->id}/services", $service);
    $services['database'] = json_decode($service_request->getBody(TRUE));
    drush_log("Attempting to create service DATABASE: " . print_r($services['database'], 1), 'ok');

    // Service: app
    $service = new stdClass();
    $service->environmentId = $environment_object->id;
    $service->name = 'app';
    $service->scale = 1;
    $service->launchConfig = new stdClass();
    $service->launchConfig->imageUuid = 'docker:terra/drupal';
    $service->launchConfig->dataVolumes = array(
      "{$platform->publish_path}:/usr/share/nginx/html"
    );

    $service_request = $server->services['rancher']->post("projects/{$project->id}/services", $service);
    $services['app'] = json_decode($service_request->getBody(TRUE));
    drush_log("Attempting to create service APP: " . print_r($services['app'], 1), 'ok');

    // Link "app" to "database"
    $link = new stdClass();
    $link->serviceId = $services['database']->id;
    $link->name = 'database';
    $service_request = $server->services['rancher']->post("projects/{$project->id}/services/{$services['app']->id}/?action=addservicelink", $link);

    $service_response = json_decode($service_request->getBody(TRUE));

    drush_log("Attempting to create service link: " . print_r($service_response, 1), 'ok');

    // @TODO: Service: balance
    // Service: balance
    $service = new stdClass();
    $service->environmentId = $environment_object->id;
    $service->name = 'balance';
    $service->scale = 1;
    $service->launchConfig = new stdClass();
    $service->launchConfig->ports = array(
      "80:80",
    );

    $service_request = $server->services['rancher']->post("projects/{$project->id}/loadbalancerservices", $service);
    $services['balance'] = json_decode($service_request->getBody(TRUE));
    drush_log("Attempting to create service BALANCE: " . print_r($services['balance'], 1), 'ok');

    // Link "app" to "balance"
    $link = new stdClass();
    $link->serviceId = $services['app']->id;
    $link->name = 'app';
    $service_request = $server->services['rancher']->post("projects/{$project->id}/loadbalancerservices/{$services['balance']->id}/?action=addservicelink", $link);

    $service_response = json_decode($service_request->getBody(TRUE));

    drush_log("Attempting to create service link for BALANCE: " . print_r($service_response, 1), 'ok');


    // @TODO: Service: drush

    // Activate Services
    $obj = new stdClass();
    $service_request = $server->services['rancher']->post("projects/{$project->id}/environments/{$environment_object->id}/?action=activateservices", $obj);
    $service_response = json_decode($service_request->getBody(TRUE));

    drush_log("Attempting to start services: " . print_r($service_response, 1), 'ok');

  }

  // Environment: Delete
  if ($task->task_type == 'delete' && isset($task->ref->rancher_server)) {

  }
}
