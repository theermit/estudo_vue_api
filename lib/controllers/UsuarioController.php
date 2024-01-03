<?php 
namespace lib\controllers;

/*
    autor: benhur (benhur.azevedo@hotmail.com)
    utilidade: controller que fornece acesso ao 
    model de usuario alem de oferecer acesso ao
    login do app
*/

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class UsuarioController
{
    private $dbConn;
    public function __construct() 
    {
        $this->dbConn = \lib\services\DbService::DbConnFactory();
    }
    public function consultarEmail(Request $request, Response $response) : Response
    {
        $parsedBody = $request->getParsedBody();
        
        try 
        {
            if(!$stmt = $this->dbConn->prepare(
                "select id from usuario where email = :email"
            ))
                throw new \Exception("Falha na query");

            if(!$stmt->execute(["email" => $parsedBody["email"]]))
                throw new \Exception("Falha na query");

            $usuarios = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        catch(\Exception $e)
        {
            return $response->withStatus(500);
        }
        
        $emailJahCadastrado = count($usuarios) > 0 ? "true" : "false";
        
        #$response = $response->withJson(["email_jah_cadastrado" => mb_convert_encoding($emailJahCadastrado, 'UTF-8', mb_list_encodings())]);
        $response->getBody()->write(json_encode(["email_jah_cadastrado" => mb_convert_encoding($emailJahCadastrado, 'UTF-8', mb_list_encodings())], JSON_NUMERIC_CHECK));
        return $response->withStatus(200);
    }
    public function cadastrarUsuario(Request $request, Response $response) : Response
    {
        $parsedBody = $request->getParsedBody();

        try 
        {
            if(!$stmt = $this->dbConn->prepare(
                "insert into usuario (nome, email, senha) values (:nome, :email, :senha)"
            ))
                throw new \Exception("Falha na query!");

            if(!$stmt->execute([
                "nome" => $parsedBody["nome"],
                "email" => $parsedBody["email"],
                "senha" => md5($parsedBody["senha"])
            ]))
                throw new \Exception("Falha na query!");
        }
        catch(\Exception $e)
        {
            return $response->withStatus(500);
        }

        return $response->withStatus(201);
    }
    public function logar(Request $request, Response $response) : Response
    {
        $parsedBody = $request->getParsedBody();
        
        try 
        {
            if(!$stmt = $this->dbConn->prepare(
                "select id from usuario where email = :email and senha = :senha"
            ))
                throw new \Exception("Falha na query");

            if(!$stmt->execute([
                "email" => $parsedBody["email"],
                "senha"=> md5($parsedBody["senha"])
                ]))
                throw new \Exception("Falha na query");

            $usuarios = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        catch(\Exception $e)
        {
            return $response->withStatus(500);
        }

        if(count($usuarios) == 0)
            return $response->withStatus(403);

        $id = $usuarios[0]['id'];
        
        $jwt = \lib\services\JWTService::codificar(["id"=> $id]);

        $response->getBody()->write(json_encode(["jwt_hash" => mb_convert_encoding($jwt, 'UTF-8', mb_list_encodings())], JSON_NUMERIC_CHECK));
        
        return $response->withStatus(200);
    }    
}