<?php
namespace lib\middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class JWTMiddleware
{
    public function __invoke(Request $request, Response $response, callable $next)
    {
        if(!$request->hasHeader("x-access-token"))
        {
            return $response->withStatus(403);
        }

        $token = $request->getHeader("x-access-token")[0];

        $token_decodificado = \lib\services\JWTService::decodificar($token);

        if(!$token_decodificado)
        {
            return $response->withStatus(403);
        }

        $request = $request->withAttribute("id_usuario", $token_decodificado->id);

        $response = $next($request, $response);
        return $response;
    }
}
