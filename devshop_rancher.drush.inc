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
    drush_log('Checking RANCHER API access...', 'notice');

    if ($task->ref->services['rancher']->verify()) {
      drush_log('RANCHER: Able to connect to rancher server!', 'ok');
    }
    else {
      drush_set_error('DRUSH_RANCHER_ERROR', 'RANCHER: Unable to connect to rancher server API!');
    }
  }

  // On install, create an environment
  if ($task->task_type == 'install') {

    // Load up server node
    $site = $task->ref;
    $server = node_load($site->db_server);

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
  }
}
