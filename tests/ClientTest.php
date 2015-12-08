<?php

use Vinelab\Http\Client;

class ClientTest extends PHPUnit_Framework_TestCase
{
    public static $pidfile = './tests/server/.pidfile';
    public static $serverHost = 'localhost';
    public static $serverPort = '6767';

    public static function setUpBeforeClass()
    {
        static::bootUpBuiltInServer();
    }

    public static function tearDownAfterClass()
    {
        static::turnDownBuiltInServer();
    }

    public function setUp()
    {
        $this->client = new Client();
    }

    public function testHeaders()
    {
        $request = ['url' => 'http://'.static::serverURL().'/get.php'];
        $headers = $this->client->get($request)->headers();

        $this->assertArrayHasKey('Host', $headers, 'Headers must have Host');
        $this->assertEquals(static::$serverHost.':'.static::$serverPort, $headers['Host']);

        // custom headers
        $request['url'] = 'http://'.static::serverUrl().'/content_type.php';
        $headers = $this->client->post($request)->headers();

        $this->assertArrayHasKey('Content-Type', $headers);
        $this->assertEquals('application/json', $headers['Content-Type']);
    }

    public function testGetRequestWithoutParams()
    {
        $request = ['url' => 'http://'.static::serverURL().'/get.php'];
        $this->assertNotNull($this->client->get($request)->content());
    }

    public function testGetRequestWithParams()
    {
        $request = [
            'url' => 'http://'.static::serverURL().'/get.php',
            'params' => [

                'id' => '12350ME1D',
                'lang' => 'en-us',
                'format' => 'rss_200',
            ],
        ];

        $content = $this->client->get($request)->content();
        $this->assertNotNull($content);

        $this->assertContains('id', $content, 'Should return the id param');
        $this->assertContains('lang', $content, 'Should return the lang param');
        $this->assertContains('format', $content, 'Should return the format param');
    }

    public function testGetJSONRequest()
    {
        $request = [
            'url' => 'http://'.static::serverURL().'/json_get.php',
            'params' => [

                'id' => '12350ME1D',
                'lang' => 'en-us',
                'format' => 'json',
            ],
        ];

        $content = $this->client->get($request)->json();

        $this->assertInstanceOf('stdClass', $content, 'Content should be returned as an object');
        $this->assertObjectHasAttribute('id', $content);
        $this->assertObjectHasAttribute('lang', $content);
        $this->assertObjectHasAttribute('format', $content);
    }

    public function testPostRequestWithoutParams()
    {
        $request = ['url' => 'http://'.static::serverURL().'/post.php'];
        $this->assertNotNull($this->client->post($request)->content());
    }

    public function testPostRequestWithparams()
    {
        $request = [
            'url' => 'http://'.static::serverURL().'/post.php',
            'params' => [

                'id' => '12350ME1D',
                'lang' => 'en-us',
                'format' => 'json',
            ],
        ];

        $content = $this->client->post($request)->content();

        $this->assertContains('id', $content, 'Should return the id param');
        $this->assertContains('lang', $content, 'Should return the lang param');
        $this->assertContains('format', $content, 'Should return the format param');
    }

    public function testPostJSONRequest()
    {
        $request = [
            'url' => 'http://'.static::serverURL().'/json_post.php',
            'params' => [

                'id' => '12350ME1D',
                'lang' => 'en-us',
                'format' => 'json',
            ],

            'json' => true,
        ];

        $content = $this->client->post($request)->json();

        $this->assertInstanceOf('stdClass', $content);
        $this->assertObjectHasAttribute('id', $content, 'Should return the id param');
        $this->assertObjectHasAttribute('lang', $content, 'Should return the lang param');
        $this->assertObjectHasAttribute('format', $content, 'Should return the format param');
    }

    /**
     * @beforeClass
     */
    public static function bootUpBuiltInServer()
    {
        shell_exec('php -S '.static::serverURL().' -t ./tests/server > /dev/null 2>&1 & echo $! >> '.static::$pidfile);
        // let's wait a couple of seconds for the server to be up
        sleep(2);
    }

    /**
     * @afterClass
     */
    public static function turnDownBuiltInServer()
    {
        $filename = static::$pidfile;

        if (file_exists($filename)) {
            $fileContent = file($filename);
            $pid = array_pop($fileContent);
            shell_exec('kill -9 '.$pid);
            unlink($filename);
        }
    }

    public static function serverURL()
    {
        return static::$serverHost.':'.static::$serverPort;
    }
}
