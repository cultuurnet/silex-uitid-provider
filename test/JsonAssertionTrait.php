<?php

namespace CultuurNet\UiTIDProvider;

trait JsonAssertionTrait
{
    /**
     * @param string $json
     * @param string $filePath
     */
    private function assertJsonEquals($json, $filePath)
    {
        $expected = $this->getJson($filePath);
        $expected = json_decode($expected);

        $actual = json_decode($json);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @param string $filePath
     * @return string
     */
    private function getJson($filePath)
    {
        return file_get_contents(__DIR__ . '/' . $filePath);
    }
}
