<?php

namespace Vinelab\Http;

use Vinelab\Http\Contracts\RequestInterface;

/**
 * The HTTP Request.
 *
 * @author Abed Halawi <abed.halawi@vinelab.com>
 *
 * @since 1.0.0
 */
class Request implements RequestInterface
{
    /**
     * The default request schema.
     *
     * @var Array
     */
    protected $default = [

        'version' => null,
        'method' => self::METHOD_GET,
        'url' => null,
        'content' => null,
        'params' => [],
        'headers' => [],
        'options' => [],
        'json' => false,
        'maxRedirects' => 50,
        'timeout' => 30,
        'tolerant' => false,
        'timeUntilNextTry' => 1,
        'triesUntilFailure' => 5,
        'digestAuth'     => [],
        'basicAuth'     => [],
    ];

    /**
     * HTTP Request Method.
     *
     * @var string
     */
    public $method = self::METHOD_GET;

    /**
     * Specify the HTTP protocol version (1.0/1.1).
     *
     * @var float
     */
    public $version = null;

    /**
     * @var Array
     */
    public $params = [];

    /**
     * @var String
     */
    public $url = null;

    /**
     * Raw content.
     *
     * @var string
     */
    public $content = null;

    /**
     * Request Headers.
     *
     * @var Associative Array
     */
    public $headers = [];

    /**
     * Sets the request.
     *
     * @var bool
     */
    public $json = false;

    /**
     * Return cURL max redirect times.
     *
     * @var int
     */
    public $maxRedirects = 50;
    /**
     * Return cURL timeout seconds.
     *
     * @var int
     */
    public $timeout = 30;

    /**
     * Sets fault tolerance.
     *
     * @var bool
     */
    public $tolerant = false;

    /**
     * Sets fault tolerance time between tries.
     *
     * @var int
     */
    public $timeUntilNextTry = 1;

    /**
     * Sets fault tolerance number of tries until failure.
     *
     * @var int
     */
    public $triesUntilFailure = 5;

    /**
     * Digest Auth
     * @var array
     */
    public $digestAuth = [];

    /**
     * Basic Auth
     * @var array
     */
    public $basicAuth = [];

    /**
     * @param array $requestData
     */
    public function __construct($requestData = array())
    {
        $data = array_merge($this->default, $requestData);

        $this->httpVersion = $data['version'];
        $this->url = $data['url'];
        $this->content = $data['content'];
        $this->method = $data['method'];
        $this->params = $data['params'];
        $this->headers = $data['headers'];
        $this->json = $data['json'];
        $this->maxRedirects = $data['maxRedirects'];
        $this->timeout = $data['timeout'];
        $this->tolerant = $data['tolerant'];
        $this->timeUntilNextTry = $data['timeUntilNextTry'];
        $this->triesUntilFailure = $data['triesUntilFailure'];
        if (isset($data['digest'])) {
            $this->digestAuth = $data['digest'];
        }
        if (isset($data['auth'])) {
            $this->basicAuth = $data['auth'];
        }

        if ($this->json) {
            array_push($this->headers, 'Content-Type: application/json');
        }
    }

    /**
     * Send a CURL request.
     *
     * @return Vinelab\Http\Response
     */
    public function send()
    {
        $cURLOptions = array(
            CURLOPT_HTTP_VERSION => $this->getCurlHttpVersion(),
            CURLOPT_URL => $this->url,
            CURLOPT_CUSTOMREQUEST => $this->method,
            CURLOPT_HTTPHEADER => $this->headers,
            CURLOPT_HEADER => true,
            CURLINFO_HEADER_OUT => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => $this->maxRedirects,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_RETURNTRANSFER => true,
        );

        //digest auth support
        if(count($this->digestAuth) > 0)  {
            $cURLOptions[CURLOPT_HTTPAUTH] = CURLAUTH_DIGEST;
            $cURLOptions[CURLOPT_USERPWD] =  $this->digestAuth['username'] . ":" . $this->digestAuth['password'];
        } else if (count($this->basicAuth) > 0) {
            $cURLOptions[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
            $cURLOptions[CURLOPT_USERPWD] =  $this->basicAuth['username'] . ":" . $this->basicAuth['password'];
        }

        if ($this->method === static::method('POST')
            || $this->method === static::method('PUT')
            || $this->method === static::method('PATCH')
        ) {
            if (count($this->params) > 0) {
                $cURLOptions[CURLOPT_POST] = count($this->params);
                $cURLOptions[CURLOPT_POSTFIELDS] = ($this->json) ? json_encode($this->params) : $this->params;
            } elseif (!is_null($this->content)) {
                $cURLOptions[CURLOPT_POST] = strlen($this->content);
                $cURLOptions[CURLOPT_POSTFIELDS] = ($this->json) ? json_encode($this->content) : $this->content;
            }
        } elseif (count($this->params) > 0) {
            $this->url = $this->url.'?'.http_build_query($this->params);
            $cURLOptions[CURLOPT_URL] = $this->url;
        } elseif (!is_null($this->content)) {
            $cURLOptions[CURLOPT_URL] = $this->url.'?'.$this->content;
        }

        // initialize cURL
        $cURL = curl_init();
        curl_setopt_array($cURL, $cURLOptions);

        return Response::make($cURL);
    }

    /**
     * returns the value of an HTTP Verb constant of this class.
     *
     * @param string $method HTTP Verb
     *
     * @return string
     */
    public static function method($method)
    {
        $const = 'METHOD_'.strtoupper($method);

        return constant('self::'.$const);
    }

    /**
     * Get the cURL Equivalent for HTTP version.
     *
     * @return int
     */
    public function getCurlHttpVersion()
    {
        $version = $this->httpVersion;

        if (is_numeric($version)) {
            $version = floatval($version);
        }

        switch ($version) {
            case 1.0:
                $cURLVersion = CURL_HTTP_VERSION_1_0;
            break;

            case 1.1:
                $cURLVersion = CURL_HTTP_VERSION_1_1;
            break;

            default:
                $cURLVersion = CURL_HTTP_VERSION_NONE;
            break;
        }

        return $cURLVersion;
    }
}
