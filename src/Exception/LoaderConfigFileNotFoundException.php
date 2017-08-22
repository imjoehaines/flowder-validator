<?php

namespace Imjoehaines\Flowder\Validator\Exception;

use Exception;

class LoaderConfigFileNotFoundException extends Exception
{
    public function __construct($configPath, ...$args)
    {
        parent::__construct(
            sprintf('Unable to find a loader config file at "%s"', $configPath),
            ...$args
        );
    }
}
