<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

/*
 * Mailer.php
 * Serviço de envio de e-mails do projeto (via PHPMailer/SMTP).
 * As credenciais vêm de config/mail.php → .env (nunca hardcoded).
 *
 * Quando MAIL_HOST está vazio (ambiente de desenvolvimento sem SMTP),
 * o e-mail é gravado em storage/mail/ para inspeção, em vez de enviado.
 */

class Mailer
{
    private static function config(): array
    {
        return require __DIR__ . '/../config/mail.php';
    }

    private static function logoPath(): string
    {
        return __DIR__ . '/../assets/img/tiopretonb.png';
    }

    /**
     * Envia o código de verificação de e-mail para um novo cadastro / troca de e-mail.
     * Lança RuntimeException se o envio falhar (para o chamador tratar).
     */
    public static function enviarCodigoVerificacao(string $paraEmail, string $paraNome, string $codigo, int $minutosValidade): void
    {
        self::enviarCodigo(
            $paraEmail,
            $paraNome,
            $codigo,
            $minutosValidade,
            'Seu código de verificação — Tio Preto Barbearia',
            'Confirme seu e-mail',
            'Use o código abaixo para confirmar seu e-mail:'
        );
    }

    /**
     * Envia o código de recuperação de senha por e-mail.
     */
    public static function enviarCodigoRecuperacao(string $paraEmail, string $paraNome, string $codigo, int $minutosValidade): void
    {
        self::enviarCodigo(
            $paraEmail,
            $paraNome,
            $codigo,
            $minutosValidade,
            'Recuperação de senha — Tio Preto Barbearia',
            'Recuperar senha',
            'Recebemos um pedido para redefinir sua senha. Use o código abaixo:'
        );
    }

    /**
     * Envio genérico de um código destacado. Reutilizado por todos os fluxos.
     */
    public static function enviarCodigo(
        string $paraEmail,
        string $paraNome,
        string $codigo,
        int $minutosValidade,
        string $assunto,
        string $titulo,
        string $chamada
    ): void {
        $cfg   = self::config();
        $html  = self::montarHtmlCodigo($paraNome, $codigo, $minutosValidade, $titulo, $chamada);
        $texto = self::montarTextoCodigo($paraNome, $codigo, $minutosValidade, $chamada);

        self::enviar($cfg, $paraEmail, $paraNome, $assunto, $html, $texto);
    }

    private static function enviar(array $cfg, string $paraEmail, string $paraNome, string $assunto, string $html, string $texto): void
    {
        // ── Envio via API HTTP (Resend) ─────────────────────────────────
        // Preferido em produção (Railway), onde as portas de SMTP são
        // bloqueadas. Usa porta 443 (HTTPS), que não sofre bloqueio.
        if (!empty($cfg['resend_api_key'])) {
            self::enviarViaResend($cfg, $paraEmail, $paraNome, $assunto, $html, $texto);
            return;
        }

        // ── Modo desenvolvimento: sem SMTP configurado → grava em arquivo ──
        if (empty($cfg['host'])) {
            self::gravarEmailDev($paraEmail, $assunto, $html);
            return;
        }

        require_once __DIR__ . '/../vendor/autoload.php';

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = $cfg['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $cfg['username'];
            $mail->Password   = $cfg['password'];
            $mail->Port       = $cfg['port'];
            $mail->CharSet    = PHPMailer::CHARSET_UTF8;
            // Limita a espera por um SMTP lento/indisponível (em segundos).
            // Mesmo com o envio já desacoplado da resposta ao cliente, evita
            // que o processo de background fique preso por muito tempo.
            $mail->Timeout    = 15;
            if (!empty($cfg['encryption'])) {
                $mail->SMTPSecure = $cfg['encryption'] === 'ssl'
                    ? PHPMailer::ENCRYPTION_SMTPS
                    : PHPMailer::ENCRYPTION_STARTTLS;
            }
            if (!empty($cfg['debug'])) {
                $mail->SMTPDebug   = SMTP::DEBUG_SERVER;
                $mail->Debugoutput = 'error_log';
            }

            $mail->setFrom($cfg['from_email'], $cfg['from_name']);
            $mail->addAddress($paraEmail, $paraNome);

            // Logo embutida (cid:tplogo) — renderiza no Gmail, ao contrário de base64.
            $logo = self::logoPath();
            if (is_file($logo)) {
                $mail->addEmbeddedImage($logo, 'tplogo', 'tiopretonb.png');
            }

            $mail->isHTML(true);
            $mail->Subject = $assunto;
            $mail->Body    = $html;
            $mail->AltBody = $texto;

            $mail->send();
        } catch (PHPMailerException $e) {
            error_log('Falha ao enviar e-mail: ' . $mail->ErrorInfo);
            throw new RuntimeException('Não foi possível enviar o e-mail de verificação. Tente novamente em instantes.');
        }
    }

    /**
     * Substitui a logo embutida (cid:tplogo) pela URL pública da logo no site,
     * já que o envio por API não suporta imagem embutida por cid:.
     */
    private static function trocarLogoPorUrlPublica(string $html): string
    {
        $base = Env::get('APP_URL', '');
        if ($base === '') {
            $dom = getenv('RAILWAY_PUBLIC_DOMAIN');
            if ($dom) {
                $base = 'https://' . $dom;
            }
        }
        if ($base !== '') {
            $html = str_replace('cid:tplogo', rtrim($base, '/') . '/assets/img/tiopretonb.png', $html);
        }
        return $html;
    }

    /**
     * Envio via API HTTP do Resend (https://api.resend.com) — porta 443.
     * Usado em produção (Railway) onde o SMTP é bloqueado.
     * Lança RuntimeException em caso de falha (mesmo contrato do envio SMTP).
     *
     * Obs.: com o remetente de teste onboarding@resend.dev, o Resend só
     * entrega no e-mail da própria conta; outros destinatários exigem um
     * domínio verificado no Resend.
     */
    private static function enviarViaResend(array $cfg, string $paraEmail, string $paraNome, string $assunto, string $html, string $texto): void
    {
        $html = self::trocarLogoPorUrlPublica($html);

        $de = $cfg['from_name'] !== ''
            ? sprintf('%s <%s>', $cfg['from_name'], $cfg['resend_from'])
            : $cfg['resend_from'];

        $payload = json_encode([
            'from'    => $de,
            'to'      => [$paraEmail],
            'subject' => $assunto,
            'html'    => $html,
            'text'    => $texto,
        ], JSON_UNESCAPED_UNICODE);

        $ch = curl_init('https://api.resend.com/emails');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => [
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Bearer ' . $cfg['resend_api_key'],
            ],
            CURLOPT_TIMEOUT        => 15,
        ]);
        $resp = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);

        if ($code < 200 || $code >= 300) {
            error_log("Resend API falhou (HTTP {$code}): " . ($err !== '' ? $err : (string) $resp));
            throw new RuntimeException('Não foi possível enviar o e-mail de verificação. Tente novamente em instantes.');
        }
    }

    private static function gravarEmailDev(string $paraEmail, string $assunto, string $html): void
    {
        $dir = __DIR__ . '/../storage/mail';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        // No preview de dev, cid: não resolve no navegador → troca pela data-URI da logo.
        $logo = self::logoPath();
        if (is_file($logo)) {
            $dataUri = 'data:image/png;base64,' . base64_encode((string) file_get_contents($logo));
            $html = str_replace('cid:tplogo', $dataUri, $html);
        }

        $arquivo = $dir . '/' . date('Ymd_His') . '_' . preg_replace('/[^a-z0-9]+/i', '_', $paraEmail) . '.html';
        $conteudo = "<!-- Para: {$paraEmail} | Assunto: {$assunto} -->\n" . $html;
        @file_put_contents($arquivo, $conteudo);
        error_log("[Mailer DEV] E-mail para {$paraEmail} gravado em {$arquivo} (SMTP não configurado).");
    }

    /**
     * Template simples e compatível (Gmail, Outlook, mobile).
     * Baseado em tabelas e CSS inline mínimo — sem gradientes, flex ou grid.
     */
    private static function montarHtmlCodigo(string $nome, string $codigo, int $minutos, string $titulo, string $chamada): string
    {
        $nomeSeguro    = htmlspecialchars($nome, ENT_QUOTES, 'UTF-8');
        $codigoSeguro  = htmlspecialchars($codigo, ENT_QUOTES, 'UTF-8');
        $tituloSeguro  = htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8');
        $chamadaSegura = htmlspecialchars($chamada, ENT_QUOTES, 'UTF-8');

        return <<<HTML
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f1ec;margin:0;padding:0;">
  <tr>
    <td align="center" style="padding:24px 12px;">
      <table role="presentation" width="440" cellpadding="0" cellspacing="0" style="width:440px;max-width:100%;background:#ffffff;border:1px solid #e7e0d4;border-radius:8px;font-family:Arial,Helvetica,sans-serif;">
        <tr>
          <td align="center" style="background:#1a1a1a;border-radius:8px 8px 0 0;padding:24px;">
            <img src="cid:tplogo" alt="Tio Preto Barbearia" width="140" style="width:140px;max-width:60%;height:auto;border:0;" />
          </td>
        </tr>
        <tr>
          <td style="padding:28px 28px 8px;color:#1a1a1a;">
            <h1 style="margin:0 0 12px;font-size:20px;font-weight:bold;color:#1a1a1a;">{$tituloSeguro}</h1>
            <p style="margin:0 0 20px;font-size:15px;line-height:22px;color:#555555;">
              Olá, <strong>{$nomeSeguro}</strong>! {$chamadaSegura}
            </p>
          </td>
        </tr>
        <tr>
          <td align="center" style="padding:0 28px;">
            <table role="presentation" cellpadding="0" cellspacing="0">
              <tr>
                <td align="center" style="background:#faf7f0;border:2px dashed #c9963a;border-radius:8px;padding:16px 28px;font-size:34px;font-weight:bold;letter-spacing:8px;color:#1a1a1a;">
                  {$codigoSeguro}
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td align="center" style="padding:16px 28px 4px;">
            <p style="margin:0;font-size:14px;color:#777777;">Válido por <strong>{$minutos} minutos</strong>.</p>
          </td>
        </tr>
        <tr>
          <td style="padding:16px 28px 24px;">
            <p style="margin:0;font-size:12px;line-height:18px;color:#999999;">
              Se você não solicitou, ignore esta mensagem — nada é alterado sem o código.
            </p>
          </td>
        </tr>
        <tr>
          <td align="center" style="border-top:1px solid #efe9df;padding:16px 28px;">
            <p style="margin:0;font-size:11px;color:#b0a99c;">© 2026 Tio Preto Barbearia · Douradina-PR</p>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
HTML;
    }

    private static function montarTextoCodigo(string $nome, string $codigo, int $minutos, string $chamada): string
    {
        return "Ola, {$nome}!\n\n"
            . "{$chamada}\n\n"
            . "Codigo: {$codigo}\n"
            . "Valido por {$minutos} minutos.\n\n"
            . "Se voce nao solicitou, ignore esta mensagem.\n\n"
            . "Tio Preto Barbearia";
    }
}
