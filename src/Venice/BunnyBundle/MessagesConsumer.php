<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 05.06.16
 * Time: 15:10
 */

namespace Venice\BunnyBundle;

use Bunny\Channel;
use Bunny\Client;
use Bunny\Message;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Trinity\Bundle\BunnyBundle\Annotation\Consumer;
use Trinity\Bundle\MessagesBundle\Event\Events;
use Trinity\Bundle\MessagesBundle\Event\UnpackMessageEvent;

/**
 * //todo: see http://ac5.modpreneur.com/projects/6?modal=Task-453-6
 * @Consumer(
 *     queue="client_3",
 *     maxMessages=1000,
 *     maxSeconds=3600.0,
 *     prefetchCount=1,
 *     method = "readMessage"
 * )
 */
class MessagesConsumer
{
    /** @var  EventDispatcherInterface */
    protected $dispatcher;

    /**
     * MessagesConsumer constructor.
     *
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function readMessage($data, Message $message, Channel $channel, Client $client)
    {
        dump($data, $message);

        //todo: get "client_3" from parameters
        $event = new UnpackMessageEvent($data, 'client_3');
        $this->dispatcher->dispatch(Events::UNPACK_MESSAGE, $event);

        $channel->ack($message);
    }
}