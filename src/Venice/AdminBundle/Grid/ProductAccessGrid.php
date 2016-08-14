<?php

namespace Venice\AdminBundle\Grid;

use Trinity\Bundle\GridBundle\Grid\BaseGrid;

/**
 * Class productAccessGrid.
 */
class ProductAccessGrid extends BaseGrid
{
    /**
     * Set up grid (template).
     */
    public function setUp()
    {
        $this->addTemplate('VeniceAdminBundle:ProductAccess:grid.html.twig');
    }
}
