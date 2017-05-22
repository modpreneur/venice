<?php

namespace Venice\AppBundle\Services;

use Venice\AppBundle\Entity\Order;

/**
 * Class InvoiceOrderService
 *
 * This service should answer the question: if the user has bought the product, which order it was in it?
 * If the product has not been bought, the answer is 0
 *
 * Example:
 * If the user has bought product A and then B, the answer for product A should be 1 and 2 for product B.
 */
class InvoiceOrderService
{
    /**
     * @param array $orders
     * @param string $productName
     * @return int
     */
    public function getInvoiceOrderForProductName(array $orders, string $productName): int
    {
        if (\count($orders) === 0) {
            return 0;
        }

        if (\count($orders) > 1) {
            \usort($orders, function ($a, $b) {
                /** @var $a Order */
                /** @var $b Order */

                return $a->getFirstPaymentDate() > $b->getFirstPaymentDate();
            });
        }

        $orderNumber = 0;
        /** @var Order $order */
        foreach ($orders as $order) {
            foreach ($order->getItems() as $item) {
                $orderNumber++;
                if ($item->getProductName() === $productName) {
                    return $orderNumber;
                }
            }
        }

        return 0;
    }
}