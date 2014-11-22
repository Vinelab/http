<?php namespace Vinelab\Http;

use Vinelab\Http\Contracts\ResponseInterface;

Class Response implements ResponseInterface {

	/**
	 * The result coming from curl_getinfo()
	 * @var Array
	 */
	protected $info = [];

	/**
	 * Response Content (Body)
	 * @var Mixed
	 */
	protected $content;

	/**
	 * Response Headers
	 * @var String
	 */
	protected $headers = [];

	/**
	 * @param cURL Handle $cURL
	 */
	function __construct($cURL)
	{
		curl_setopt($cURL, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		$response = curl_exec($cURL);

		if (!curl_errno($cURL))
		{
			$this->info    = curl_getinfo($cURL);
			$this->headers = $this->parseHeaders($response, $this->info['header_size']);
			$this->content = $this->parseBody($response, $this->info['header_size']);

		} else {

			throw new \Exception(curl_error($cURL));
		}

		curl_close($cURL);
	}

	/**
	 * Initiates a request
	 * @param  cURL Handle $cURL
	 * @return Response
	 */
	public static function make($cURL)
	{
		return new static($cURL);
	}

	/**
	 * @return @var info
	 */
	public function info()
	{
		return $this->info;
	}

	public function statusCode()
	{
		return $this->info['http_code'];
	}

	public function contentType()
	{
		return $this->info['content_type'];
	}

	public function content()
	{
		return $this->content;
	}

	protected function parseHeaders($response, $headerSize)
	{
		$headers = substr($response, 0, $headerSize);
		$parsedHeaders = [];

		foreach (explode("\r\n",$headers) as $header)
		{
			if (strpos($header, ':'))
			{
				$nestedHeader = explode(':', $header);
				$parsedHeaders[$nestedHeader[0]] = trim($nestedHeader[1]);
			}
		}

		return $parsedHeaders;
	}

	public function headers()
	{
		return $this->headers;
	}

	public function header($name)
	{
		return (array_key_exists($name, $this->headers)) ? $this->headers[$name] : null;
	}

	/**
	 * Parses the body (content) out of the response
	 * @param  cURL Response $response
	 * @param  cURL Header Size $headerSize
	 * @return string
	 */
	public function parseBody($response, $headerSize)
	{
		return substr($response, $headerSize);
	}

	/**
	 * Content encoded as JSON
	 * @return string
	 */
	public function json()
	{
		return json_decode($this->content);
	}

	/**
	 * Content encoded as XML
	 * @return SimpleXMLElement
	 */
	public function xml()
	{
		return new \SimpleXMLElement($this->content);
	}

	/**
	 * Primary IP
	 * @return string
	 */
	public function ip()
	{
		return $this->info['primary_ip'];
	}
}
