<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 09.06.16
 * Time: 19:06
 */

namespace Venice\BunnyBundle;


use Trinity\Bundle\BunnyBundle\AbstractProducer;
use Trinity\Bundle\BunnyBundle\Annotation\Producer;

/**
 * @Producer(
 *     exchange="clients_to_necktie_notifications",
 *     beforeMethod="preProcessMessage"
 * )
 */
class NotificationsProducer extends AbstractProducer
{
    /**
     * @param $message
     *
     * @return string
     */
    public function preProcessMessage($message)
    {
        dump("preprocess:", $message);
    }
}