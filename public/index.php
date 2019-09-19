<?php

declare(strict_types=1);

use App\Bootstrap;
use Nette\Application\Application;

require __DIR__ . '/../vendor/autoload.php';

(new Bootstrap)->createContainer()->getByType(Application::class)->run();
