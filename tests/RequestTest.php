<?php

use Vinelab\Http\Request;

class RequestTest extends PHPUnit_Framework_TestCase
{
    public function testInitializingEmptyRequest()
    {
        $this->assertInstanceOf('Vinelab\Http\Request', new Request());
    }

    public function testGettingCurlHttpVersion()
    {
        $request = new Request();
        $this->assertEquals(CURL_HTTP_VERSION_NONE, $request->getCurlHttpVersion());

        $request10 = new Request(['version' => 1.0]);
        $this->assertEquals(CURL_HTTP_VERSION_1_0, $request10->getCurlHttpVersion());

        $request10String = new Request(['version' => '1.0']);
        $this->assertEquals(CURL_HTTP_VERSION_1_0, $request10String->getCurlHttpVersion());

        $request11 = new Request(['version' => 1.1]);
        $this->assertEquals(CURL_HTTP_VERSION_1_1, $request11->getCurlHttpVersion());

        $request11String = new Request(['version' => '1.1']);
        $this->assertEquals(CURL_HTTP_VERSION_1_1, $request11String->getCurlHttpVersion());
    }
}
