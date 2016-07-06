<?php

namespace Venice\AdminBundle\Grid;

use Trinity\Bundle\GridBundle\Grid\BaseGrid;

/**
 * Class billingPlanGrid
 * @package Venice\AdminBundle\Grid
 */
class BillingPlanGrid extends BaseGrid
{
    /**
     * Set up grid (template)
     *
     * @return void
     */
    public function setUp()
    {
        $this->addTemplate('VeniceAdminBundle:BillingPlan:grid.html.twig');
    }
}