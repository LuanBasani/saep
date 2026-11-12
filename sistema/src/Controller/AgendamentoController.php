<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Controller\Controller;
use App\Models\Agendamento;
use App\Models\Tutor;
use App\Models\Pet;

/**
 * Controller para agendamentos ordenados por ano e atualizacao de datas.
 */
final class AgendamentoController extends Controller {
    public function listar(): void {
        $agendamentos = Agendamento::listarOrdenadosPorData();
        $this->renderizar('agendamentos/listar', [
            'titulo'       => 'Agendamentos da Clínica',
            'agendamentos' => $agendamentos
        ]);
    }

    public function formulario(): void {
        $pets = Pet::listarTodos();
        $this->renderizar('agendamentos/criar', [
            'titulo'    => 'Novo Agendamento',
            'pets'      => $pets,
            'dataMinima' => $this->dataMinima(),
            'csrfToken' => $this->obterCsrfToken()
        ]);
    }

    public function salvar(): void {
        if (!$this->validarCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->redirecionar('/agendamentos');
        }
        $petId  = (int)($_POST['pet_id'] ?? 0);
        $dt     = trim($_POST['data_agendamento'] ?? '');
        $hr     = trim($_POST['hora_agendamento'] ?? '');
        $serv   = trim($_POST['servico'] ?? '');
        $usrId  = (int)($_SESSION['usuario_id'] ?? 1);

        $pet = array_values(array_filter(Pet::listarTodos(), static fn(array $item): bool => (int)$item['id'] === $petId))[0] ?? null;
        if ($pet !== null && $this->validarDataDeAgendamento($dt) && preg_match('/^\d{2}:\d{2}$/', $hr) && $serv !== '' && $usrId > 0) {
            Agendamento::salvar((int)$pet['tutor_id'], $petId, $usrId, $dt, $hr, $serv);
            $this->redirecionar('/agendamentos');
        }
        $this->renderizar('agendamentos/criar', [
            'titulo' => 'Novo Agendamento', 'pets' => Pet::listarTodos(),
            'erro' => 'Selecione um pet, informe os dados e escolha uma data de hoje em diante.',
            'dataMinima' => $this->dataMinima(),
            'csrfToken' => $this->obterCsrfToken()
        ]);
    }

    public function formularioEditarData(): void {
        $id = (int)($_GET['id'] ?? 0);
        $agendamento = Agendamento::buscarPorId($id);
        if (!$agendamento) {
            $this->redirecionar('/agendamentos');
        }
        $this->renderizar('agendamentos/editar-data', [
            'titulo'      => 'Atualizar Data do Agendamento #' . $id,
            'agendamento' => $agendamento,
            'dataMinima'  => $this->dataMinima(),
            'csrfToken'   => $this->obterCsrfToken()
        ]);
    }

    public function atualizarData(): void {
        if (!$this->validarCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->redirecionar('/agendamentos');
        }
        $id   = (int)($_POST['id'] ?? 0);
        $novaData = trim($_POST['data_agendamento'] ?? '');
        $novaHora = trim($_POST['hora_agendamento'] ?? '');

        if ($id > 0 && $this->validarDataDeAgendamento($novaData) && ($novaHora === '' || preg_match('/^\d{2}:\d{2}$/', $novaHora))) {
            Agendamento::atualizarData($id, $novaData, $novaHora !== '' ? $novaHora : null);
            $this->redirecionar('/agendamentos');
        }
        $agendamento = Agendamento::buscarPorId($id);
        if (!$agendamento) {
            $this->redirecionar('/agendamentos');
        }
        $this->renderizar('agendamentos/editar-data', [
            'titulo' => 'Atualizar Data do Agendamento #' . $id,
            'agendamento' => $agendamento,
            'erro' => 'A nova data deve ser hoje ou posterior. Confira também o horário.',
            'dataMinima' => $this->dataMinima(),
            'csrfToken' => $this->obterCsrfToken()
        ]);
    }

    private function dataMinima(): string {
        return (new \DateTimeImmutable('today', new \DateTimeZone('America/Sao_Paulo')))->format('Y-m-d');
    }
}
