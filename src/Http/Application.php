<?php

declare(strict_types=1);

namespace App\Http;

use Nette\Application\ApplicationException;
use Nette\Application\BadRequestException;
use Nette\Application\InvalidPresenterException;
use Nette\Application\IPresenterFactory;
use Nette\Application\IResponse;
use Nette\Application\Request;
use Nette\Application\Responses\ForwardResponse;

final class Application
{

	/**
	 * @var int
	 */
	public $maxLoop = 20;

	/**
	 * @var string|null
	 */
	public $errorPresenter;

	/**
	 * @var Request[]
	 */
	private $requests = [];

	/**
	 * @var IPresenterFactory
	 */
	private $presenterFactory;


	public function __construct(
		IPresenterFactory $presenterFactory,
	) {
		$this->presenterFactory = $presenterFactory;
	}


	public function processRequest(Request $request): IResponse
	{
		process:
		if (count($this->requests) > $this->maxLoop) {
			throw new ApplicationException('Too many loops detected in application life cycle.');
		}

		$this->requests[] = $request;
		//$this->onRequest($this, $request);

		if (!$request->isMethod($request::FORWARD) && !(bool) strcasecmp($request->getPresenterName(), (string) $this->errorPresenter)) {
			throw new BadRequestException('Invalid request. Presenter is not achievable.');
		}

		try {
			$presenter = $this->presenterFactory->createPresenter($request->getPresenterName());
		} catch (InvalidPresenterException $e) {
			throw count($this->requests) > 1 ? $e : new BadRequestException($e->getMessage(), 0, $e);
		}

		//$this->onPresenter($this, $presenter);
		$response = $presenter->run(clone $request);

		if ($response instanceof ForwardResponse) {
			$request = $response->getRequest();
			goto process;
		}

		// $this->onResponse($this, $response);
		//$response->send($this->httpRequest, $this->httpResponse);

		$response;
	}
}
