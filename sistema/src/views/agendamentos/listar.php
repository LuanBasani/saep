<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h2><?= htmlspecialchars($titulo) ?></h2>
            <p style="color:var(--muted); font-size:0.85rem; margin-top:4px;">
                Agendamentos em ordem de data e horário, com informações do pet e do tutor.
            </p>
        </div>
        <a href="/agendamentos/novo" class="btn btn-success">+ Novo Agendamento</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Data / Hora</th>
                <th>Pet</th>
                <th>Tutor</th>
                <th>Serviço</th>
                <th>Status</th>
                <th style="text-align:right;">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($agendamentos)): ?>
                <tr><td colspan="6" style="text-align:center; color:var(--muted);">Nenhum agendamento cadastrado.</td></tr>
            <?php else: ?>
                <?php foreach ($agendamentos as $a): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars(date('d/m/Y', strtotime($a['data_agendamento']))) ?></strong> às <?= htmlspecialchars(substr($a['hora_agendamento'], 0, 5)) ?></td>
                        <td><strong><?= htmlspecialchars($a['pet_nome']) ?></strong><br><small><?= htmlspecialchars($a['pet_especie']) ?> · <?= htmlspecialchars($a['pet_raca']) ?> · Nasc. <?= htmlspecialchars(date('d/m/Y', strtotime($a['pet_data_nascimento']))) ?></small></td>
                        <td><strong><?= htmlspecialchars($a['tutor_nome']) ?></strong><br><small><?= htmlspecialchars($a['tutor_telefone']) ?> · <?= htmlspecialchars($a['tutor_email']) ?></small></td>
                        <td><?= htmlspecialchars($a['servico']) ?></td>
                        <td><?= htmlspecialchars($a['status']) ?></td>
                        <td style="text-align:right;">
                            <a href="/agendamentos/editar-data?id=<?= (int)$a['id'] ?>" class="btn" style="padding:4px 8px; font-size:0.75rem; background:var(--green);">
                                Alterar Data
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
