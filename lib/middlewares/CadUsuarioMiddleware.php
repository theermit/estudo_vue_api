<?php
namespace lib\middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class CadUsuarioMiddleware
{
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $ok = true;

        $parsedBody = $request->getParsedBody();
        
        if(!array_key_exists("nome", $parsedBody))
            $ok = false;

        if(!array_key_exists("email", $parsedBody))
            $ok = false;

        if(!array_key_exists("senha", $parsedBody))
            $ok = false;

        if(!array_key_exists("confirmacao_senha", $parsedBody))
            $ok = false;

        if(array_key_exists("senha", $parsedBody) && array_key_exists("confirmacao_senha", $parsedBody))
        {
            if($parsedBody["senha"] != $parsedBody["confirmacao_senha"])
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