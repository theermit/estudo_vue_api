<?php
namespace lib\middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class LoginMiddleware
{
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $ok = true;

        $parsedBody = $request->getParsedBody();

        if(!array_key_exists("email", $parsedBody))
            $ok = false;

        if(array_key_exists("email", $parsedBody))
        {
            if($parsedBody["email"] == "")
                $ok = false;
        }

        if(!array_key_exists("senha", $parsedBody))
            $ok = false;

        if(array_key_exists("senha", $parsedBody))
        {
            if($parsedBody["senha"] == "")
                $ok = false;
        }
            
        if(!$ok)
        {
            return $response->withStatus(400);
        }

        $response = $next($request, $response);
        return $response;
    }
}
