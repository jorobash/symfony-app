<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class Greeting {

    /*
     * @var $logger
     */

    private $logger;
    private $message;

    public  function __construct(LoggerInterface $logger, string $message){
        $this->logger = $logger;
        $this->message = $message;
    }

    public function greet($name): string
    {
        $this->logger->info( "Greeted $name");
        return "{$this->message} $name";
    }
}