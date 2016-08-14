<?php

namespace Venice\AdminBundle\Grid;

use Trinity\Bundle\GridBundle\Grid\BaseGrid;

/**
 * Class billingPlanGrid.
 */
class BillingPlanGrid extends BaseGrid
{
    /**
     * Set up grid (template).
     */
    public function setUp()
    {
        $this->addTemplate('VeniceAdminBundle:BillingPlan:grid.html.twig');
    }
}
