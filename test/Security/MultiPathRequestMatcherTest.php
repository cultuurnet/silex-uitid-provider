<?php

namespace CultuurNet\UiTIDProvider\Security;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class MultiPathRequestMatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_not_match_any_request_when_no_paths_are_provided()
    {
        $requestMatcher = new MultiPathRequestMatcher([]);

        $matches = $requestMatcher->matches(new Request());
        $this->assertFalse($matches);
    }

    /**
     * @test
     */
    public function it_should_match_requests_against_multiple_paths()
    {
        $requestMatcher = new MultiPathRequestMatcher([
            '^/some/path',
            '^/some/other/path'
        ]);
        $request = new Request();
        $request->server->set('REQUEST_URI', '/some/path');
        $request->initialize($request->query->all(), $request->request->all(), $request->attributes->all(), $request->cookies->all(), $request->files->all(), $request->server->all(), $request->getContent());

        var_dump($request->getPathInfo());

        $matches = $requestMatcher->matches($request);
        $this->assertTrue($matches);
    }
}
