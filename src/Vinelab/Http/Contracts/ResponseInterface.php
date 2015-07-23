<?php

namespace Vinelab\Http\Contracts;

/**
 * Http Response returned from {@see HttpClientInterface::request}.
 *
 * @author Abed Halawi <abed.halawi@vinelab.com>
 *
 * @since 1.0.0
 */
interface ResponseInterface
{
    /**
     * @return int
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
     *
     * @return mixed
     */
    public function header($name);
}
