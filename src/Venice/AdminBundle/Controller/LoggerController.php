<?php

namespace Venice\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Trinity\Bundle\GridBundle\Exception\DuplicateColumnException;
use Trinity\Bundle\LoggerBundle\Services\ElasticReadLogService;
use Trinity\Bundle\SettingsBundle\Exception\PropertyNotExistsException;
use Trinity\FrameworkBundle\Entity\ExceptionLog;

/**
 * Class LoggerController.
 */
class LoggerController extends BaseAdminController
{
    /**
     * @Security("is_granted('ROLE_ADMIN_LOGGER_VIEW')")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     */
    public function indexAction(Request $request)
    {
        $breadcrumbs = $this->getBreadcrumbs();
        $breadcrumbs->addItem('Logger', $this->get('router')->generate('admin_logger_index'));

        return $this->render('VeniceAdminBundle:Logger:index.html.twig');
    }

    /**
     * @Security("is_granted('ROLE_ADMIN_LOGGER_VIEW')")
     *d
     *
     * @return Response
     *
     * @throws PropertyNotExistsException
     * @throws DuplicateColumnException
     */
    public function tabExceptionAction(Request $request)
    {
        $max = $this->get('trinity.logger.elastic_read_log_service')->getCount('ExceptionLog');

        $url = $this->generateUrl('grid_elastic', ['entity' => 'ExceptionLog']);

        $gridConfBuilder = $this->get('trinity.grid.grid_configuration_service')->createGridConfigurationBuilder(
            $url,
            $max,
            null,
            false,
            'createdAt:DESC'
        );

        // Defining columns
        $gridConfBuilder->addColumn('createdAt', 'Created');
        $gridConfBuilder->addColumn('ip', 'User');
        $gridConfBuilder->addColumn('user', '_hidden', ['hidden' => true]);
        $gridConfBuilder->addColumn('level', 'Level');
        $gridConfBuilder->addColumn('url', 'Url');
        $gridConfBuilder->addColumn('log', 'Log', ['allowOrder' => false]);
        $gridConfBuilder->addColumn('details', ' ', ['allowOrder' => false, 'className' => 'cell-center']);

        return $this->render(
            'VeniceAdminBundle:Logger/Exception:tab.html.twig',
            ['gridConfiguration' => $gridConfBuilder->getJSON(), 'count' => $max]
        );
    }

    /**
     * @Security("is_granted('ROLE_ADMIN_LOGGER_VIEW')")
     *
     * @param string $id
     *
     * @return Response
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     *
     * @throws RouteNotFoundException
     * @throws MissingMandatoryParametersException
     * @throws InvalidParameterException
     * @throws NotFoundHttpException
     */
    public function exceptionShowAction(string $id)
    {
        /** @var ElasticReadLogService $esLogger */
        $esLogger = $this->get('trinity.logger.elastic_read_log_service');
        /** @var ExceptionLog $entity */
        $entity = $esLogger->getById('ExceptionLog', $id);

        $breadcrumbs = $this->getBreadcrumbs();
        $breadcrumbs->addItem('Logger', $this->get('router')->generate('admin_logger_index'));
        $breadcrumbs->addItem('Exceptions', $this->get('router')->generate('admin_logger_index').'#tab1');
        $breadcrumbs->addItem(
            $entity->getId(),
            $this->get('router')->generate('admin_logger_exception_show', ['id' => $entity->getId()])
        );

        return $this->render('VeniceAdminBundle:Logger/Exception:show.html.twig', ['entity' => $entity]);
    }
}
