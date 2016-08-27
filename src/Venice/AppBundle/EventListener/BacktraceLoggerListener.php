<?php

namespace Venice\AppBundle\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

/**
 * Class BacktraceLoggerListener.
 */
class BacktraceLoggerListener
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $this->logger->error($event->getException());
    }
}
