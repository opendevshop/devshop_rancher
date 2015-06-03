<?php

use Guzzle\Http\Client;
use Guzzle\Http\Exception\ClientErrorResponseException;

/**
 * Rancher settings form.
 * @return array()
 */
function devshop_rancher_hosts_form(){

  // Lookup rancher hosts.
  $client = new Client(variable_get('devshop_rancher_server_url', ''));

  try {
    $request = $client->get('/v1/hosts');
    $request->setAuth(variable_get('devshop_rancher_api_username', ''), variable_get('devshop_rancher_api_secret', ''));

    $response = $request->send();

    $data = json_decode($response->getBody()->__toString());
    drupal_set_message(t('We have successfully loaded your Rancher hosts.'));

  } catch (ClientErrorResponseException $e) {
    drupal_set_message(t('Unable to connect to Rancher Server. Check your credentials and try again.'), 'error');
    drupal_set_message($e->getMessage(), 'error');
  }


  // Create options
  foreach ($data->data as $host) {
    $options[$host->uuid] = $host->name;
  }

  $form['rancher_hosts'] = array(
    '#title' => t('Rancher Hosts'),
    '#type' => 'checkboxes',
    '#description' => t('Select the hosts you would like to import as aegir servers.'),
    '#options' => $options,
  );

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Import Servers'),
  );
  return $form;
}

/**
 * Validator for rancher settings.
 *
 * @param $form
 * @param $form_state
 */
function devshop_rancher_hosts_form_validate(&$form, &$form_state) {

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