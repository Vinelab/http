<?php

namespace Vinelab\Http;

use Tolerance\Waiter\CountLimited;
use Tolerance\Operation\Callback;
use Tolerance\Waiter\ExponentialBackOff;
use Tolerance\Waiter\SleepWaiter;
use Tolerance\Operation\Runner\RetryOperationRunner;
use Tolerance\Operation\Runner\CallbackOperationRunner;

/**
 * The HTTP Client.
 *
 * Dynamically calls a method that sends the corresponding request.
 *
 * @author Abed Halawi <abed.halawi@vinelab.com>
 *
 * @since 1.0.0
 */
class Client
{
    /**
     * Validates passed request parameters.
     *
     * @param string|array $request
     *
     * @return bool
     */
    protected function valid($request)
    {
        return (boolean) array_key_exists('url', $request);
    }

    /**
     * Makes a Vinelab\Http\Request object out of an array.
     *
     * @param array|string $request
     *
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
        if (!is_array($request)) {
            $request = ['url' => $request];
        }

        if ($this->valid($request)) {
            $request['method'] = Request::method($method);
            $this->request = $this->requestInstance($request);

            $operation = new Callback(function () use ($request) {
                return $this->request->send();
            });

            // Creates the strategy used to wait between failing calls
            $waitStrategy = new CountLimited(
                new ExponentialBackOff(
                    new SleepWaiter(),
                    (getenv('TIME_UNTIL_NEXT_TRY') ?: 1)
                ),
                (getenv('NUMBER_OF_TRIES_UNTIL_FAILURE') ?: 5)
            );

            // Creates the runner
            $runner = new RetryOperationRunner(
                new CallbackOperationRunner(),
                $waitStrategy
            );

            return $runner->run($operation);
        }

        throw new \Exception('Invalid Request Params sent to HttpClient');
    }
}
