<?php namespace Vinelab\Http\Contracts;

/**
 * Http Response returned from {@see HttpClientInterface::request}.
 */
interface ResponseInterface
{
    /**
     * @return integer
     */
    public function statusCode();

    /**
     * @return string
     */
    public function contentType();

    /**
     * @return string
     */
    public function content();

    /**
     * @return array
     */
    public function headers();

    /**
     * @param $name
     * @return mixed
     */
    public function header($name);
}
