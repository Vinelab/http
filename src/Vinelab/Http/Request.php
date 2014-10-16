<?php namespace Vinelab\Http;

use Vinelab\Http\Contracts\RequestInterface;

Class Request implements RequestInterface{

	/**
	 * The default request schema
	 * @var Array
	 */
	protected $default = [

		'url'        => null,
		'params' => [],
		'headers'    => [],
		'options'    => [],
		'returnTransfer' => true,
		'json'		 => false
	];

	/**
	 * HTTP Request Method
	 * @var string
	 */
	public $method = self::METHOD_GET;

	/**
	 * @var Array
	 */
	public $params = [];

	/**
	 * @var String
	 */
	public $url = null;

	/**
	 * Request Headers
	 * @var Associative Array
	 */
	public $headers = [];

	/**
	 * Sets the request
	 * @var boolean
	 */
	public $json= false;

	/**
	 * Return cURL transfer or not
	 * @var boolean
	 */
	public $returnTransfer = true;

	/**
	 * @param Array $requestData
	 */
	function __construct($requestData)
	{
		$data = array_merge($this->default, $requestData);

		$this->url     = $data['url'];
		$this->method  = $data['method'];
		$this->params  = $data['params'];
		$this->headers = $data['headers'];
		$this->json    = $data['json'];

		if ($this->json)
		{
			array_push($this->headers, 'Content-Type: application/json');
		}
	}

	public function send()
	{
		$cURLOptions = array(
			CURLOPT_URL            => $this->url,
			CURLOPT_CUSTOMREQUEST  => $this->method,
			CURLOPT_RETURNTRANSFER => $this->returnTransfer,
			CURLOPT_HTTPHEADER     => $this->headers,
			CURLOPT_HEADER         => true,
			CURLINFO_HEADER_OUT    => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_MAXREDIRS      => 50
		);

		if ($this->method === static::method('POST'))
		{
			$cURLOptions[CURLOPT_POST] = count($this->params);
			$cURLOptions[CURLOPT_POSTFIELDS] = ($this->json) ? json_encode($this->params) : $this->params;

		} elseif (count($this->params) > 0) {
			$this->url = $this->url.'?'.http_build_query($this->params);
			$cURLOptions[CURLOPT_URL] = $this->url;
		}

		// initialize cURL
		$cURL = curl_init();
		curl_setopt_array($cURL, $cURLOptions);

		return Response::make($cURL);
	}

	/**
	 * returns the value of an HTTP Verb constant of this class
	 *
	 * @param  string $method HTTP Verb
	 * @return string
	 */
	public static function method($method)
	{
		$const = 'METHOD_'.strtoupper($method);
		return constant('self::'.$const);
	}
}
