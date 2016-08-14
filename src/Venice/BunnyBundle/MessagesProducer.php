<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 05.06.16
 * Time: 15:10.
 */
namespace Venice\BunnyBundle;

use Trinity\Bundle\BunnyBundle\AbstractProducer;
use Trinity\Bundle\BunnyBundle\Annotation\Producer;

/**
 * @Producer(
 *     exchange="clients_to_necktie_messages",
 *     beforeMethod="preProcessMessage"
 * )
 */
class MessagesProducer extends AbstractProducer
{
    /**
     * @param $message
     *
     * @return string
     */
    public function preProcessMessage($message)
    {
    }
}
