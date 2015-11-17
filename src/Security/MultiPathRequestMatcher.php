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

    public function __construct(Array $paths)
    {
        $this->paths = $paths;
    }

    public function matches(Request $request)
    {
        $matchesPath = false;

        if (is_array($this->paths)) {
            $i = 0;
            $pathCount = count($this->paths);

            while ($i < $pathCount && !$matchesPath) {
                $matchesPath = !!preg_match('{'.$this->paths[$i].'}', rawurldecode($request->getPathInfo()));
                $i++;
            }
        }

        return $matchesPath;
    }
}
