<?php

namespace CultuurNet\UiTIDProvider;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

abstract class ControllerExtensionProvider implements ControllerProviderInterface
{
    /**
     * @var ControllerCollection
     */
    protected $controllers;

    /**
     * @param ControllerCollection $controllerCollection
     */
    public function __construct(ControllerCollection $controllerCollection = null)
    {
        $this->controllers = $controllerCollection;
    }

    /**
     * Returns either an existing ControllerCollection, or a new one.
     *
     * @param Application $app
     *
     * @return ControllerCollection
     */
    protected function getControllerCollection(Application $app)
    {
        if (!is_null($this->controllers)) {
            return $this->controllers;
        } else {
            return $app['controllers_factory'];
        }
    }
}
