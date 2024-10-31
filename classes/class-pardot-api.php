<?php
/**
 * Pardot API class
 *
 * @package PardotMarketing
 * @since 1.0.0
 */

/**
 * Pardot API Class
 *
 * @since 1.0.0
 */

class PardotAPI {

  /**
   * @var string
   */
  private $APIBaseURL;

  /**
   * @var string
   */
  private $APIEmail;

  /**
   * @var string
   */
  private $APIPassword;

  /**
   * @var string
   */
  private $APIUserKey;

  /**
   * @var array
   */
  private $APICredentials;

  /**
   * @var string
   */
  private $APIKey;

  /**
   * @var string
   */
  private $APIVersion;

  /**
   * @var array
   */
  private $DefaultParams;

  /**
   * PardotAPI constructor.
   */
  function __construct( $args ) {
    $this->APIVersion = 'version/4';

    $this->APIBaseURL  = ! empty( $args['api_url'] ) ? $args['api_url'] : 'https://pi.pardot.com/api';
    $this->APIEmail    = ! empty( $args['api_email'] ) ? $args['api_email'] : false;
    $this->APIPassword = ! empty( $args['api_password'] ) ? $args['api_password'] : false;
    $this->APIUserKey  = ! empty( $args['api_user_key'] ) ? $args['api_user_key'] : false;

    $this->DefaultParams  = $this->getDefaultParams();
    $this->APICredentials = $this->getAPICredentials();
  }

  /**
   * @return array
   */
  function getAPICredentials() {
    return [
      'email'    => $this->APIEmail,
      'password' => $this->APIPassword,
      'user_key' => $this->APIUserKey,
    ];
  }

  function getDefaultParams() {
    return [
      'timeout'   => 30,
      'method'    => 'POST',
      'sslverify' => true,
      'output'    => 'mobile',
    ];
  }

  /**
   * Authenticates with the Pardot API & returns an API key,
   *
   * @return string|boolean API key, false if failed.
   */
  function authenticate() {
    $this->DefaultParams['body'] = $this->APICredentials;
    $response = wp_remote_post(
      "{$this->APIBaseURL}/login/{$this->APIVersion}",
      array_merge($this->DefaultParams, $this->APICredentials)
    );

    if ( ! empty( $response ) ) {
      return ( string ) simplexml_load_string( $response['body'] )->api_key;
    }

    return false;
  }

  function getProspects( $args = [] ) {
    $this->APIKey = $this->authenticate();

    $auth_params['headers']['authorization'] = "Pardot api_key={$this->APIKey}, user_key={$this->APICredentials['user_key']}";

    $endpoint = "{$this->APIBaseURL}/prospect/{$this->APIVersion}/do/query?" . http_build_query( $args );

    $response = wp_remote_post( $endpoint, array_merge( $this->getDefaultParams(), $auth_params ) );

    $body = ( array ) simplexml_load_string( wp_remote_retrieve_body( $response ) );

    $status = $body['@attributes']['stat'];
    if ( $status == 'ok' ) {
      return json_decode( json_encode( $body['result'] ), TRUE );
    }

    return false;
  }

  function getForms( $args = [] ) {
    $this->APIKey = $this->authenticate();

    $auth_params['headers']['authorization'] = "Pardot api_key={$this->APIKey}, user_key={$this->APICredentials['user_key']}";

    $endpoint = "{$this->APIBaseURL}/form/{$this->APIVersion}/do/query?" . http_build_query( $args );

    $response = wp_remote_post( $endpoint, array_merge( $this->getDefaultParams(), $auth_params ) );

    $body = ( array ) simplexml_load_string( wp_remote_retrieve_body( $response ) );

    $status = $body['@attributes']['stat'];
    if ( $status == 'ok' ) {
      return json_decode( json_encode( $body['result'] ), TRUE );
    }

    return false;
  }

  /*function getProspect(string $email) {
    $this->APIKey = $this->authenticate();

    $auth_params['headers']['authorization'] = "Pardot api_key={$this->APIKey}, user_key={$this->APICredentials['user_key']}";

    $endpoint = "{$this->APIBaseURL}/prospect/{$this->APIVersion}/do/read/email/{$email}?";

    $response = wp_remote_post( $endpoint, array_merge( $this->getDefaultParams(), $auth_params ));

    $body = ( array ) simplexml_load_string( wp_remote_retrieve_body( $response ) );

    $status = $body['@attributes']['stat'];
    if ( $status == 'ok' ) {
      return ( array ) $body['prospect'];
    }

    return false;
  }*/

  /**
   * Add/update prospect
   *
   * @param array $prospects
   * @return boolean
   */
  function batchUpsertProspect(array $prospects = []) {
    $auth_params['headers']['authorization'] = "Pardot api_key={$this->APIKey}, user_key={$this->APICredentials['user_key']}";

    $prospects = [
      'prospects' => $prospects
    ];

    $response = wp_remote_post(
      "{$this->APIBaseURL}/prospect/{$this->APIVersion}/do/batchUpsert?prospects=" . json_encode( $prospects ),
      array_merge($this->DefaultParams, $auth_params)
    );

    $body = ( array ) simplexml_load_string( wp_remote_retrieve_body( $response ) );

    $status = $body['@attributes']['stat'];
    if ( $status == 'ok' ) {
      return true;
    }

    return false;
  }

  function readEmailTemplate(int $template_id) {
    $this->APIKey = $this->authenticate();

    $auth_params['headers']['authorization'] = "Pardot api_key={$this->APIKey}, user_key={$this->APICredentials['user_key']}";

    $endpoint = "{$this->APIBaseURL}/emailTemplate/{$this->APIVersion}/do/read/id/{$template_id}";

    $response = wp_remote_post( $endpoint, array_merge( $this->getDefaultParams(), $auth_params ));

    $body = ( array ) simplexml_load_string( wp_remote_retrieve_body( $response ) );

    $status = $body['@attributes']['stat'];
    if ( $status == 'ok' ) {
      return ( array ) $body['emailTemplate'];
    }

    return false;
  }

  function sendEmail(string $prospect_email, array $params = []) {
    $this->APIKey = $this->authenticate();

    $auth_params['headers']['authorization'] = "Pardot api_key={$this->APIKey}, user_key={$this->APICredentials['user_key']}";


    $query_params = http_build_query([
      'from_name'     => ! empty( $params['from_name'] ) ? $params['from_name'] : false,
      'from_email'    => ! empty( $params['from_email'] ) ? $params['from_email'] : false,
      'replyto_email' => ! empty( $params['replyto_email'] ) ? $params['replyto_email'] : false,
      'name'          => ! empty( $params['name'] ) ? $params['name'] : false,
      'subject'       => ! empty( $params['subject'] ) ? $params['subject'] : false,
      'campaign_id'    => ! empty( $params['campaign_id'] ) ? $params['campaign_id'] : false,
    ]);

    $body = [
      'html_content' => ! empty( $params['html_content'] ) ? $params['html_content'] : false,
      'text_content' => ! empty( $params['text_content'] ) ? $params['text_content'] : false,
    ];

    $endpoint = "{$this->APIBaseURL}/email/{$this->APIVersion}/do/send/prospect_email/{$prospect_email}?" . $query_params;

    $remote_params = array_merge( $this->getDefaultParams(), $auth_params );
    $remote_params['body'] = $body;

    $response = wp_remote_post( $endpoint, $remote_params );

    $body = ( array ) simplexml_load_string( wp_remote_retrieve_body( $response ) );


    $status = $body['@attributes']['stat'];
    if ( $status == 'ok' ) {
      return ( array ) $body['email'];
    }

    return false;
  }
}
