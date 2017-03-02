<?php
# Copyright (c) 2017, Infomaniak Network SA.
# All rights reserved.

namespace Infomaniak\Deserializers;

use GuzzleHttp\Command\Guzzle\Deserializer;
use GuzzleHttp\Command\CommandInterface;
use GuzzleHttp\Command\Result;
use GuzzleHttp\Command\ResultInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Infomaniak\Api;
use Infomaniak\Results\ApiResult;

/**
 * Api deserializer support specific returned result class
 *
 * @package  Infomaniak
 * @category Deserializers
 */
final class ApiDeserializer extends Deserializer {

	/**
	 * @param ResponseInterface $response
	 * @param RequestInterface  $request
	 * @param CommandInterface  $command
	 *
	 * @return Result|ResultInterface|ResponseInterface|void
	 */
	public function __invoke(ResponseInterface $response, RequestInterface $request, CommandInterface $command) {
		$result = parent::__invoke($response, $request, $command);
		$operation = Api::getApiOperationDescription($command->getName());
		$model = $operation->getServiceDescription()->getModel($operation->getResponseModel());
		$wrapper = $model->getData('wrapper');

		if (!is_null($wrapper) && class_exists($wrapper)) {
			return new $wrapper($result->toArray());
		}

		return new ApiResult($result->toArray());

	}


}

