<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 17.01.16
 * Time: 11:59
 */

namespace FrontBundle\Twig;


use AppBundle\Entity\Content\Content;

class RenderDefaultContentTemplateExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter(
                "renderDefault",
                [
                    $this,
                    "renderDefault",
                ],
                [
                    "is_safe" => ["html"],
                    "needs_environment" => true,
                ]
            )
        ];
    }

    public function renderDefault(\Twig_Environment $twig, Content $content)
    {
        return $twig->render(
            "FrontBundle:Content:".$content->getType()."Default.html.twig",
            ["content" => $content]
        );
    }


    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return "app_render_default_content_template_extension";
    }
}