<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 31.01.16
 * Time: 10:50.
 */
namespace Venice\AdminBundle\Grid;

use Trinity\Bundle\GridBundle\Grid\BaseGrid;

/**
 * Class ContentGrid.
 */
class ContentGrid extends BaseVeniceGrid
{
    /**
     * Set up grid (template).
     */
    public function setUp()
    {
        $this->addTemplate('VeniceAdminBundle:Content:grid.html.twig');
    }
}
