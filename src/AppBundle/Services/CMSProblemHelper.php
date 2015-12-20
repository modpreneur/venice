<?php
///**
// * Created by PhpStorm.
// * User: Jakub Fajkus
// * Date: 06.11.15
// * Time: 16:46
// */
//
//namespace AppBundle\Services;
//
//
//use AppBundle\Entity\BillingPlan;
//use Symfony\Component\Config\Definition\Exception\Exception;
//use Symfony\Component\DependencyInjection\ContainerInterface;
//
//class CMSProblemHelper
//{
//    const NECKTIE = "necktie";
//    const AMEMBER = "amember";
//
//    /**
//     * @var string
//     */
//    protected $primaryCMS;
//
//
//    /**
//     * @param ContainerInterface $container
//     * @param string             $primaryCMS
//     *
//     */
//    public function __construct(ContainerInterface $container, $primaryCMS = "")
//    {
//        if(!($primaryCMS == self::NECKTIE || $primaryCMS == self::AMEMBER))
//        {
//            throw new Exception("Expected one of: " . self::NECKTIE . " or " . self::AMEMBER . ", " . $primaryCMS . " given.");
//        }
//
//        $this->primaryCMS = $primaryCMS;
//    }
//
//
//    /**
//     * Get billing plan id depending on to which CMS(necktie, amember, ...) is main.
//     *
//     * @param BillingPlan $billingPlan
//     *
//     * @return int|null
//     */
//    public function getBillingPlanId(BillingPlan $billingPlan)
//    {
//        if($this->primaryCMS == self::NECKTIE)
//        {
//            return $billingPlan->getNecktieId();
//        }
//        else if($this->primaryCMS == self::AMEMBER)
//        {
//            return $billingPlan->getAmemberId();
//        }
//
//        return null;
//    }
//
//}