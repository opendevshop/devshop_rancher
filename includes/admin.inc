<?php

use Guzzle\Http\Client;
use Guzzle\Http\Exception\ClientErrorResponseException;

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

  // Check for API access
  $client = new Client($form_state['values']['devshop_rancher_server_url']);

  // If no secret is present, load from variables.
  if (empty($form_state['values']['devshop_rancher_api_secret'])) {
    $form_state['values']['devshop_rancher_api_secret'] = variable_get('devshop_rancher_api_secret', '');
  }

  try {
    $request = $client->get('/v1');
    $request->setAuth($form_state['values']['devshop_rancher_api_username'],
      $form_state['values']['devshop_rancher_api_secret']);

    $response = $request->send();

    drupal_set_message(t('We have successfully connected to your Rancher server!'));

  } catch (ClientErrorResponseException $e) {
    drupal_set_message(t('Unable to connect to Rancher Server. Check your credentials and try again.'));
    form_set_error('devshop_rancher_api_username', $e->getMessage());
  }
}