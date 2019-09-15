<?php

declare(strict_types=1);

namespace App;

use Nette\Configurator;
use Nette\DI\Container;

require __DIR__ . '/../vendor/autoload.php';

final class Bootstrap
{

	/**
	 * @var Configurator
	 */
	private $configurator;


	public function __construct()
	{
		$this->configurator = new Configurator;

		$this->configurator->setTimeZone('Europe/Prague');
		$this->configurator->setTempDirectory(__DIR__ . '/../temp');
		$this->configurator->addConfig(__DIR__ . '/../config/config.neon');

		if (file_exists(__DIR__ . '/../config/config.local.neon')) {
			$this->configurator->addConfig(__DIR__ . '/../config/config.local.neon');
		}
	}


	public function createContainer(): Container
	{
		return $this->configurator->createContainer();
	}
}
