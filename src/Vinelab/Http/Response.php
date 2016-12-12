<?php

namespace Vinelab\Http;

use Vinelab\Http\Contracts\ResponseInterface;
use Vinelab\Http\Exceptions\HttpClientRequestFailedException;

/**
 * The HTTP Response.
 *
 * @author Abed Halawi <abed.halawi@vinelab.com>
 *
 * @since 1.0.0
 */
class Response implements ResponseInterface
{
    /**
     * The result coming from curl_getinfo().
     *
     * @var array
     */
    protected $info = [];

    /**
     * Response Content (Body).
     *
     * @var mixed
     */
    protected $content;

    /**
     * Response Headers.
     *
     * @var string
     */
    protected $headers = [];

    /**
     * @param cURL Handle $cURL
     */
    public function __construct($cURL)
    {
        $response = curl_exec($cURL);

        if (!curl_errno($cURL)) {
            $this->info = curl_getinfo($cURL);
            $this->headers = $this->parseHeaders($response, $this->info['header_size']);
            $this->content = $this->parseBody($response, $this->info['header_size']);
        } else {
            throw new HttpClientRequestFailedException(curl_error($cURL));
        }

        curl_close($cURL);
    }

    /**
     * Initiates a request.
     *
     * @param cURL Handle $cURL
     *
     * @return Vinelab\Http\Response
     */
    public static function make($cURL)
    {
        return new static($cURL);
    }

    /**
     * Get the information about this response,
     * including header, status code and content.
     *
     * @return array
     */
    public function info()
    {
        return $this->info;
    }

    /**
     * Get the status code for this response instance.
     *
     * @return int
     */
    public function statusCode()
    {
        return $this->info['http_code'];
    }

    /**
     * Get the content type of this response instance.
     *
     * @return string
     */
    public function contentType()
    {
        return $this->info['content_type'];
    }

    /**
     * Get the content of this response instance.
     *
     * @return mixed
     */
    public function content()
    {
        return $this->content;
    }

    /**
     * Parse the headers of this response instance.
     *
     * @param string $response
     * @param string $headerSize
     *
     * @return array
     */
    protected function parseHeaders($response, $headerSize)
    {
        $headers = substr($response, 0, $headerSize);
        $parsedHeaders = [];

        foreach (explode("\r\n", $headers) as $header) {
            if (strpos($header, ':')) {
                $nestedHeader = explode(':', $header);
                $parsedHeaders[array_shift($nestedHeader)] = trim(implode(':', $nestedHeader));
            }
        }

        return $parsedHeaders;
    }

    /**
     * Get the headers of this response instance.
     *
     * @return array
     */
    public function headers()
    {
        return $this->headers;
    }

    /**
     * Get a specific header from the headers of this response instance.
     *
     * @param string $name
     *
     * @return string
     */
    public function header($name)
    {
        return (array_key_exists($name, $this->headers)) ? $this->headers[$name] : null;
    }

    /**
     * Parses the body (content) out of the response.
     *
     * @param cURL Response    $response
     * @param cURL Header Size $headerSize
     *
     * @return string
     */
    public function parseBody($response, $headerSize)
    {
        return substr($response, $headerSize);
    }

    /**
     * Content encoded as JSON.
     *
     * @return string
     */
    public function json()
    {
        return json_decode($this->content);
    }

    /**
     * Content encoded as XML.
     *
     * @return SimpleXMLElement
     */
    public function xml()
    {
        return new \SimpleXMLElement($this->content);
    }

    /**
     * Primary IP.
     *
     * @return string
     */
    public function ip()
    {
        return $this->info['primary_ip'];
    }
}
