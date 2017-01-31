<?php

namespace Venice\AdminBundle\Grid;

/**
 * Class ExceptionGrid.
 */
class ExceptionGrid extends BaseVeniceGrid
{
    /**
     * Set up grid (template).
     */
    public function setUp()
    {
        $this->addTemplate('VeniceAdminBundle:Logger/Exception:grid.html.twig');
        $this->setEntityName('ExceptionLog');
    }
}
