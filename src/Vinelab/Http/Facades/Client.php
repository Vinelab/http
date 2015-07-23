<?php

namespace Vinelab\Http\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Client's facade.
 *
 * @author Abed Halawi <abed.halawi@vinelab.com>
 *
 * @since 1.0.0
 */
class Client extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'vinelab.httpclient';
    }
}
