<?php

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
  return system_settings_form($form);
}