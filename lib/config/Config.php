<?php
namespace lib\config;
class Config 
{
    public static function getConfig() : array
    {
        // gera a configuração com base no arquivo .env
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();

        $config = [ 'db' => [], 'ambiente_app' => '', 'JWT' => []];

        $config['db']['SGDB'] = $_ENV['SGDB'];
        switch($_ENV['SGDB'])
        {
            case 'SQLITE':
                $config['db']['DSN'] = "sqlite:" . $_ENV['DSN'];
                break;
            case 'POSTGRESQL':
                break;
        }
        $config['JWT']['JWT_KEY'] = $_ENV['JWT_KEY'];
        $config['JWT']['iss'] = $_ENV['ISS'];
        $config['JWT']['aud'] = $_ENV['AUD'];
        $config['ambiente_app'] = $_ENV['AMBIENTE'];
        $config['base_path'] = $_ENV['BASE_PATH'];

        return $config;
    }
}