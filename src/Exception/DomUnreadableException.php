<?php

namespace Rocketsoba\DomParserWrapper\Exception;

use Exception;

class DomUnreadableException extends Exception
{
    public function __construct($message = "")
    {
        parent::__construct("Failed to construct dom object");
    }
}
