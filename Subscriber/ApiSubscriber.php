<?php

namespace Shopware\DsnApiLog\Subscriber;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ApiSubscriber implements \Enlight\Event\SubscriberInterface
{
    /** @var  Logger */
    protected $logger;

    public static function getSubscribedEvents()
    {
        return array(
            'Enlight_Controller_Action_PostDispatch_Api' => 'onPostDispatch',
            'Enlight_Controller_Action_PreDispatch_Api' => 'onPreDispatch',
        );
    }

    /**
     * @return Logger
     */
    private function getLogger()
    {
        if ($this->logger) {
            return $this->logger;
        }

        return $this->logger = new Logger('apiLogger', [new RotatingFileHandler(Shopware()->Container()->getParameter('kernel.logs_dir') . '/api.log', 7)]);
    }

    public function onPostDispatch(\Enlight_Event_EventArgs $args)
    {
        /** @var $controller \Enlight_Controller_Action */
        $controller = $args->getSubject();

        // this will bloat the log file
        // $this->getLogger()->info('RESPONSE: ' . $controller->Response()->getBody());

        $this->getLogger()->info('RESPONSE-CODE: ' . $controller->Response()->getHttpResponseCode());

    }
    public function onPreDispatch(\Enlight_Event_EventArgs $args)
    {
        /** @var $controller \Enlight_Controller_Action */
        $controller = $args->getSubject();
        $view = $controller->View();

        $this->getLogger()->info('REQUEST: ' . $controller->Request()->getMethod() . ' - ' . $controller->Request()->getRequestUri());

        // this will bloat the log file
        // $this->getLogger()->info('PAYLOAD: ' . $controller->Request()->getPost());
    }
}