<?php

module_load_include('service.inc', 'hosting_web_server');
module_load_include('service.inc', 'hosting_db_server');

use Guzzle\Http\Client;
use Guzzle\Http\Exception\ClientErrorResponseException;

/**
 * Class hostingService_rancher
 *
 * Base class for rancher server and agent.
 */
class hostingService_rancher extends hostingService {
  public $service = 'rancher';
}

/**
 * Class hostingService_rancher_server
 *
 * Class for handling Rancher Server.
 */
class hostingService_rancher_server extends hostingService_rancher {
  public $type = 'server';

  protected $has_port = TRUE;

  function default_port() {
    return 8000;
  }

  public function view(&$render) {
    unset($render['port']);

    $url = "http://" . $this->server->title . ':' . $this->default_port();
    $render['port'] = array(
      '#type' => 'item',
      '#title' => t('Rancher URL'),
      '#value' => l($url, $url),
    );
  }

  function form(&$form) {
    parent::form($form);

    $form['api_username'] = array(
      '#title' => t('Username (API Access Key)'),
      '#type' => 'textfield',
      '#description' => t('Rancher API access key.'),
      '#default_value' => $this->api_username,
    );

    if (empty($this->api_secret)) {
      $form['api_secret'] = array(
        '#title' => t('Password (API Secret Key)'),
        '#type' => 'password',
        '#description' => t('Rancher API secret key.'),
        '#default_value' => $this->api_secret,
      );
    }
    else {
      $form['api_secret'] = array(
        '#type' => 'value',
        '#value' => $this->api_secret,
      );
    }
  }

  /**
   * Saves the server API credentials as variables.
   * @param $node
   */
  public function save($node) {
    variable_set("devshop_rancher_server_{$node->title}_api_username", $node->services['rancher']->api_username);
    if (!empty($node->services['rancher']->api_secret)) {
      variable_set("devshop_rancher_server_{$node->title}_api_secret", $node->services['rancher']->api_secret);
    }
    parent::save($node);
  }

  /**
   * Loads the server API credentials into the service.
   */
  public function load() {
    parent::load();
    $this->setValues(array(
      'api_username' => variable_get("devshop_rancher_server_{$this->server->title}_api_username", ''),
      'api_secret' => variable_get("devshop_rancher_server_{$this->server->title}_api_secret", '')
    ));
  }


  /**
   * Validates the server API credentials.
   *
   * @param $node
   * @param $form
   */
  public function validate(&$node, &$form) {
    parent::validate($node, $form);

    if ($this->verify()) {
      drupal_set_message(t('We have successfully connected to your Rancher server!'));
    } else {
      form_set_error('api_username', t('Unable to connect to Rancher Server. Check your credentials and try again.'));
    }
  }

  /**
   * Verify that the rancher server API is accessible.
   *
   * @param $node
   * @param $form
   */
  public function verify() {

    // Check for API access
    $client = new Client('http://' . $this->server->title . ':' . $this->default_port());

    try {
      $request = $client->get('/v1');
      $request->setAuth($this->api_username, $this->api_secret);
      $response = $request->send();
      return TRUE;
    } catch (ClientErrorResponseException $e) {
      return FALSE;
    }
  }
}

/**
 * Class hostingService_rancher_agent
 * Base class for rancher agent servers.
 */
class hostingService_rancher_agent extends hostingService {
  public $type = 'rancher';

}

/**
 * Class hostingService_http_rancher
 *
 * Rancher for HTTP Server
 */
class hostingService_http_rancher_web extends hostingService_http {
  public $type = 'rancher';

  function form(&$form) {
    parent::form($form);
  }
}

/**
 * Class hostingService_http_rancher
 *
 * Rancher for HTTP Server
 */
class hostingService_db_rancher_database extends hostingService_db {
  public $type = 'rancher';

  function form(&$form) {
    parent::form($form);
  }
}
