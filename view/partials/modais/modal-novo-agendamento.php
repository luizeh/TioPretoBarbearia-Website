<?php /** Modal de agendamento do cliente. Requer $servicos no escopo. */ ?>
<div class="modal-overlay" id="modal-novo-agendamento">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title"><i class="fa-solid fa-calendar-plus"></i> Agendamento</h2>
            <button class="modal-close" data-close="modal-novo-agendamento"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <form class="modal-form">
                <input type="hidden" name="data">
                <input type="hidden" name="hora_inicio">
                <div class="modal-field">
                    <label class="modal-label">Horário escolhido</label>
                    <p class="modal-input modal-selected-time__value" data-agendamento-horario></p>
                </div>
                <div class="modal-field">
                    <label class="modal-label">Serviços</label>
                    <div class="agendamento-servicos" data-agendamento-servicos>
                        <?php foreach ($servicos ?? [] as $s): ?>
                            <label class="agendamento-servico-opcao">
                                <input type="checkbox" name="servicos_ids" value="<?= (int) $s['id'] ?>" data-preco="<?= htmlspecialchars((string) $s['preco']) ?>" data-duracao="<?= (int) $s['tempo_estimado'] ?>">
                                <span><?= htmlspecialchars($s['nome']) ?></span>
                                <small>R$ <?= number_format($s['preco'], 2, ',', '.') ?> · <?= (int) $s['tempo_estimado'] ?> min</small>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="modal-field modal-summary" data-agendamento-resumo hidden>
                    <p class="modal-label modal-label--compact">Resumo</p>
                    <small class="modal-summary__details"><span data-agendamento-lista></span><br>Total: <strong data-agendamento-total></strong> · Duração: <strong data-agendamento-duracao></strong> · Término: <strong data-agendamento-fim></strong></small>
                </div>
                <div class="modal-field">
                    <label class="modal-label">Observações <small>(opcional)</small></label>
                    <textarea class="modal-textarea" name="observacoes" rows="3" maxlength="1000" placeholder="Alguma preferência ou informação importante?"></textarea>
                </div>
            </form>
        </div>
    </div>
</div>
