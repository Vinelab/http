<?php namespace Vinelab\Http\Facades;

use Illuminate\Support\Facades\Facade;

Class Client extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'vinelab.httpclient'; }

}
