<?php

namespace Cool\Foundation\Exceptions;

/**
 * Class ErrorException
 * @package Cool\Foundation\Exceptions
 */
class ErrorException extends \RuntimeException
{

    // 构造
    public function __construct($type, $message, $file, $line)
    {
        $this->code = $type;
        $this->message = $message;
        $this->file = $file;
        $this->line = $line;
        // 父类构造
        parent::__construct();
    }

}
