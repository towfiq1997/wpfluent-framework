<?php

namespace Adirectory\Framework\Container;

use Exception;
use Adirectory\Framework\Container\Contracts\Psr\NotFoundExceptionInterface;

class EntryNotFoundException extends Exception implements NotFoundExceptionInterface
{
    //
}
