<?php
namespace Venice\AppBundle\Entity\Interfaces;


/**
 * Class Mp3Content.
 */
interface Mp3ContentInterface extends AbstractPlayableContentInterface, ContentInterface
{
    /**
     * @return string
     */
    public function getLink();

    /**
     * @param string $link
     *
     * @return void
     */
    public function setLink(string $link);
}
