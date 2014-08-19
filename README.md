![build status](https://travis-ci.org/Vinelab/http.png?branch=master "build status")

![Codeshipt Build Status](https://www.codeship.io/projects/a3d14930-c970-0131-6192-227098611d1d/status)

# http://Client

A smart and simple HTTP client for JSON and XML.

## Installation

### Composer

- `"vinelab/http": "dev-master"` or refer to [vinelab/http on packagist.org](https://packagist.org/packages/vinelab/http) for the latest version installation instructions.

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

> It is pretty much the same process with different HTTP Verbs. Supports ``` GET, POST, PUT, DELETE, PATCH, OPTIONS, HEAD ```

#### TODO
- Improve tests to include testing all the methods of response (like statusCode...)
- Include tests for handling bad data / errors
- Improve tests to include testing for all HTTP Verbs
