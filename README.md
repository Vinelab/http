![build status](https://travis-ci.org/Vinelab/http.png?branch=master "build status")

[![Dependency Status](https://www.versioneye.com/user/projects/53efc9a613bb06cc6f0004b0/badge.svg?style=flat)](https://www.versioneye.com/user/projects/53efc9a613bb06cc6f0004b0)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/0663136a-6dde-4159-bc96-d1749599dca4/big.png)](https://insight.sensiolabs.com/projects/0663136a-6dde-4159-bc96-d1749599dca4)

# http://Client

A smart, simple and fault-tolerant HTTP client for sending and recieving JSON and XML.


## Installation

### Composer

`composer require vinelab/http`

```php
// change this to point correctly according
// to your folder structure.
require './vendor/autoload.php';

use Vinelab\Http\Client as HttpClient;

$client = new HttpClient;

$response = $client->get('echo.jsontest.com/key/value/something/here');

var_dump($response->json());
```

### Laravel

Edit **app.php** and add ```'Vinelab\Http\HttpServiceProvider',``` to the ```'providers'``` array.

It will automatically alias itself as **HttpClient** so no need to alias it in your **app.php**, unless you would like to customize it - in that case edit your **'aliases'** in **app.php** adding ``` 'MyHttp'	  => 'Vinelab\Http\Facades\Client',```

## Usage

### GET

#### Simple

```php
$response = HttpClient::get('http://example.org');

// raw content
$response->content();
```

#### With Params

```php
$request = [
	'url' => 'http://somehost.net/something',
	'params' => [

		'id'     => '12350ME1D',
		'lang'   => 'en-us',
		'format' => 'rss_200'
	]
];

$response = HttpClient::get($request);

// raw content
$response->content();

// in case of json
$response->json();

// XML
$response->xml();
```

### POST

```php
$request = [
	'url' => 'http://somehost.net/somewhere',
	'params' => [

		'id'     => '12350ME1D',
		'lang'   => 'en-us',
		'format' => 'rss_200'
	]
];

$response = HttpClient::post($request);

// raw content
$response->content();

// in case of json
$response->json();

// XML
$response->xml();
```

### Options
These options work with all requests.

#### Timeout

You can set the `timeout` option in order to specify the number of seconds that your request will fail, if not completed.

```php
$request = [
	'url' => 'http://somehost.net/somewhere',
	'params' => [

		'id'     => '12350ME1D',
		'lang'   => 'en-us',
		'format' => 'rss_200'
	],
	'timeout' => 10
];
```

### Headers

```php
$response = HttpClient::get([
	'url' => 'http://somehost.net/somewhere',
	'headers' => ['Connection: close', 'Authorization: some-secret-here']
]);

// The full headers payload
$response->headers();
```

### Basic Auth

```php
$response = HttpClient::get([
	'url' => 'http://somehost.net/somewhere',
	'auth' => [
		'username' => 'user',
		'password' => 'pass'
	],
	'params' => [
		'var1'     => 'value1',
		'var2'   => 'value2'
	]
]);
```

### Digest Auth

```php
$response = HttpClient::get([
	'url' => 'http://some.where.url',
	'digest' => [
		'username' => 'user',
		'password' => 'pass'
	],
	'params' => [
		'var1'     => 'value1',
		'var2'   => 'value2'
	]
]);
```

### Enforce HTTP Version

```php
HttpClient::get(['version' => 1.1, 'url' => 'http://some.url']);
```

### Raw Content

```php
HttpClient::post(['url' => 'http://to.send.to', 'content' => 'Whatever content here may go!']);
```

#### Custom Query String

The content passed in the `content` key will be concatenated to the *URL* followed by a *?*

```php
HttpClient::get(['url' => 'http://my.url', 'content' => 'a=b&c=d']);
```

> It is pretty much the same process with different HTTP Verbs. Supports ``` GET, POST, PUT, DELETE, PATCH, OPTIONS, HEAD ```

## Fault Tolerance

Fault tolerance allows the request to be re-issued when it fails (i.e. timeout).
This is useful in cases such as Microservices: When a service is down and is being called by another service,
with fault tolerance the request will be re-issued in the hopes of the destination service being up again.

Issue a fault-tolerant request by setting the `tolerant` flag to `true` in the request. Also, specify
the time it should wait until it tries again with `timeUntilNextTry` (in seconds) and the number of tries
before it is considered a failure with `triesUntilFailure` (in seconds).

```php
$request = [
	'url' => 'http://somehost.net/somewhere',
	'params' => [

		'id'     => '12350ME1D',
		'lang'   => 'en-us',
		'format' => 'rss_200'
	],
	'timeout' => 10
	'tolerant' => true,
	'timeUntilNextTry' => 1,
	'triesUntilFailure' => 3
];
```

In case of timeout occurance, a `HttpClientRequestFailedException` will be thrown.

> **IMPORTANT! Notice**: In order to make use of fault tolerance option, you must specify the `timeout` parameter too.
