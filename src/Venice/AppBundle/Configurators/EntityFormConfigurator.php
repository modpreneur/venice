<?php

namespace Venice\AppBundle\Configurators;

use Venice\AppBundle\Entity\BillingPlan;
use Venice\AppBundle\Entity\BlogArticle;
use Venice\AppBundle\Entity\Category;
use Venice\AppBundle\Entity\Content\Content;
use Venice\AppBundle\Entity\Content\ContentInGroup;
use Venice\AppBundle\Entity\Content\GroupContent;
use Venice\AppBundle\Entity\Content\HtmlContent;
use Venice\AppBundle\Entity\Content\IframeContent;
use Venice\AppBundle\Entity\Content\Mp3Content;
use Venice\AppBundle\Entity\Content\PdfContent;
use Venice\AppBundle\Entity\Content\VideoContent;
use Venice\AppBundle\Entity\Product\FreeProduct;
use Venice\AppBundle\Entity\Product\Product;
use Venice\AppBundle\Entity\Product\StandardProduct;
use Venice\AppBundle\Entity\ProductAccess;
use Venice\AppBundle\Entity\Tag;
use Venice\AppBundle\Entity\User;
use Venice\AppBundle\Form\BillingPlanType;
use Venice\AppBundle\Form\BlogArticleType;
use Venice\AppBundle\Form\CategoryType;
use Venice\AppBundle\Form\Content\ContentInGroupType;
use Venice\AppBundle\Form\Content\ContentType;
use Venice\AppBundle\Form\Content\GroupContentType;
use Venice\AppBundle\Form\Content\HtmlContentType;
use Venice\AppBundle\Form\Content\IframeContentType;
use Venice\AppBundle\Form\Content\Mp3ContentType;
use Venice\AppBundle\Form\Content\PdfContentType;
use Venice\AppBundle\Form\Content\VideoContentType;
use Venice\AppBundle\Form\Product\FreeProductType;
use Venice\AppBundle\Form\Product\ProductType;
use Venice\AppBundle\Form\Product\StandardProductType;
use Venice\AppBundle\Form\ProductAccessType;
use Venice\AppBundle\Form\TagType;
use Venice\AppBundle\Form\User\UserType;

/**
 * This class defines which entities should use which forms.
 * This could be also done with configuration.
 * But that approach would require hardcoding the namespaces for over 30 classes.
 *
 * Class EntityFormConfigurator
 */
class EntityFormConfigurator extends BaseConfigurator
{
    /**
     * EntityFormConfigurator constructor.
     *
     * @param array $configuration
     */
    public function __construct(array $configuration = [])
    {
        //override the values defined in the constructor with the values from the constructor's argument
        parent::__construct(
            array_merge(
                [
                    //content
                    Content::class => ContentType::class,
                    ContentInGroup::class => ContentInGroupType::class,
                    GroupContent::class => GroupContentType::class,
                    HtmlContent::class => HtmlContentType::class,
                    IframeContent::class => IframeContentType::class,
                    Mp3Content::class => Mp3ContentType::class,
                    PdfContent::class => PdfContentType::class,
                    VideoContent::class => VideoContentType::class,

                    //product
                    FreeProduct::class => FreeProductType::class,
                    Product::class => ProductType::class,
                    StandardProduct::class => StandardProductType::class,
                    BillingPlan::class => BillingPlanType::class,

                    //user
                    User::class => UserType::class,
                    ProductAccess::class => ProductAccessType::class,

                    //blog
                    BlogArticle::class => BlogArticleType::class,
                    Category::class => CategoryType::class,
                    Tag::class => TagType::class,

                ],
                $configuration
            )
        );
    }
}
