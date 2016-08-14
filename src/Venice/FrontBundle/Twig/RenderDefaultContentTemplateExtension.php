<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 17.01.16
 * Time: 11:59.
 */
namespace Venice\FrontBundle\Twig;

use Venice\AppBundle\Entity\Content\Content;

/**
 * Class RenderDefaultContentTemplateExtension.
 */
class RenderDefaultContentTemplateExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter(
                'renderDefault',
                [
                    $this,
                    'renderDefault',
                ],
                [
                    'is_safe' => ['html'],
                    'needs_environment' => true,
                ]
            ),
        ];
    }

    public function renderDefault(\Twig_Environment $twig, Content $content)
    {
        return $twig->render(
            'VeniceFrontBundle:Content:'.$content->getType().'Default.html.twig',
            ['content' => $content]
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'app_render_default_content_template_extension';
    }
}
