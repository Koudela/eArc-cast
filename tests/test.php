<?php declare(strict_types=1);

include __DIR__.'/../vendor/autoload.php';

\eArc\Cast\Initializer::init();

dump(\eArc\Cast\cast_simple(new \eArc\CastTests\Origin(),[]));
