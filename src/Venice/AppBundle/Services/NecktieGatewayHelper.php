<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 22.10.15
 * Time: 14:31.
 */
namespace Venice\AppBundle\Services;

use Venice\AppBundle\Entity\BillingPlan;
use Venice\AppBundle\Entity\Invoice;
use Venice\AppBundle\Entity\OAuthToken;
use Venice\AppBundle\Interfaces\NecktieGatewayHelperInterface;

class NecktieGatewayHelper implements NecktieGatewayHelperInterface
{
    const NECKTIE_EXPIRED_ACCESS_TOKEN_ERROR = '{"error":"invalid_grant","error_description":"The access token provided has expired."}';
    const NECKTIE_INVALID_ACCESS_TOKEN_ERROR = '{"error":"invalid_grant","error_description":"The access token provided is invalid."}';
    //this message is the same for expired as well as invalid refresh token
    const NECKTIE_EXPIRED_REFRESH_TOKEN_ERROR = '{"error":"invalid_grant","error_description":"Invalid refresh token"}';
    const NECKTIE_INVALID_CLIENT_ERROR = '{"error":"invalid_token","error_description":"The client credentials are invalid"}';

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
            $necktieToken = new OAuthToken();
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
    public function getInvoicesFromNecktieResponse(array $response)
    {
        if (!array_key_exists('invoices', $response) && is_array($response['invoices'])) {
            return [];
        }

        $invoices = [];

        foreach ($response['invoices'] as $invoice) {
            $invoiceObject = new Invoice();

            if (array_key_exists('id', $invoice)) {
                $invoiceObject->setId($invoice['id']);
            } else {
                continue;
            }

            if (array_key_exists('status', $invoice)) {
                $invoiceObject->setStatus($invoice['status']);
            } else {
                continue;
            }

            if (array_key_exists('first_payment_date', $invoice)) {
                $date = \DateTime::createFromFormat(\DateTime::W3C, $invoice['first_payment_date']);
                $invoiceObject->setFirstPaymentDate($date);
            } else {
                continue;
            }

            if (array_key_exists('items', $invoice)) {
                foreach ($invoice['items'] as $invoiceItem) {
                    if (array_key_exists('billing_plan', $invoiceItem)
                        && array_key_exists('product', $invoiceItem['billing_plan'])
                        && array_key_exists('name', $invoiceItem['billing_plan']['product'])
                    ) {
                        $invoiceObject->addItem($invoiceItem['billing_plan']['product']['name']);
                    }
                }
            } else {
                continue;
            }

            if (array_key_exists('full_prices', $response)) {
                if (array_key_exists($invoiceObject->getReceipt(), $response['full_prices'])) {
                    $invoiceObject->setStringPrice($response['full_prices'][$invoiceObject->getReceipt()]);
                }
            }

            $invoices[] = $invoiceObject;
        }

        return $invoices;
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
