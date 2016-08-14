<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 02.10.15
 * Time: 17:08.
 */
namespace Venice\AdminBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * {@inheritdoc}
 */
class VeniceAdminBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'TrinityAdminBundle';
    }
}
