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
}
