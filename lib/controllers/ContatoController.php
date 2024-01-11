<?php 
namespace lib\controllers;

/*
    autor: benhur (benhur.azevedo@hotmail.com)
    utilidade: controller que fornece acesso ao 
    model de contato
*/

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ContatoController
{
    private $dbConn;
    public function __construct() 
    {
        $this->dbConn = \lib\services\DbService::DbConnFactory();
    }
    public function criarContato(Request $request, Response $response) : Response
    {
        $parsedBody = $request->getParsedBody();

        try 
        {
            if(!$stmt = $this->dbConn->prepare(
                "insert into contato (nome, telefone, id_usuario) values (:nome, :telefone, :id_usuario)"
            ))
                throw new \Exception("Falha na query!");

            if(!$stmt->execute([
                "nome" => $parsedBody["nome"],
                "telefone" => $parsedBody["telefone"],
                "id_usuario" => $request->getAttribute("id_usuario")
            ]))
                throw new \Exception("Falha na query!");
        }
        catch(\Exception $e)
        {
            return $response->withStatus(500);
        }

        return $response->withStatus(201);
    }
    public function atualizarContato(Request $request, Response $response, array $args) : Response
    {
        $parsedBody = $request->getParsedBody();

        try 
        {
            if(!$stmt = $this->dbConn->prepare(
                "select * from contato where id = :id"
            ))
                throw new \Exception("Falha na query!");

            if(!$stmt->execute([
                "id" => $args['id']
            ]))
                throw new \Exception("Falha na query!");
            
            $contato = $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        catch(\Exception $e)
        {
            return $response->withStatus(500);
        }
        
        if(!$contato)
        {
            return $response->withStatus(404);
        }
        
        $usuarioId = $request->getAttribute("id_usuario");
        if($contato['id_usuario'] != $usuarioId)
            return $response->withStatus(403);

        try 
        {
            if(!$stmt = $this->dbConn->prepare(
                "update contato set nome = :nome, telefone = :telefone where id = :id"
            ))
                throw new \Exception("Falha na query!");

            if(!$stmt->execute([
                "nome" => $parsedBody["nome"],
                "telefone" => $parsedBody["telefone"],
                "id" => $args['id']
            ]))
                throw new \Exception("Falha na query!");
        }
        catch(\Exception $e)
        {
            return $response->withStatus(500);
        }

        return $response->withStatus(200);
    }
    public function apagarContato(Request $request, Response $response, array $args) : Response
    {
        try 
        {
            if(!$stmt = $this->dbConn->prepare(
                "select * from contato where id = :id"
            ))
                throw new \Exception("Falha na query!");

            if(!$stmt->execute([
                "id" => $args['id']
            ]))
                throw new \Exception("Falha na query!");
            
            $contato = $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        catch(\Exception $e)
        {
            return $response->withStatus(500);
        }
        
        if(!$contato)
        {
            return $response->withStatus(404);
        }
        
        $usuarioId = $request->getAttribute("id_usuario");
        if($contato['id_usuario'] != $usuarioId)
            return $response->withStatus(403);
        
        try 
        {
            if(!$stmt = $this->dbConn->prepare(
                "delete from contato where id = :id"
            ))
                throw new \Exception("Falha na query!");

            if(!$stmt->execute([
                "id" => $args['id']
            ]))
                throw new \Exception("Falha na query!");
        }
        catch(\Exception $e)
        {
            return $response->withStatus(500);
        }
        
        return $response->withStatus(204);
    }
    public function getContato(Request $request, Response $response, array $args) : Response
    {
        try 
        {
            if(!$stmt = $this->dbConn->prepare(
                "select * from contato where id = :id"
            ))
                throw new \Exception("Falha na query!");

            if(!$stmt->execute([
                "id" => $args['id']
            ]))
                throw new \Exception("Falha na query!");
            
            $contato = $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        catch(\Exception $e)
        {
            return $response->withStatus(500);
        }
        
        if(!$contato)
        {
            return $response->withStatus(404);
        }
        
        $usuarioId = $request->getAttribute("id_usuario");
        if($contato['id_usuario'] != $usuarioId)
            return $response->withStatus(403);
        
        $response->getBody()->write(json_encode($contato, JSON_NUMERIC_CHECK));
        $response = $response->withHeader('content-type','application/json');
        return $response->withStatus(200);
    }
    public function listContatos(Request $request, Response $response, array $args) : Response
    {
        try 
        {
            if(!$stmt = $this->dbConn->prepare(
                #"select * from contato where id_usuario = :id_usuario"
                "select 
                    usuario.nome as nome_usuario, 
                    contato.id, 
                    contato.id_usuario, 
                    contato.nome, 
                    contato.telefone 
                    from 
                        contato RIGHT JOIN usuario 
                        on usuario.id = contato.id_usuario 
                    WHERE 
                        usuario.id = :id_usuario"
            ))
                throw new \Exception("Falha na query!");

            if(!$stmt->execute([
                "id_usuario" => $request->getAttribute("id_usuario")
            ]))
                throw new \Exception("Falha na query!");
            
            $resultados = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $resultado = [];
            $resultado['nome_usuario'] = null;
            $resultado['contatos'] = [];
            if(count($resultados) > 0)
            {
                for($cont = 0; $cont < count($resultados); $cont++)
                {
                    if($cont == 0)
                    {
                        $resultado['nome_usuario'] = $resultados[$cont]['nome_usuario'];
                    }
                    if($resultados[$cont]['id'] != null)
                    {
                        $temp = $resultados[$cont];
                        unset($temp['nome_usuario']);
                        array_push($resultado['contatos'], $temp);
                    }
                }
            }
        }
        catch(\Exception $e)
        {
            return $response->withStatus(500);
        }

        $response->getBody()->write(json_encode($resultado, JSON_NUMERIC_CHECK));
        $response = $response->withHeader('content-type','application/json');
        return $response->withStatus(200);
    }
    public function listContatosPDF(Request $request, Response $response, array $args) : Response
    {
        try 
        {
            if(!$stmt = $this->dbConn->prepare(
                "select * from usuario where id = :id_usuario"
            ))
                throw new \Exception("Falha na query!");

            if(!$stmt->execute([
                "id_usuario" => $request->getAttribute("id_usuario")
            ]))
                throw new \Exception("Falha na query!");
            
            $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

            if(!$stmt = $this->dbConn->prepare(
                "select * from contato where id_usuario = :id_usuario"
            ))
                throw new \Exception("Falha na query!");

            if(!$stmt->execute([
                "id" => $request->getAttribute("id_usuario")
            ]))
                throw new \Exception("Falha na query!");
            
            $contato = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        catch(\Exception $e)
        {
            return $response->withStatus(500);
        }
        
        $data = $usuario;
        $data['contatos'] = $contato;
        $pdf = \lib\services\PDFService::geraListaContatosPDF($data);
        $newResponse = $response->withHeader('Content-type', 'application/pdf');
        $newResponse->getBody()->write($pdf->Output());
        return $newResponse;
    }
}
