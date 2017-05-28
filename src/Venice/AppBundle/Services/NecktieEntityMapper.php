<?php

namespace Venice\AppBundle\Services;

use Venice\AppBundle\Entity\BillingPlan;
use Venice\AppBundle\Entity\OAuthToken;
use Venice\AppBundle\Entity\Order;
use Venice\AppBundle\Entity\OrderItem;

class NecktieEntityMapper
{
    /**
     * @var EntityOverrideHandler
     */
    protected $entityOverrideHandler;

    /**
     * Create a OAuthToken object from oauth server response converted to array.
     *
     * @param $array array
     *
     * @return OAuthToken|null
     */
    public function createOAuthTokenFromArray(array $array): ?OAuthToken
    {
        if (array_key_exists('access_token', $array)
            && array_key_exists('refresh_token', $array)
            && array_key_exists('scope', $array)
            && array_key_exists('expires_in', $array)
        ) {
            /** @var OAuthToken $necktieToken */
            $necktieToken = $this->entityOverrideHandler->getEntityInstance(OAuthToken::class);
            $necktieToken->setAccessToken($array['access_token']);
            $necktieToken->setRefreshToken($array['refresh_token']);
            $necktieToken->setScope($array['scope']);
            $necktieToken->setValidToByLifetime($array['expires_in']);

            return $necktieToken;
        }
    }

//    /**
//     * todo: test or remove.
//     *
//     * @param $response
//     *
//     * @return BillingPlan|null
//     */
//    public function getBillingPlanFromResponse($response): ?BillingPlan
//    {
//        /** @var BillingPlan $billingPlan */
//        $billingPlan = $this->entityOverrideHandler->getEntityInstance(BillingPlan::class);
//
//        if (array_key_exists('initial_price', $response)) {
//            $billingPlan->setInitialPrice($response['initial_price']);
//        }
//
//        if (array_key_exists('id', $response)) {
//            $billingPlan->setNecktieId($response['id']);
//        }
//
//        if (array_key_exists('id', $response)) {
//            $billingPlan->setId($response['id']);
//        }
//
//        if (array_key_exists('type', $response) && $response['type'] === 'recurring') {
//            if (array_key_exists('rebill_price', $response)) {
//                $billingPlan->setRebillPrice($response['rebill_price']);
//            }
//
//            if (array_key_exists('frequency', $response)) {
//                $billingPlan->setFrequency($response['frequency']);
//            }
//
//            if (array_key_exists('rebill_times', $response)) {
//                $billingPlan->setRebillTimes($response['rebill_times']);
//            }
//        }
//
//        return $billingPlan;
//    }

    public function getOrdersFromNecktieResponse(array $response)
    {
        if (!array_key_exists('orders', $response) && is_array($response['orders'])) {
            return [];
        }

        $orders = [];

        foreach ($response['orders'] as $order) {
            $orderObject = new Order();

            if (array_key_exists('id', $order)) {
                $orderObject->setNecktieId($order['id']);
            } else {
                continue;
            }

            if (array_key_exists('status', $order)) {
                $orderObject->setStatus($order['status']);
            } else {
                continue;
            }

            if (array_key_exists('receipt', $order)) {
                $orderObject->setReceipt($order['receipt']);
            } else {
                continue;
            }

            if (array_key_exists('first_payment_date', $order)) {
                $date = \DateTime::createFromFormat(\DateTime::W3C, $order['first_payment_date']);
                $orderObject->setFirstPaymentDate($date);
            } else {
                continue;
            }

            if (array_key_exists('items', $order)) {
                foreach ($order['items'] as $orderItem) {
                    $orderItemObject = new OrderItem();
                    $orderObject->addItem($orderItemObject);
                    $orderItemObject->setOrder($orderObject);

                    if (array_key_exists('billing_plan', $orderItem)
                        && array_key_exists('product', $orderItem['billing_plan'])
                        && array_key_exists('name', $orderItem['billing_plan']['product'])
                    ) {
                        $orderItemObject->setProductName($orderItem['billing_plan']['product']['name']);
                    } else {
                        $orderItemObject->setProductName('');
                    }

                    if (array_key_exists('initial_price', $orderItem)) {
                        $orderItemObject->setInitialPrice($orderItem['initial_price']);
                    } else {
                        continue;
                    }

                    if (array_key_exists('rebill_price', $orderItem)) {
                        $orderItemObject->setRebillPrice($orderItem['rebill_price']);
                    } else {
                        continue;
                    }

                    if (array_key_exists('rebill_times', $orderItem)) {
                        $orderItemObject->setRebillTimes($orderItem['rebill_times']);
                    } else {
                        continue;
                    }

                    if (array_key_exists('id', $orderItem)) {
                        $orderItemObject->setNecktieId($orderItem['id']);
                    } else {
                        continue;
                    }

                    if (array_key_exists('type', $orderItem)) {
                        $orderItemObject->setType($orderItem['type']);
                    } else {
                        continue;
                    }
                }
            } else {
                continue;
            }

            if (array_key_exists('full_prices', $response) &&
                array_key_exists($orderObject->getReceipt(), $response['full_prices'])
            ) {
                $orderObject->setStringPrice($response['full_prices'][$orderObject->getReceipt()]);
            }

            $orders[] = $orderObject;
        }

        return $orders;
    }
}