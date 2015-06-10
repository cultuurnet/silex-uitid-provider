<?php

namespace CultuurNet\UiTIDProvider;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\Route;

class UserControllerProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var ControllerCollection
     */
    protected $controllerCollection;

    public function setUp()
    {
        $this->app = new Application();
        $this->controllerCollection = new ControllerCollection(new Route());
    }

    /**
     * @test
     */
    public function it_uses_an_existing_controller_collection_if_provided()
    {
        $userControllerProvider = new UserControllerProvider();
        $controllers = $userControllerProvider->connect($this->app);
        $this->assertInstanceOf(ControllerCollection::class, $controllers);

        $userControllerProvider = new UserControllerProvider($this->controllerCollection);
        $controllers = $userControllerProvider->connect($this->app);
        $this->assertEquals($this->controllerCollection, $controllers);
    }
}
