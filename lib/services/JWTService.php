<?php
namespace lib\services;
class JWTService 
{
    public static function codificar(array $dados_payload) : string
    {
        // gera chave jwt usando a JWT_KEY e o payload
        $config = \lib\config\Config::getConfig();

        $key = $config['JWT']['JWT_KEY'];

        $payload = [
            'iss' => $config['JWT']['iss'],
            'aud' => $config['JWT']['aud'],
            'iat' => time(),
            'exp' => time() + 3600,
        ];

        $jwt = \Firebase\JWT\JWT::encode(array_merge($payload, $dados_payload), $key, 'HS256');

        return $jwt;
    }
    public static function decodificar(string $jwt) 
    {
        // decodifica JWT usando a JWT_KEY para recuperar o payload
        $config = \lib\config\Config::getConfig();

        $key = $config['JWT']['JWT_KEY'];
        try 
        {
            return \Firebase\JWT\JWT::decode($jwt, new \Firebase\JWT\Key($key, 'HS256'));
        }
        catch(\Firebase\JWT\ExpiredException $e)
        {
            return false;
        }
    }
}