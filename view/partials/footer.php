<!-- ══════════════ FOOTER ══════════════ -->
<?php
require_once __DIR__ . '/../../sql/SiteConfigSql.php';
$_footer = SiteConfigSql::buscarGrupo('footer');
$ftr = static function (string $k, string $f) use ($_footer): string {
  return htmlspecialchars($_footer[$k] ?? $f);
};
?>
<footer id="contato">
  <div class="footer-top">
    <div class="footer-brand">
      <img
        src="<?= $rootPath ?? '../' ?>assets/img/tiopretonb.png"
        alt="Tio Preto Barbearia"
        data-hide-on-error />
      <p><?= $ftr('footer_descricao', 'Tradição, estilo e cuidado em cada detalhe. A Tio Preto Barbearia é o seu espaço de confiança para sair sempre com o melhor visual.') ?></p>
    </div>
    <div class="footer-col">
      <h4>Localização &amp; Horários</h4>
      <ul>
        <li>
          <i class="fa-solid fa-location-dot"></i>
          <?= $ftr('footer_endereco', 'Rua Joao Vasques, 180 — Ana Laura 2, Douradina-PR') ?>
        </li>
        <li><i class="fa-regular fa-clock"></i> <?= $ftr('footer_horario_1', 'Ter – Sex: 8h30 às 19h') ?></li>
        <li><i class="fa-regular fa-clock"></i> <?= $ftr('footer_horario_2', 'Sábado: 08h ao 12h') ?></li>
        <li>
          <i class="fa-regular fa-clock"></i>
          <?= $ftr('footer_horario_3', 'Domingo e Segunda: Fechado') ?>
        </li>
      </ul>
    </div>

    <div class="footer-col">
      <h4>Contato</h4>
      <ul>
        <li>
          <a href="tel:<?= $ftr('footer_telefone', '+5544998603404') ?>">
            <i class="fa-solid fa-phone"></i>
            <?= $ftr('footer_telefone', '+5544998603404') ?>
          </a>
        </li>
        <li>
          <a href="https://wa.me/<?= $ftr('footer_whatsapp', '554498603404') ?>" target="_blank">
            <i class="fa-brands fa-whatsapp"></i> WhatsApp
          </a>
        </li>
        <li>
          <a href="https://instagram.com/<?= $ftr('footer_instagram', 'tiopretobarbearia') ?>" target="_blank">
            <i class="fa-brands fa-instagram"></i>
            @<?= $ftr('footer_instagram', 'tiopretobarbearia') ?>
          </a>
        </li>
      </ul>
    </div>
  </div>

  <div class="footer-bottom">
    <p><?= $ftr('footer_copyright', '© 2026 Tio Preto Barbearia — Todos os direitos reservados.') ?></p>
    <p class="footer-gold">Feito por Luizeh</p>
  </div>
</footer>