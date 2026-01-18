<?php

namespace Adirectory\Framework\Container\Contracts;

use Exception;
use Adirectory\Framework\Container\Contracts\Psr\ContainerExceptionInterface;

class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
