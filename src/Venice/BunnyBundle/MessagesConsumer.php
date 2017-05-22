<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 05.06.16
 * Time: 15:10.
 */
namespace Venice\BunnyBundle;

use Bunny\Channel;
use Bunny\Message;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Trinity\Bundle\BunnyBundle\Annotation\Consumer;
use Trinity\Bundle\MessagesBundle\Event\UnpackMessageEvent;

/**
 * //todo: see http://ac5.modpreneur.com/projects/6?modal=Task-453-6.
 *
 * //todo: hardcoded for flofit
 * @Consumer(
 *     queue="client_3",
 *     maxMessages=100,
 *     maxSeconds=600.0,
 *     prefetchCount=1,
 *     method = "readMessage"
 * )
 */
class MessagesConsumer
{
    /** @var  EventDispatcherInterface */
    protected $dispatcher;

    /** @var  string */
    protected $clientIdentification;

    /**
     * MessagesConsumer constructor.
     *
     * @param EventDispatcherInterface $dispatcher
     * @param string                   $clientIdentification
     */
    public function __construct(EventDispatcherInterface $dispatcher, string $clientIdentification)
    {
        $this->dispatcher = $dispatcher;
        $this->clientIdentification = $clientIdentification;
    }

    public function readMessage($data, Message $message, Channel $channel)
    {
        if (function_exists('dump')) {
            dump($data, $message);
        }

        try {
            $event = new UnpackMessageEvent($data, $this->clientIdentification);
            $this->dispatcher->dispatch(UnpackMessageEvent::NAME, $event);

            $channel->ack($message);
        } catch (\Throwable $error) {
            if (function_exists('dump')) {
                dump($error->getMessage(), $error->getFile(), $error->getTraceAsString());
            }
            $channel->nack($message, false, false);
        }
    }
}
