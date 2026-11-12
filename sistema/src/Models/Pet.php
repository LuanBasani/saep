<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\ConexaoBanco;
use PDO;

final class Pet
{
    private static function pdo(): PDO
    {
        return ConexaoBanco::obterConexao();
    }

    public static function listarTodos(): array
    {
        $sql = 'SELECT p.id, p.tutor_id, p.nome, p.especie, p.raca, p.data_nascimento,
                       t.nome AS tutor_nome, t.telefone AS tutor_telefone
                FROM pets p JOIN tutores t ON t.id = p.tutor_id
                ORDER BY p.nome ASC';
        return self::pdo()->query($sql)->fetchAll();
    }

    public static function totalPets(): int
    {
        return (int) self::pdo()->query('SELECT COUNT(*) FROM pets')->fetchColumn();
    }

    public static function salvar(int $tutorId, string $nome, string $especie, string $raca, string $dataNascimento): bool
    {
        $sql = 'INSERT INTO pets (tutor_id, nome, especie, raca, data_nascimento)
                VALUES (:tutor_id, :nome, :especie, :raca, :data_nascimento)';
        return self::pdo()->prepare($sql)->execute([
            ':tutor_id' => $tutorId,
            ':nome' => trim($nome),
            ':especie' => trim($especie),
            ':raca' => trim($raca),
            ':data_nascimento' => $dataNascimento,
        ]);
    }
}
