<?php
# Copyright (c) 2017, Infomaniak Network SA.
# All rights reserved.

namespace Infomaniak;

use GuzzleHttp\Client;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use GuzzleHttp\Command\Guzzle\Description;
use Infomaniak\Exceptions\ApiException;
use Infomaniak\Deserializers\ApiDeserializer;

/**
 * Class Api
 *
 * @package Infomaniak
 * @method Api ping(array $params)
 * @method Api listMailbox(array $params) Get list Mailboxes from ServiceMail
 * @method Api getMailbox(array $params) Get informations of a given Mailbox
 * @method Api updateMailbox(array $params) Update a given Mailbox
 * @method Api deleteMailbox(array $params) Delete a given Mailbox
 * @method Api addMailbox(array $params) Create a new Mailbox
 */
final class Api extends GuzzleClient {

	/** @var string */
	private $endpoint = '';

	/** @var array */
	private $config = [];

	/** @var Client */
	private $client;

	/** @var Description */
	private static $description;

	const ENDPOINTS        = ['default' => 'https://api.infomaniak.com'];
	const DESCRIPTION_JSON = '/doc/methods_description.json';
	const CONNECT_TIMEOUT  = 5;
	const TIMEOUT          = 30;


	/******************************************************************************************************
	 * PUBLIC METHODS
	 ******************************************************************************************************/

	/**
	 * Api constructor.
	 *
	 * @param array  $params
	 * @param string $endpoint
	 *
	 * @throws ApiException if token is not provided
	 */
	public function __construct($params, $endpoint = 'default') {
		if (isset($params['token'])) {
			$token = $params['token'];
		} elseif (isset($params['client_api']) && $params['client_secret']) {
			throw new ApiException("Unsupported OAuth provider");
		} else {
			throw new ApiException("Unknown provided endpoint");
		}
		$this->initEndpoint($endpoint);
		$this->initClientApi($token);
		$this->initDescription();

		parent::__construct($this->client, self::getApiDescription(), null, new ApiDeserializer(self::$description, true));
	}


	/******************************************************************************************************
	 * PRIVATE METHODS
	 ******************************************************************************************************/

	/**
	 * Configure and init Api endpoint
	 *
	 * @param string $endpoint
	 *
	 * @throws ApiException if endpoint provided is not define
	 */
	private function initEndpoint($endpoint) {
		if (!array_key_exists($endpoint, self::ENDPOINTS)) {
			throw new ApiException("Unknown provided endpoint");
		}
		$this->endpoint = self::ENDPOINTS[ $endpoint ];
	}

	/**
	 * Configure and init Guzzle Client
	 *
	 * @param string $token
	 */
	private function initClientApi($token) {
		$this->config['connect_timeout'] = self::CONNECT_TIMEOUT;
		$this->config['timeout'] = self::TIMEOUT;
		$this->config['protocols'] = ['https'];
		$this->config['headers'] = ['authorization' => 'Bearer ' . $token];
		$this->client = new Client($this->config);
	}

	/**
	 * Configure and init Api description
	 */
	private function initDescription() {
		if (is_null(self::$description)) {
			$json_location = file_get_contents($this->endpoint . self::DESCRIPTION_JSON);
			$description_config = json_decode($json_location, true);
			$description_config['baseUri'] = $this->endpoint;
			self::$description = new Description($description_config);
		}
	}


	/******************************************************************************************************
	 * STATIC PUBLIC METHODS
	 ******************************************************************************************************/

	/**
	 * Return Api description services from a static context
	 *
	 * @return Description
	 */
	public static function getApiDescription() {
		return self::$description;
	}

	/**
	 * Return Api operation description from a static context
	 *
	 * @param string $name
	 *
	 * @return \GuzzleHttp\Command\Guzzle\Operation
	 */
	public static function getApiOperationDescription($name) {
		return self::$description->getOperation($name);
	}

}


