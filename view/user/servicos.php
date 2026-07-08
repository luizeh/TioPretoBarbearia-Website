<?php
$rootPath  = '../../';
$linkBase  = '../';
$activeNav = 'servicos';
$pageTitle = 'Serviços — Tio Preto Barbearia';
$bodyClass = 'user-page';
include_once __DIR__ . '/../partials/head_public.php';
?>
<!-- ══════════════ HEADER ══════════════ -->
<?php include_once __DIR__ . '/../partials/header_public.php'; ?>

<!-- ══════════════ BANNER ══════════════ -->
<div class="page-banner">
    <span class="page-banner__eyebrow">✦ Tio Preto Barbearia</span>
    <h1 class="page-banner__title">Nossos <span>Serviços</span></h1>
    <p class="page-banner__desc">Escolha o serviço ideal e agende o seu horário com facilidade.</p>
</div>

<!-- ══════════════ SERVIÇOS ══════════════ -->
<section id="servicos">
    <div class="section-header">
        <span class="section-eyebrow">O que oferecemos</span>
        <h2 class="section-title">Para o seu Estilo</h2>
    </div>

    <div class="services-grid">

        <!-- Corte Social -->
        <div class="service-card fade-in">
            <div class="service-icon-wrap">
                <div class="service-icon-inner">
                    <i class="fa-solid fa-scissors"></i>
                </div>
                <span class="service-badge">Cabelo</span>
            </div>
            <div class="service-body">
                <h3 class="service-name">Corte Social</h3>
                <p class="service-desc">
                    Corte clássico com acabamento perfeito. Ideal para quem busca um
                    visual elegante e bem definido, seja para o trabalho ou para um
                    evento especial. Técnica precisa com finalização impecável.
                </p>
                <div class="service-meta">
                    <span class="service-duration">
                        <i class="fa-regular fa-clock"></i> 30 min
                    </span>
                    <span class="service-price">R$ 35,00</span>
                </div>
                <div class="service-cta">
                    <a href="agendamentos.php" class="btn-service-book">
                        <i class="fa-solid fa-calendar-plus"></i> Agendar
                    </a>
                </div>
            </div>
        </div>

        <!-- Corte + Barba -->
        <div class="service-card fade-in">
            <div class="service-icon-wrap">
                <div class="service-icon-inner">
                    <i class="fa-solid fa-user-tie"></i>
                </div>
                <span class="service-badge">Cabelo &amp; Barba</span>
            </div>
            <div class="service-body">
                <h3 class="service-name">Corte + Barba</h3>
                <p class="service-desc">
                    A combinação perfeita: corte de cabelo e modelagem completa da
                    barba em uma única sessão. Saiba com um visual renovado e
                    totalmente alinhado, do cabelo à barba.
                </p>
                <div class="service-meta">
                    <span class="service-duration">
                        <i class="fa-regular fa-clock"></i> 60 min
                    </span>
                    <span class="service-price">R$ 55,00</span>
                </div>
                <div class="service-cta">
                    <a href="agendamentos.php" class="btn-service-book">
                        <i class="fa-solid fa-calendar-plus"></i> Agendar
                    </a>
                </div>
            </div>
        </div>

        <!-- Barba Degradê -->
        <div class="service-card fade-in">
            <div class="service-icon-wrap">
                <div class="service-icon-inner">
                    <i class="fa-solid fa-person-rays"></i>
                </div>
                <span class="service-badge">Barba</span>
            </div>
            <div class="service-body">
                <h3 class="service-name">Barba Degradê</h3>
                <p class="service-desc">
                    Modelagem com efeito degradê nas laterais para um visual moderno
                    e sofisticado. Técnica que proporciona suavidade e transição
                    perfeita entre os comprimentos.
                </p>
                <div class="service-meta">
                    <span class="service-duration">
                        <i class="fa-regular fa-clock"></i> 45 min
                    </span>
                    <span class="service-price">R$ 40,00</span>
                </div>
                <div class="service-cta">
                    <a href="agendamentos.php" class="btn-service-book">
                        <i class="fa-solid fa-calendar-plus"></i> Agendar
                    </a>
                </div>
            </div>
        </div>

        <!-- Hidratação -->
        <div class="service-card fade-in">
            <div class="service-icon-wrap">
                <div class="service-icon-inner">
                    <i class="fa-solid fa-droplet"></i>
                </div>
                <span class="service-badge">Cabelo</span>
            </div>
            <div class="service-body">
                <h3 class="service-name">Hidratação</h3>
                <p class="service-desc">
                    Tratamento profundo de hidratação capilar com produtos premium.
                    Devolve o brilho, a maciez e a saúde dos fios, combatendo o
                    ressecamento e o frizz de forma duradoura.
                </p>
                <div class="service-meta">
                    <span class="service-duration">
                        <i class="fa-regular fa-clock"></i> 40 min
                    </span>
                    <span class="service-price">R$ 45,00</span>
                </div>
                <div class="service-cta">
                    <a href="agendamentos.php" class="btn-service-book">
                        <i class="fa-solid fa-calendar-plus"></i> Agendar
                    </a>
                </div>
            </div>
        </div>

        <!-- Sobrancelha -->
        <div class="service-card fade-in">
            <div class="service-icon-wrap">
                <div class="service-icon-inner">
                    <i class="fa-regular fa-eye"></i>
                </div>
                <span class="service-badge">Design</span>
            </div>
            <div class="service-body">
                <h3 class="service-name">Sobrancelha</h3>
                <p class="service-desc">
                    Design e alinhamento de sobrancelha masculina com técnica
                    precisa. Valoriza o olhar e complementa o visual geral,
                    garantindo harmonia e expressão natural ao rosto.
                </p>
                <div class="service-meta">
                    <span class="service-duration">
                        <i class="fa-regular fa-clock"></i> 15 min
                    </span>
                    <span class="service-price">R$ 20,00</span>
                </div>
                <div class="service-cta">
                    <a href="agendamentos.php" class="btn-service-book">
                        <i class="fa-solid fa-calendar-plus"></i> Agendar
                    </a>
                </div>
            </div>
        </div>

    </div>
</section>

<?php include_once __DIR__ . '/../partials/footer.php'; ?>
</body>

</html>