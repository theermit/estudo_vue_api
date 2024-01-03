<?php 
namespace lib\routes;

/*
    autor: benhur (benhur.azevedo@hotmail.com)
    utilidade: seta as rotas da aplicação slim
*/
use lib\controllers\UsuarioController;
use lib\controllers\ContatoController;
use lib\middlewares\ContatoMiddleware;
class Route 
{
    public static function setRoutes(\Slim\App $app): void
    {
        self::setUserRoutes($app);
        self::setContatoRoutes($app);
        return;
    }
    private static function setUserRoutes(\Slim\App $app): void
    {
        #rota para consultar se o email (que é campo unique) já existe
        $app->post("/consultaremail", UsuarioController::class . ":consultarEmail");

        #rota para cadastrar o usuário
        $app->post("/cadastrarusuario", UsuarioController::class .":cadastrarUsuario")
            ->add(new \lib\middlewares\CadUsuarioMiddleware())
            ->add(new \lib\middlewares\EmailMiddleware());

        #rota para logar
        $app->post("/login", UsuarioController::class .":logar")
            ->add(new \lib\middlewares\LoginMiddleware());
    }
    private static function setContatoRoutes(\Slim\App $app): void
    {
        $app->group("", function (\Slim\App $app) 
        { 
            $app->post("/contato", ContatoController::class .":criarContato")
                ->add(new ContatoMiddleware());

            $app->put("/contato/{id}", ContatoController::class . ":atualizarContato")
                ->add(new ContatoMiddleware());

            $app->delete("/contato/{id}", ContatoController::class . ":apagarContato");

            $app->get("/contato/{id}", ContatoController::class . ":getContato");

            $app->get("/contato", ContatoController::class . ":listContatos");
        })->add(new \lib\middlewares\JWTMiddleware());
    }
}