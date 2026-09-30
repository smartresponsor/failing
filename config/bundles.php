<?php

declare(strict_types=1);

use App\Failing\FailingBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;

return [
    FrameworkBundle::class => ['all' => true],
    FailingBundle::class => ['all' => true],
];
