<?php

namespace Vinelab\Http\Tests\Server;

if (isset($_SERVER['HTTP_CONTENT_TYPE']) and $_SERVER['HTTP_CONTENT_TYPE'] === 'application/json') {
    $result = json_decode(file_get_contents('php://input'), true);
    $result['json_header'] = true;

    return print json_encode($result);
}
