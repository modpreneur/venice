<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 02.10.15
 * Time: 17:08
 */

namespace Venice\AdminBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class VeniceAdminBundle extends Bundle
{
    public function getParent()
    {
        return 'TrinityAdminBundle';
    }
}
