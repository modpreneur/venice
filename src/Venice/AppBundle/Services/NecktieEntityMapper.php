<?php
declare(strict_types=1);

namespace Venice\AppBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use Venice\AppBundle\Entity\OAuthToken;
use Venice\AppBundle\Entity\Order;
use Venice\AppBundle\Entity\OrderItem;
use Venice\AppBundle\Entity\Product\StandardProduct;
use Venice\AppBundle\Entity\ProductAccess;

/**
 * Class NecktieEntityMapper
 *
 * Todo: replace all "continue"s with propper logging!
 *
 * Takes necktie response as associative array and constructs the appropriate object.
 */
class NecktieEntityMapper
{
    /**
     * @var EntityOverrideHandler
     */
    protected $entityOverrideHandler;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * NecktieEntityMapper constructor.
     * @param EntityOverrideHandler $entityOverrideHandler
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityOverrideHandler $entityOverrideHandler, EntityManagerInterface $entityManager)
    {
        $this->entityOverrideHandler = $entityOverrideHandler;
        $this->entityManager = $entityManager;
    }


    /**
     * Create a OAuthToken object from oauth server response converted to array.
     *
     * @param $array array
     *
     * @return OAuthToken|null
     */
    public function createOAuthTokenFromArray(array $array): ?OAuthToken
    {
        if (\array_key_exists('access_token', $array)
            && \array_key_exists('refresh_token', $array)
            && \array_key_exists('scope', $array)
            && \array_key_exists('expires_in', $array)
        ) {
            /** @var OAuthToken $necktieToken */
            $necktieToken = $this->entityOverrideHandler->getEntityInstance(OAuthToken::class);
            $necktieToken->setAccessToken($array['access_token']);
            $necktieToken->setRefreshToken($array['refresh_token']);
            $necktieToken->setScope($array['scope']);
            $necktieToken->setValidToByLifetime($array['expires_in']);

            return $necktieToken;
        }

        return null;
    }

    /**
     * @param array $response
     * @return array
     */
    public function getUserInfoFromNecktieProfileResponse(array $response): array
    {
        $requiredFields = ['username', 'email', 'id'];
        $userInfo = [];

        if (\is_array($response) && \array_key_exists('user', $response)) {
            $response = $response['user'];
        } else {
            return [];
        }

        foreach ($requiredFields as $requiredFiled) {
            if (\is_array($response) && \array_key_exists($requiredFiled, $response)) {
                $userInfo[$requiredFiled] = $response[$requiredFiled];
            } else {
                return [];
            }
        }

        return $userInfo;
    }

    /**
     * @param array $response
     *
     * @return array
     */
    public function getOrders(array $response): array
    {
        if (!\is_array($response) || !\array_key_exists('orders', $response)) {
            return [];
        }

        if (!\array_key_exists('orders', $response) && \is_array($response['orders'])) {
            return [];
        }

        $orders = [];

        foreach ($response['orders'] as $order) {
            $orderObject = new Order();

            if (\array_key_exists('id', $order)) {
                $orderObject->setNecktieId($order['id']);
            } else {
                continue;
            }

            if (\array_key_exists('status', $order)) {
                $orderObject->setStatus($order['status']);
            } else {
                continue;
            }

            if (\array_key_exists('receipt', $order)) {
                $orderObject->setReceipt($order['receipt']);
            } else {
                continue;
            }

            if (\array_key_exists('first_payment_date', $order)) {
                $date = \DateTime::createFromFormat(\DateTime::W3C, $order['first_payment_date']);
                $orderObject->setFirstPaymentDate($date);
            } else {
                continue;
            }

            if (\array_key_exists('items', $order)) {
                foreach ($order['items'] as $orderItem) {
                    $orderItemObject = $this->getOrderItem($orderItem);
                    if ($orderItemObject === null) {
                        continue;
                    }

                    $orderObject->addItem($orderItemObject);
                    $orderItemObject->setOrder($orderObject);
                }
            } else {
                continue;
            }

            if (\array_key_exists('full_prices', $response) &&
                \array_key_exists($orderObject->getReceipt(), $response['full_prices'])
            ) {
                $orderObject->setStringPrice($response['full_prices'][$orderObject->getReceipt()]);
            }

            $orders[] = $orderObject;
        }

        return $orders;
    }

    /**
     * @param array $response
     *
     * @return ProductAccess[]
     */
    public function getProductAccesses(array $response): array
    {
        if (!\is_array($response)) {
            return [];
        }

        if (!\array_key_exists('productAccesses', $response)) {
            return [];
        }

        if (!\is_array($response['productAccesses'])) {
            return [];
        }

        $accesses = [];

        foreach ($response['productAccesses'] as $productAccess) {
            if (\is_array($productAccess)
                && \array_key_exists('product', $productAccess)
                && \array_key_exists('from_date', $productAccess)
            ) {
                $access = $this->getProductAccess($productAccess);

                if ($access !== null) {
                    $access->setNecktieId($productAccess['id']);
                    $accesses[] = $access;
                }
            }
        }

        return $accesses;
    }

    /**
     * @param array $data
     *
     * @return ProductAccess|null
     */
    public function getProductAccess(array $data): ?ProductAccess
    {
        $product = $this
            ->entityManager
            ->getRepository(StandardProduct::class)
            ->findOneBy(['necktieId' => $data['product']]);

        if ($product === null) {
            return null;
        }

        /** @var ProductAccess $access */
        $access = $this->entityOverrideHandler->getEntityInstance(ProductAccess::class);

        $access->setProduct($product);

        $fromDate = \DateTime::createFromFormat(\DateTime::W3C, $data['from_date']);
        if (!($fromDate instanceof \DateTime)) {
            return null;
        }
        $access->setFromDate($fromDate);

        $toDate = \DateTime::createFromFormat(\DateTime::W3C, $data['to_date']);
        if ($toDate instanceof \DateTime) {
            $access->setToDate($toDate);
        }

        $access->setNecktieId($data['id']);

        return $access;
    }


    /**
     * @param array $data
     *
     * @return null|OrderItem
     */
    protected function getOrderItem(array $data): ?OrderItem
    {
        $orderItemObject = new OrderItem();

        if (\array_key_exists('billing_plan', $data)
            && \array_key_exists('product', $data['billing_plan'])
            && \array_key_exists('name', $data['billing_plan']['product'])
        ) {
            $orderItemObject->setProductName($data['billing_plan']['product']['name']);
        } else {
            $orderItemObject->setProductName('');
        }

        if (\array_key_exists('initial_price', $data)) {
            $orderItemObject->setInitialPrice($data['initial_price']);
        } else {
            return null;
        }

        if (\array_key_exists('rebill_price', $data)) {
            $orderItemObject->setRebillPrice($data['rebill_price']);
        } else {
            return null;
        }

        if (\array_key_exists('rebill_times', $data)) {
            $orderItemObject->setRebillTimes($data['rebill_times']);
        } else {
            return null;
        }

        if (\array_key_exists('id', $data)) {
            $orderItemObject->setNecktieId($data['id']);
        } else {
            return null;
        }

        if (\array_key_exists('type', $data)) {
            $orderItemObject->setType($data['type']);
        } else {
            return null;
        }

        return $orderItemObject;
    }
}
