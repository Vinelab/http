<?php namespace Vinelab\Http;

Class Client {

	/**
	 * Performs a GET reuqest
	 * @param  URL|Array $request
	 * @return \Vinelab\Http\Response
	 */

	/**
	 * Validates passed request parameters
	 * @param  URL|Array $request
	 * @return Boolean
	 */
	protected function valid($request)
	{
		return (boolean) array_key_exists('url', $request);
	}

	/**
	 * Makes a Vinelab\Http\Request object out of an array
	 * @param  array|string $request
	 * @return Vinelab\Http\Request
	 */
	private function requestInstance($request)
	{
		return new Request($request);
	}

	public function __call($method, $arguments)
	{
		$request = array_pop($arguments);

		// add support to sending requests to url's only
		if(!is_array($request))
		{
			$request = ['url'=>$request];
		}

		if ($this->valid($request))
		{
			$request['method'] = Request::method($method);
			$this->request = $this->requestInstance($request);
			return $this->request->send();
		}

		throw new \Exception('Invalid Request Params sent to HttpClient');
	}
}
