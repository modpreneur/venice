<?php

namespace Venice\BunnyBundle\EventListener;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpKernel\KernelInterface;
use Trinity\Bundle\MessagesBundle\Event\SendMessageEvent;

/**
 * Class MessagesEventListener
 * @package Venice\BunnyBundle\EventListener
 */
class MessagesEventListener
{
    /** @var  KernelInterface */
    protected $kernel;

    /**
     * ProducerProxy constructor.
     *
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function onSendMessage(SendMessageEvent $event)
    {
        $kernel = $this->kernel;
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $message = $event->getMessage();
        $routingKey = $message->getDestination();

        if ($message->getType() === 'notification') {
            $producerName = 'Notifications';
        } else {
            $producerName = 'Messages';
        }

        $input = new ArrayInput([
            'command' => 'bunny:producer',
            'producer-name' => $producerName,
            'message' => $message->pack(),
            'routing-key' => ($routingKey === '')? null : $routingKey
        ]);

        $output = new BufferedOutput();

        $application->run($input, $output);

        $response = $output->fetch();

        return new Response($response);
    }
}