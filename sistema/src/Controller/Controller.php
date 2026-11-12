<?php
declare(strict_types=1);

namespace App\Controller;

use RuntimeException;

abstract class Controller {

    protected function renderizar(string $view, array $dados = []): void {
        extract($dados, EXTR_SKIP);
        $arquivoView = dirname(__DIR__) . "/views/{$view}.php";
        if (!file_exists($arquivoView)) {
            throw new RuntimeException("A view nao foi encontrada: {$arquivoView}");
        }
        require_once dirname(__DIR__) . "/views/layout/header.php";
        require $arquivoView;
        require_once dirname(__DIR__) . "/views/layout/footer.php";
    }

    protected function redirecionar(string $rota): void {
        header("Location: {$rota}");
        exit;
    }

    protected function obterCsrfToken(): string {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return (string)$_SESSION['csrf_token'];
    }

    protected function validarData(string $data): bool {
        $valor = \DateTimeImmutable::createFromFormat('!Y-m-d', $data);
        return $valor !== false && $valor->format('Y-m-d') === $data;
    }

    protected function validarDataDeAgendamento(string $data): bool {
        if (!$this->validarData($data)) {
            return false;
        }

        $hoje = new \DateTimeImmutable('today', new \DateTimeZone('America/Sao_Paulo'));
        return $data >= $hoje->format('Y-m-d');
    }

    protected function validarCsrfToken(?string $token): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $sessaoToken = $_SESSION['csrf_token'] ?? '';
        return !empty($token) && hash_equals((string)$sessaoToken, (string)$token);
    }
}
