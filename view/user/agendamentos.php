<?php
include_once(__DIR__ . '/../../api/auth/session.php');
$rootPath    = '../../';
$linkBase    = '../';
$activeNav   = 'agendar';
$nomeUsuario = htmlspecialchars($_SESSION['nome'] ?? 'Cliente');
$pageTitle   = 'Meus Agendamentos — Tio Preto Barbearia';
$extraCss    = ['assets/css/components.css', 'assets/css/agenda.css'];
$bodyClass   = 'user-page';
include_once __DIR__ . '/../partials/head_public.php';
?>
<!-- ══════════════ HEADER ══════════════ -->
<?php include_once __DIR__ . '/../partials/header_public.php'; ?>

<!-- ══════════════ BANNER ══════════════ -->
<div class="page-banner">
    <span class="page-banner__eyebrow">✦ Área do Cliente</span>
    <h1 class="page-banner__title">Meus <span>Agendamentos</span></h1>
    <p class="page-banner__desc">Olá, <?= $nomeUsuario ?>! Acompanhe e gerencie os seus horários.</p>
</div>

<!-- ══════════════ CONTEÚDO ══════════════ -->
<div class="user-agenda">

    <!-- Ação: Novo Agendamento -->
    <div class="user-agenda__actions">
        <button class="btn-new-appt" data-modal="modal-novo-agendamento">
            <i class="fa-solid fa-plus"></i> Novo Agendamento
        </button>
    </div>

    <!-- Grade semanal -->
    <div class="agenda-wrap">
        <div class="agenda-nav">
            <button class="agenda-nav__btn"><i class="fa-solid fa-chevron-left"></i></button>
            <span class="agenda-nav__label">Semana de 07 – 12 Jul 2026</span>
            <button class="agenda-nav__btn"><i class="fa-solid fa-chevron-right"></i></button>
        </div>
        <div class="agenda-scroll">
            <div class="agenda-grid">

                <!-- Cabeçalho dos dias -->
                <div class="agenda-corner"></div>
                <div class="agenda-day-head today">Seg <span>07</span></div>
                <div class="agenda-day-head">Ter <span>08</span></div>
                <div class="agenda-day-head">Qua <span>09</span></div>
                <div class="agenda-day-head">Qui <span>10</span></div>
                <div class="agenda-day-head">Sex <span>11</span></div>
                <div class="agenda-day-head">Sáb <span>12</span></div>

                <!-- 08:00 -->
                <div class="agenda-hour">08:00</div>
                <div class="agenda-cell today"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell">
                    <div class="agenda-appt agenda-appt--ocupado" style="--slots: 1">
                        <span class="agenda-appt__service">Ocupado</span>
                    </div>
                </div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>

                <!-- 08:30 -->
                <div class="agenda-hour agenda-hour--half">08:30</div>
                <div class="agenda-cell today"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>

                <!-- 09:00 -->
                <div class="agenda-hour">09:00</div>
                <div class="agenda-cell today">
                    <div class="agenda-appt agenda-appt--ocupado" style="--slots: 1">
                        <span class="agenda-appt__service">Ocupado</span>
                    </div>
                </div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell">
                    <div class="agenda-appt agenda-appt--ocupado" style="--slots: 2">
                        <span class="agenda-appt__service">Ocupado</span>
                    </div>
                </div>

                <!-- 09:30 -->
                <div class="agenda-hour agenda-hour--half">09:30</div>
                <div class="agenda-cell today"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell agenda-cell--cont-ocupado"></div>

                <!-- 10:00 — Corte + Barba (Qua 09, confirmado, 60 min) -->
                <div class="agenda-hour">10:00</div>
                <div class="agenda-cell today"></div>
                <div class="agenda-cell">
                    <div class="agenda-appt agenda-appt--ocupado" style="--slots: 1.5">
                        <span class="agenda-appt__service">Ocupado</span>
                    </div>
                </div>
                <div class="agenda-cell">
                    <div class="agenda-appt agenda-appt--confirmed" style="--slots: 2">
                        <span class="agenda-appt__service">Corte + Barba · 60 min</span>
                        <div class="agenda-appt__actions">
                            <button class="agenda-appt__cancel-btn" title="Cancelar agendamento">Cancelar</button>
                        </div>
                    </div>
                </div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>

                <!-- 10:30 -->
                <div class="agenda-hour agenda-hour--half">10:30</div>
                <div class="agenda-cell today"></div>
                <div class="agenda-cell agenda-cell--cont-ocupado"></div>
                <div class="agenda-cell agenda-cell--cont-confirmed"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>

                <!-- 11:00 -->
                <div class="agenda-hour">11:00</div>
                <div class="agenda-cell today"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell">
                    <div class="agenda-appt agenda-appt--ocupado" style="--slots: 2">
                        <span class="agenda-appt__service">Ocupado</span>
                    </div>
                </div>
                <div class="agenda-cell"></div>

                <!-- 11:30 -->
                <div class="agenda-hour agenda-hour--half">11:30</div>
                <div class="agenda-cell today"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell agenda-cell--cont-ocupado"></div>
                <div class="agenda-cell"></div>

                <!-- 12:00 -->
                <div class="agenda-hour">12:00</div>
                <div class="agenda-cell today"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>

                <!-- 12:30 -->
                <div class="agenda-hour agenda-hour--half">12:30</div>
                <div class="agenda-cell today"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>

                <!-- 13:00 -->
                <div class="agenda-hour">13:00</div>
                <div class="agenda-cell today"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>

                <!-- 13:30 -->
                <div class="agenda-hour agenda-hour--half">13:30</div>
                <div class="agenda-cell today"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>

                <!-- 14:00 — Corte Social (Sáb 12, pendente, 30 min) -->
                <div class="agenda-hour">14:00</div>
                <div class="agenda-cell today">
                    <div class="agenda-appt agenda-appt--ocupado" style="--slots: 2">
                        <span class="agenda-appt__service">Ocupado</span>
                    </div>
                </div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell">
                    <div class="agenda-appt agenda-appt--pending" style="--slots: 1">
                        <span class="agenda-appt__service">Corte Social · 30 min</span>
                        <div class="agenda-appt__actions">
                            <button class="agenda-appt__cancel-btn" title="Cancelar agendamento">Cancelar</button>
                        </div>
                    </div>
                </div>

                <!-- 14:30 -->
                <div class="agenda-hour agenda-hour--half">14:30</div>
                <div class="agenda-cell today agenda-cell--cont-ocupado"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>

                <!-- 15:00 -->
                <div class="agenda-hour">15:00</div>
                <div class="agenda-cell today"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>

                <!-- 15:30 -->
                <div class="agenda-hour agenda-hour--half">15:30</div>
                <div class="agenda-cell today"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>

                <!-- 16:00 -->
                <div class="agenda-hour">16:00</div>
                <div class="agenda-cell today"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell">
                    <div class="agenda-appt agenda-appt--ocupado" style="--slots: 1.5">
                        <span class="agenda-appt__service">Ocupado</span>
                    </div>
                </div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>

                <!-- 16:30 -->
                <div class="agenda-hour agenda-hour--half">16:30</div>
                <div class="agenda-cell today"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell agenda-cell--cont-ocupado"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>

                <!-- 17:00 -->
                <div class="agenda-hour">17:00</div>
                <div class="agenda-cell today"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell">
                    <div class="agenda-appt agenda-appt--ocupado" style="--slots: 1">
                        <span class="agenda-appt__service">Ocupado</span>
                    </div>
                </div>
                <div class="agenda-cell"></div>

                <!-- 17:30 -->
                <div class="agenda-hour agenda-hour--half">17:30</div>
                <div class="agenda-cell today"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>

                <!-- 18:00 -->
                <div class="agenda-hour">18:00</div>
                <div class="agenda-cell today"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>

                <!-- 18:30 -->
                <div class="agenda-hour agenda-hour--half">18:30</div>
                <div class="agenda-cell today"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>

                <!-- 19:00 -->
                <div class="agenda-hour">19:00</div>
                <div class="agenda-cell today"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>

                <!-- 19:30 -->
                <div class="agenda-hour agenda-hour--half">19:30</div>
                <div class="agenda-cell today"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>
                <div class="agenda-cell"></div>

            </div><!-- /.agenda-grid -->
        </div><!-- /.agenda-scroll -->
    </div><!-- /.agenda-wrap -->

</div><!-- /.user-agenda -->

<?php include_once __DIR__ . '/../partials/footer.php'; ?>

<!-- Modal: Novo Agendamento -->
<div class="modal-overlay" id="modal-novo-agendamento">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title"><i class="fa-solid fa-calendar-plus"></i> Novo Agendamento</h2>
            <button class="modal-close" data-close="modal-novo-agendamento"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <form class="modal-form">
                <div class="modal-field">
                    <label class="modal-label">Serviço</label>
                    <select class="modal-select">
                        <option value="">Selecione um serviço</option>
                        <option>Corte Social — R$ 35,00 (30 min)</option>
                        <option>Corte + Barba — R$ 55,00 (60 min)</option>
                        <option>Barba Degradê — R$ 40,00 (45 min)</option>
                        <option>Hidratação — R$ 45,00 (40 min)</option>
                        <option>Sobrancelha — R$ 20,00 (15 min)</option>
                    </select>
                </div>
                <div class="modal-row">
                    <div class="modal-field">
                        <label class="modal-label">Data</label>
                        <input class="modal-input" type="date" />
                    </div>
                    <div class="modal-field">
                        <label class="modal-label">Horário</label>
                        <input class="modal-input" type="time" />
                    </div>
                </div>
                <div class="modal-field">
                    <label class="modal-label">Observação (opcional)</label>
                    <input class="modal-input" type="text" placeholder="Ex: preferência de barbeiro" />
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn-modal-secondary" data-close="modal-novo-agendamento">Cancelar</button>
            <button class="btn-modal-primary">Confirmar Agendamento</button>
        </div>
    </div>
</div>

</body>

</html>