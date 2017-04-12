<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 22.10.15
 * Time: 14:31.
 */
namespace Venice\AppBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use Venice\AppBundle\Entity\BillingPlan;
use Venice\AppBundle\Entity\Order;
use Venice\AppBundle\Entity\OAuthToken;
use Venice\AppBundle\Entity\OrderItem;
use Venice\AppBundle\Interfaces\NecktieGatewayHelperInterface;

class NecktieGatewayHelper implements NecktieGatewayHelperInterface
{
    const NECKTIE_EXPIRED_ACCESS_TOKEN_ERROR = '{"error":"invalid_grant","error_description":"The access token provided has expired."}';
    const NECKTIE_INVALID_ACCESS_TOKEN_ERROR = '{"error":"invalid_grant","error_description":"The access token provided is invalid."}';
    //this message is the same for expired as well as invalid refresh token
    const NECKTIE_EXPIRED_REFRESH_TOKEN_ERROR = '{"error":"invalid_grant","error_description":"Invalid refresh token"}';
    const NECKTIE_INVALID_CLIENT_ERROR = '{"error":"invalid_token","error_description":"The client credentials are invalid"}';

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var EntityOverrideHandler
     */
    protected $entityOverrideHandler;

    /**
     * NecktieGatewayHelper constructor.
     * @param EntityManagerInterface $entityManager
     * @param EntityOverrideHandler $entityOverrideHandler
     */
    public function __construct(EntityManagerInterface $entityManager, EntityOverrideHandler $entityOverrideHandler)
    {
        $this->entityManager = $entityManager;
        $this->entityOverrideHandler = $entityOverrideHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserInfoFromNecktieProfileResponse(array $response)
    {
        $requiredFields = ['username', 'email', 'id'];
        $userInfo = [];

        if (is_array($response) && array_key_exists('user', $response)) {
            $response = $response['user'];
        } else {
            return;
        }

        foreach ($requiredFields as $requiredFiled) {
            if (is_array($response) && array_key_exists($requiredFiled, $response)) {
                $userInfo[$requiredFiled] = $response[$requiredFiled];
            } else {
                return;
            }
        }

        return $userInfo;
    }

    /**
     * {@inheritdoc}
     */
    public function createOAuthTokenFromArray(array $array)
    {
        if (array_key_exists('access_token', $array)
            && array_key_exists('refresh_token', $array)
            && array_key_exists('scope', $array)
            && array_key_exists('expires_in', $array)
        ) {
            $necktieToken = $this->entityOverrideHandler->getEntityInstance(OAuthToken::class);
            $necktieToken->setAccessToken($array['access_token']);
            $necktieToken->setRefreshToken($array['refresh_token']);
            $necktieToken->setScope($array['scope']);
            $necktieToken->setValidToByLifetime($array['expires_in']);

            return $necktieToken;
        } else {
            return;
        }
    }

    /**
     * {@inheritdoc}
     */
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

            if (array_key_exists('full_prices', $response)) {
                if (array_key_exists($orderObject->getReceipt(), $response['full_prices'])) {
                    $orderObject->setStringPrice($response['full_prices'][$orderObject->getReceipt()]);
                }
            }

            $orders[] = $orderObject;
        }

        return $orders;
    }

    /**
     * todo: test or remove.
     *
     * @param $response
     *
     * @return BillingPlan|null
     */
    public function getBillingPlanFromResponse($response)
    {
        $billingPlan = new BillingPlan();

        if (array_key_exists('initial_price', $response)) {
            $billingPlan->setInitialPrice($response['initial_price']);
        }

        if (array_key_exists('id', $response)) {
            $billingPlan->setNecktieId($response['id']);
        }

        if (array_key_exists('id', $response)) {
            $billingPlan->setId($response['id']);
        }

        if (array_key_exists('type', $response) && $response['type'] == 'recurring') {
            if (array_key_exists('rebill_price', $response)) {
                $billingPlan->setRebillPrice($response['rebill_price']);
            }

            if (array_key_exists('frequency', $response)) {
                $billingPlan->setFrequency($response['frequency']);
            }

            if (array_key_exists('rebill_times', $response)) {
                $billingPlan->setRebillTimes($response['rebill_times']);
            }
        }

        return $billingPlan;
    }

    /**
     * {@inheritdoc}
     */
    public function isResponseOk($response)
    {
        return !($this->isAccessTokenExpiredResponse($response)
            || $this->isAccessTokenInvalidResponse($response)
            || $this->isRefreshTokenExpiredResponse($response)
            || $this->isInvalidClientResponse($response)
            || $this->hasError($response)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isAccessTokenInvalidResponse($response)
    {
        if ((is_string($response) && false !== strpos($response, self::NECKTIE_INVALID_ACCESS_TOKEN_ERROR))
            || (is_array($response) && ($response == json_decode(self::NECKTIE_INVALID_ACCESS_TOKEN_ERROR, true))
            )
        ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isAccessTokenExpiredResponse($response)
    {
        if ((is_string($response) && false !== strpos($response, self::NECKTIE_EXPIRED_ACCESS_TOKEN_ERROR))
            || (is_array($response) && $response === json_decode(self::NECKTIE_EXPIRED_ACCESS_TOKEN_ERROR, true))
        ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isRefreshTokenExpiredResponse($response)
    {
        if ((is_string($response) && false !== strpos($response, self::NECKTIE_EXPIRED_REFRESH_TOKEN_ERROR))
            || (is_array($response) && $response == json_decode(self::NECKTIE_EXPIRED_REFRESH_TOKEN_ERROR, true))
        ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isInvalidClientResponse($response)
    {
        if ((is_string($response) && false !== strpos($response, self::NECKTIE_INVALID_CLIENT_ERROR))
            || (is_array($response) && $response == json_decode(self::NECKTIE_INVALID_CLIENT_ERROR, true))
        ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasError($response)
    {
        if ((is_string($response) && false !== strpos($response, 'error'))
            || (is_array($response) && array_key_exists('error', $response))
        ) {
            return true;
        } else {
            return false;
        }
    }
}
