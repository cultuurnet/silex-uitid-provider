<?php

namespace CultuurNet\UiTIDProvider\Security;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;

class MultiPathRequestMatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RequestMatcherInterface
     */
    private $requestMatcher;

    public function setUp()
    {
        $this->requestMatcher = new MultiPathRequestMatcher([
            '^/some/path',
            '^/some/other/path'
        ]);
    }

    /**
     * @test
     */
    public function it_matches_requests_against_multiple_paths()
    {
        $matchingRequest = new Request();
        $matchingRequest->server->set('REQUEST_URI', '/some/path');
        $matchingRequest->initialize(
            $matchingRequest->query->all(),
            $matchingRequest->request->all(),
            $matchingRequest->attributes->all(),
            $matchingRequest->cookies->all(),
            $matchingRequest->files->all(),
            $matchingRequest->server->all(),
            $matchingRequest->getContent()
        );

        $matches = $this->requestMatcher->matches($matchingRequest);
        $this->assertTrue($matches);
    }

    /**
     * @test
     */
    public function it_does_not_match_a_path_not_in_the_multi_path_configuration()
    {
        $nonMatchingRequest = new Request();
        $nonMatchingRequest->server->set('REQUEST_URI', '/incorrect/path');
        $nonMatchingRequest->initialize(
            $nonMatchingRequest->query->all(),
            $nonMatchingRequest->request->all(),
            $nonMatchingRequest->attributes->all(),
            $nonMatchingRequest->cookies->all(),
            $nonMatchingRequest->files->all(),
            $nonMatchingRequest->server->all(),
            $nonMatchingRequest->getContent()
        );

        $matches = $this->requestMatcher->matches($nonMatchingRequest);
        $this->assertFalse($matches);
    }

    /**
     * @test
     */
    public function it_does_not_match_any_request_when_no_paths_are_provided()
    {
        $requestMatcher = new MultiPathRequestMatcher([]);

        $matches = $requestMatcher->matches(new Request());
        $this->assertFalse($matches);
    }

    /**
     * @test
     */
    public function it_does_match_the_method_when_provided()
    {
        $this->requestMatcher = new MultiPathRequestMatcher(
            [
                '^/some/path',
                '^/some/other/path'
            ],
            [
                'GET',
                'DELETE'
            ]
        );

        $matchingRequest = Request::create('/some/path', 'GET');
        $matches = $this->requestMatcher->matches($matchingRequest);
        $this->assertTrue($matches);


        $nonMatchingRequest = Request::create('/some/other/path', 'GET');
        $matches = $this->requestMatcher->matches($nonMatchingRequest);
        $this->assertFalse($matches);
    }
}
