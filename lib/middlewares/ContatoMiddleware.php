<?php
namespace lib\middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ContatoMiddleware
{
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $ok = true;

        $parsedBody = $request->getParsedBody();

        if(!array_key_exists("nome", $parsedBody))
            $ok = false;

        if(array_key_exists("nome", $parsedBody))
        {
            if($parsedBody["nome"] == "")
                $ok = false;
        }

        if(!array_key_exists("telefone", $parsedBody))
            $ok = false;

        if(array_key_exists("telefone", $parsedBody))
        {
            if($parsedBody["telefone"] == "")
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