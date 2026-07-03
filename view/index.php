<!doctype html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="shortcut icon" href="../img/favicon.png" type="image/png" />
  <title>Tio Preto Barbearia</title>
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Barlow:wght@300;400;500;600&family=Barlow+Condensed:wght@600;700&display=swap"
    rel="stylesheet" />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="../css/style.css" />
</head>

<body>
  <!-- ══════════════ HEADER ══════════════ -->
  <header>
    <div class="header-logo">
      <img src="../img/tiopretonb.png" alt="Tio Preto Barbearia" />
      <div class="brand-text">
        <span class="brand-name">Tio Preto</span>
        <span class="brand-sub">Barbearia</span>
      </div>
    </div>
    <nav>
      <a href="#produtos">Produtos</a>
      <a href="#contato">Contato</a>
    </nav>
  </header>

  <!-- ══════════════ HERO ══════════════ -->
  <section class="hero">
    <div class="hero-content">
      <p class="hero-eyebrow">✦ Estilo &amp; Tradição</p>
      <h1 class="hero-title">Arte em cada<br /><span>corte</span>.</h1>
      <p class="hero-desc">
        Na Tio Preto Barbearia, cada detalhe é pensado para realçar o melhor
        de você — do corte à barba, com produtos premium e técnica impecável.
      </p>
    </div>
    <div class="hero-image-side">
      <img src="img/barbearia.png" alt="Nossa Barbearia" />
      <div class="hero-placeholder-img">
        <svg
          width="64"
          height="64"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="1">
          <path
            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z" />
          <path d="M8 14s1.5 2 4 2 4-2 4-2" />
          <line x1="9" y1="9" x2="9.01" y2="9" />
          <line x1="15" y1="9" x2="15.01" y2="9" />
        </svg>
        Adicione uma foto do ambiente
      </div>
      <div class="hero-overlay"></div>
    </div>
  </section>

  <!-- ══════════════ PRODUTOS ══════════════ -->
  <section id="produtos">
    <div class="section-header ">
      <span class="section-eyebrow">Linha de Produtos</span>
      <h2 class="section-title">Para o seu Estilo</h2>
    </div>

    <div class="products-grid">
      <div class="product-card ">
        <div class="product-image-wrap">
          <img
            src="img/shampoo.png"
            alt="Shampoo Pomade Million"
            onerror="this.style.display = 'none'" />
          <div class="product-image-placeholder"></div>
          <span class="product-badge">Cabelo e barba</span>
        </div>
        <div class="product-body">
          <p class="product-brand">Pomade Million</p>
          <h3 class="product-name">Shampoo Million</h3>
          <p class="product-desc">
            Fórmula de pH 5,5 balanceada para limpar profundamente sem agredir
            o couro cabeludo nem os fios da barba. Hidrata, perfuma e confere
            brilho e maciez duradouros — ideal para uso diário tanto no cabelo
            quanto na barba. O shampoo remove o excesso de sebo e resíduos de
            pomada, deixando os fios leves e com um aroma masculino
            sofisticado.
          </p>
          <div class="product-tags">
            <span class="product-tag">300ml</span>
            <span class="product-tag">Hidratação</span>
            <span class="product-tag">Brilho</span>
            <span class="product-tag">Maciez</span>
          </div>
        </div>
      </div>

      <div class="product-card ">
        <div class="product-image-wrap">
          <img
            src="img/efeitoseco.png"
            alt="Pomade Million efeito seco"
            onerror="this.style.display = 'none'" />
          <div class="product-image-placeholder"></div>
          <span class="product-badge">Cabelo</span>
        </div>
        <div class="product-body">
          <p class="product-brand">Pomade Million</p>
          <h3 class="product-name">Pomade Million Efeito seco</h3>
          <p class="product-desc">
            Pomada com acabamento seco, perfeita para penteados modernos e
            naturais. Oferece fixação duradoura sem deixar os fios pesados ou
            oleosos, mantendo o visual estiloso ao longo do dia.
          </p>
          <div class="product-tags">
            <span class="product-tag">Efeito Seco</span>
            <span class="product-tag">Fixação Duradoura</span>
          </div>
        </div>
      </div>

      <div class="product-card ">
        <div class="product-image-wrap">
          <div class="product-image-placeholder">
            <img
              src="img/efeitomolhado.png"
              alt="Pomade Million Efeito Molhado" />
          </div>
          <span class="product-badge">Cabelo</span>
        </div>
        <div class="product-body">
          <p class="product-brand">Pomade Million</p>
          <h3 class="product-name">Pomade Million Efeito Molhado</h3>
          <p class="product-desc">
            Pomada com acabamento brilhante que proporciona o clássico efeito
            molhado. Ideal para penteados alinhados e sofisticados, garantindo
            controle e definição sem ressecar os fios.
          </p>
          <div class="product-tags">
            <span class="product-tag">Efeito Molhado</span>
            <span class="product-tag">Brilho</span>
            <span class="product-tag">Alta Definição</span>
          </div>
        </div>
      </div>

      <div class="product-card ">
        <div class="product-image-wrap">
          <div class="product-image-placeholder">
            <img src="img/locao.png" alt="Loção pós barba Million" />
          </div>
          <span class="product-badge">barba</span>
        </div>
        <div class="product-body">
          <p class="product-brand">Pomade Million</p>
          <h3 class="product-name">Loção Pós Barba</h3>
          <p class="product-desc">
            Loção refrescante que acalma a pele após o barbear, reduzindo
            irritações e vermelhidão. Hidrata profundamente e proporciona uma
            sensação de frescor imediata.
          </p>
          <div class="product-tags">
            <span class="product-tag">Refrescante</span>
            <span class="product-tag">Hidratação</span>
            <span class="product-tag">Pós-Barba</span>
          </div>
        </div>
      </div>

      <div class="product-card ">
        <div class="product-image-wrap">
          <div class="product-image-placeholder">
            <img src="img/oleo.png" alt="Oleo para barba Million" />
          </div>
          <span class="product-badge">Barba</span>
        </div>
        <div class="product-body">
          <p class="product-brand">Pomade Million</p>
          <h3 class="product-name">Óleo Para Barba</h3>
          <p class="product-desc">
            Óleo nutritivo que hidrata profundamente os fios da barba e a pele
            abaixo. Deixa a barba macia, alinhada e com aspecto saudável, além
            de reduzir o ressecamento.
          </p>
          <div class="product-tags">
            <span class="product-tag">Hidratação</span>
            <span class="product-tag">Maciez</span>
            <span class="product-tag">Nutrição</span>
          </div>
        </div>
      </div>

      <div class="product-card ">
        <div class="product-image-wrap">
          <div class="product-image-placeholder">
            <img src="img/grooming.png" alt="Grooming Million" />
          </div>
          <span class="product-badge">Cabelo</span>
        </div>
        <div class="product-body">
          <p class="product-brand">Pomade Million</p>
          <h3 class="product-name">Grooming</h3>
          <p class="product-desc">
            Produto versátil para modelagem com fixação equilibrada e
            acabamento natural. Ideal para quem busca controle e estilo sem
            perder o movimento dos fios.
          </p>
          <div class="product-tags">
            <span class="product-tag">Versátil</span>
            <span class="product-tag">Natural</span>
            <span class="product-tag">Modelagem</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ══════════════ FOOTER ══════════════ -->
  <footer id="contato">
    <div class="footer-top">
      <div class="footer-brand">
        <img
          src="LOGO_TIO_PRETO.png"
          alt="Tio Preto Barbearia"
          onerror="this.style.display = 'none'" />
        <p>
          Tradição, estilo e cuidado em cada detalhe. A Tio Preto Barbearia é
          o seu espaço de confiança para sair sempre com o melhor visual.
        </p>
      </div>
      <div class="footer-col">
        <h4>Localização &amp; Horários</h4>
        <ul>
          <li>
            <i class="fa-solid fa-location-dot"></i> Rua Exemplo, 123 —
            Bairro, Cidade
          </li>
          <li><i class="fa-regular fa-clock"></i> Ter – Sex: 09h às 20h</li>
          <li><i class="fa-regular fa-clock"></i> Sábado: 08h ao 12h</li>
          <li>
            <i class="fa-regular fa-clock"></i>Domingo e Segunda: Fechado
          </li>
        </ul>
      </div>

      <div class="footer-col">
        <h4>Contato</h4>
        <ul>
          <li>
            <a href="tel:+5544998603404"><i class="fa-solid fa-phone"></i> (44) 9 9860-3404</a>
          </li>
          <li>
            <a href="https://wa.me/554498603404" target="_blank"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>
          </li>
          <li>
            <a href="https://instagram.com/tiopretobarbearia" target="_blank"><i class="fa-brands fa-instagram"></i> @tiopretobarbearia</a>
          </li>
        </ul>
      </div>
    </div>

    <div class="footer-bottom">
      <p>© 2026 Tio Preto Barbearia — Todos os direitos reservados.</p>
      <p class="footer-gold">Feito por Luizeh</p>
    </div>
  </footer>
</body>

</html>