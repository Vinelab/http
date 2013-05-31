<?php namespace Vinelab\Http\Tests\Server;

if (isset($_SERVER['HTTP_CONTENT_TYPE']) and $_SERVER['HTTP_CONTENT_TYPE'] === 'application/json')
{
	$result = $_POST;
	$result['json_header'] = true;
	print json_encode($result);
}