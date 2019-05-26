<?php

namespace DomParserWrapper\Exception;

use \Exception;

class DomNotFoundException extends Exception
{
    public function __construct($message = "")
    {
        parent::__construct("Specified elements do not found in parent dom");
    }
}
