<?php
// ErrorHandlerInterface.php
namespace brainwave;

use Throwable;

interface ErrorHandlerInterface {
    public function handleException(Throwable $e): void;
}

