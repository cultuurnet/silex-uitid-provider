<?php

namespace CultuurNet\UiTIDProvider\Security;

use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\HttpFoundation\Request;

class MultiPathRequestMatcher implements RequestMatcherInterface
{
    /**
     * @var String[]
     */
    protected $paths;

    /**
     * @var String[]
     */
    protected $methods;

    public function __construct(array $paths, array $methods = [])
    {
        $this->paths = $paths;
        $this->methods = $methods;
    }

    public function matches(Request $request)
    {
        $matchesPath = false;

        if (is_array($this->paths)) {
            $i = 0;
            $pathCount = count($this->paths);

            while ($i < $pathCount && !$matchesPath) {
                $matchesPath = !!preg_match('{'.$this->paths[$i].'}', rawurldecode($request->getPathInfo()));

                // if we have a matching path and we are checking for methods
                // make sure the method matches as well
                if(!empty($this->methods)
                    && $this->methods[$i]
                    && $matchesPath
                    && $this->methods[$i] != $request->getMethod()
                ) {
                    $matchesPath = false;
                }
                $i++;
            }
        }

        return $matchesPath;
    }
}
