<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Controller\Controller;
use App\Models\Tutor;

/**
 * Controller para gerenciamento, cadastro, busca e edicao de tutores.
 */
final class TutorController extends Controller {
    public function listar(): void {
        $termo = trim((string)($_GET['q'] ?? ''));
        $tutores = Tutor::listar($termo !== '' ? $termo : null);
        $this->renderizar('tutores/listar', [
            'titulo'  => 'Gerenciamento de Tutores',
            'tutores' => $tutores,
            'busca'   => $termo
        ]);
    }

    public function formulario(): void {
        $this->renderizar('tutores/criar', [
            'titulo'    => 'Novo Tutor',
            'csrfToken' => $this->obterCsrfToken()
        ]);
    }

    public function salvar(): void {
        if (!$this->validarCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->redirecionar('/tutores');
        }
        $nome = trim($_POST['nome'] ?? '');
        $cpf  = trim($_POST['cpf'] ?? '');
        $tel  = trim($_POST['telefone'] ?? '');
        $mail = trim($_POST['email'] ?? '');
        if ($nome !== '' && preg_match('/^\d{11}$/', preg_replace('/\D+/', '', $cpf)) && $tel !== '' && filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            Tutor::salvar($nome, $cpf, $tel, $mail);
            $this->redirecionar('/tutores');
        }
        $this->renderizar('tutores/criar', [
            'titulo'    => 'Novo Tutor',
            'erro'      => 'Informe nome, CPF válido (11 dígitos), telefone e e-mail válido.',
            'csrfToken' => $this->obterCsrfToken()
        ]);
    }

    public function formularioEdicao(): void {
        $id = (int)($_GET['id'] ?? 0);
        $tutor = Tutor::buscarPorId($id);
        if (!$tutor) {
            $this->redirecionar('/tutores');
        }
        $this->renderizar('tutores/editar', [
            'titulo'    => 'Editar Dados do Tutor',
            'tutor'     => $tutor,
            'csrfToken' => $this->obterCsrfToken()
        ]);
    }

    public function atualizar(): void {
        if (!$this->validarCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->redirecionar('/tutores');
        }
        $id   = (int)($_POST['id'] ?? 0);
        $nome = trim($_POST['nome'] ?? '');
        $cpf  = trim($_POST['cpf'] ?? '');
        $tel  = trim($_POST['telefone'] ?? '');
        $mail = trim($_POST['email'] ?? '');
        if ($id > 0 && $nome !== '' && preg_match('/^\d{11}$/', preg_replace('/\D+/', '', $cpf)) && $tel !== '' && filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            Tutor::atualizar($id, $nome, $cpf, $tel, $mail);
            $this->redirecionar('/tutores');
        }
        $tutor = Tutor::buscarPorId($id);
        if (!$tutor) {
            $this->redirecionar('/tutores');
        }
        $this->renderizar('tutores/editar', [
            'titulo' => 'Editar Dados do Tutor', 'tutor' => $tutor,
            'erro' => 'Confira nome, CPF, telefone e e-mail.', 'csrfToken' => $this->obterCsrfToken()
        ]);
    }
}
