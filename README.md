![build status](https://travis-ci.org/Vinelab/http.png?branch=master "build status")

## Vinelab Http Library for Laravel 4

Handles server-to-server connections.

## Installation
Refer to [Vinelab/http on packagist.org](https://packagist.org/search/?q=vinelab%2Fhttp) for composer installation instructions.

Edit **app.php** and add ```'Vinelab\Http\HttpServiceProvider',``` to the ```'providers'``` array.

It will automatically alias itself as **HttClient** so no need to aslias it in your **app.php** unless you would like to customize it. In that case edit your **'aliases'** in **app.php** adding ``` 'MyHttp'	  => 'Vinelab\Http\Facades\Client',```

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
- Improve tests to include testing for all HTTP Verbs
- Improve support for XML requests (left till needed)