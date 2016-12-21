<?php

namespace Venice\AdminBundle\Grid;

use Trinity\Bundle\GridBundle\Grid\BaseGrid;
use Trinity\Bundle\GridBundle\Grid\GridConfigurationBuilder;

/**
 * Class BaseVeniceGrid
 */
abstract class BaseVeniceGrid extends BaseGrid
{

    /**
     * Build grid
     *
     * @param GridConfigurationBuilder $builder
     *
     * @return void
     */
    protected function build(GridConfigurationBuilder $builder)
    {
        // TODO: Implement build() method.
    }
}