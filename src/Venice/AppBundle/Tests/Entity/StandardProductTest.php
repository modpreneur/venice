<?php

namespace Venice\AppBundle\Tests\Entity;

use Venice\AppBundle\Entity\Product\StandardProduct;
use Venice\AppBundle\Tests\BaseTest;

class StandardProductTest extends BaseTest
{
    /**
     * @dataProvider getDescriptionForCustomerProvider
     *
     * @param $necktieDescription
     * @param $veniceDescription
     * @param $result
     */
    public function testGetDescriptionForCustomer($necktieDescription, $veniceDescription, $result)
    {
        $product = new StandardProduct();
        $product->setNecktieDescription($necktieDescription);
        $product->setDescription($veniceDescription);

        static::assertEquals($result, $product->getDescriptionForCustomer());
    }

    /**
     * @return array
     */
    public function getDescriptionForCustomerProvider()
    {
        $necktieDescription = 'necktieDescription';
        $veniceDescription = 'veniceDescription';

        return [
            [$necktieDescription, $veniceDescription, $veniceDescription],
            ['', $veniceDescription, $veniceDescription],
            [$necktieDescription, '', $necktieDescription],
            ['', '', '']
        ];
    }
}