<?php
declare(strict_types=1);

namespace App\Core;

use PDO;
use RuntimeException;

final class ConexaoBanco
{
    private static ?PDO $instancia = null;

    private function __construct()
    {
    }

    public static function obterConexao(?string $caminhoConfig = null): PDO
    {
        if (self::$instancia === null) {
            $caminhoConfig ??= dirname(__DIR__, 2) . '/config/database.ini';
            $dados = parse_ini_file($caminhoConfig, true, INI_SCANNER_TYPED);
            if ($dados === false || !isset($dados['database'])) {
                throw new RuntimeException('Configuração do banco de dados inválida.');
            }

            $cfg = $dados['database'];
            $dsn = sprintf('%s:host=%s;port=%s;dbname=%s', $cfg['db_driver'], $cfg['db_host'], $cfg['db_port'], $cfg['db_name']);
            self::$instancia = new PDO($dsn, $cfg['db_user'], $cfg['db_pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }

        return self::$instancia;
    }
}
