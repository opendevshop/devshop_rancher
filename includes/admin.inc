<?php

use GuzzleHttp\Client;

/**
 * Rancher settings form.
 * @return array()
 */
function devshop_rancher_settings_form(){

  $form['devshop_rancher_server_url'] = array(
    '#title' => t('Rancher Server URL'),
    '#type' => 'textfield',
    '#description' => t('Enter the full URL of a running rancher server.'),
    '#default_value' => variable_get('devshop_rancher_server_url', ''),
  );
  $form['devshop_rancher_api_username'] = array(
    '#title' => t('Username (API Access Key)'),
    '#type' => 'textfield',
    '#description' => t('Rancher API access key.'),
    '#default_value' => variable_get('devshop_rancher_api_username', ''),
  );
  $form['devshop_rancher_api_secret'] = array(
    '#title' => t('Password (API Secret Key)'),
    '#type' => 'password',
    '#description' => t('Rancher API secret key.'),
    '#default_value' => variable_get('devshop_rancher_api_secret', ''),
  );

  $form['#validate'] = array(
    'devshop_rancher_settings_form_validate',
  );

  return system_settings_form($form);
}

/**
 * Validator for rancher settings.
 *
 * @param $form
 * @param $form_state
 */
function devshop_rancher_settings_form_validate(&$form, &$form_state) {

  form_set_error('devshop_rancher_server_url', t('Wrong!'));
}