<?php


/**
 * Implements drush_HOOK_pre_COMMAND() for hosting_task
 *
 * Verifies access to the rancher server API.
 */
function drush_devshop_rancher_pre_hosting_task() {

  $task = &drush_get_context('HOSTING_TASK');

  if ($task->task_type == 'verify' && isset($task->ref->services['rancher'])) {
    drush_log('Checking RANCHER API access...', 'notice');

    if ($task->ref->services['rancher']->verify()) {
      drush_log('RANCHER: Able to connect to rancher server!', 'ok');
    }
    else {
      drush_set_error('DRUSH_RANCHER_ERROR', 'RANCHER: Unable to connect to rancher server API!');
    }
  }
}
