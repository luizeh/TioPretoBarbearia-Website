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
     * Envia o código de verificação de e-mail para um novo cadastro.
     * Lança RuntimeException se o envio falhar (para o chamador tratar).
     */
    public static function enviarCodigoVerificacao(string $paraEmail, string $paraNome, string $codigo, int $minutosValidade): void
    {
        $cfg     = self::config();
        $assunto = 'Seu código de verificação — Tio Preto Barbearia';
        $html    = self::montarHtmlCodigo($paraNome, $codigo, $minutosValidade);
        $texto   = self::montarTextoCodigo($paraNome, $codigo, $minutosValidade);

        self::enviar($cfg, $paraEmail, $paraNome, $assunto, $html, $texto);
    }

    private static function enviar(array $cfg, string $paraEmail, string $paraNome, string $assunto, string $html, string $texto): void
    {
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

    private static function montarHtmlCodigo(string $nome, string $codigo, int $minutos): string
    {
        $nomeSeguro   = htmlspecialchars($nome, ENT_QUOTES, 'UTF-8');
        $codigoSeguro = htmlspecialchars($codigo, ENT_QUOTES, 'UTF-8');

        return <<<HTML
<div style="margin:0;padding:0;background:#f4f1ec;">
  <div style="max-width:540px;margin:0 auto;padding:32px 16px;font-family:'Segoe UI',Arial,Helvetica,sans-serif;">
    <div style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 12px 34px rgba(0,0,0,.10);">

      <!-- Cabeçalho com logo -->
      <div style="background:#1a1a1a;padding:32px 32px 26px;text-align:center;">
        <img src="cid:tplogo" alt="Tio Preto Barbearia" width="160" style="width:160px;max-width:62%;height:auto;display:inline-block;border:0;" />
      </div>
      <!-- Faixa dourada -->
      <div style="height:4px;background:linear-gradient(90deg,#a67c1e,#e6c65c,#a67c1e);"></div>

      <!-- Corpo -->
      <div style="padding:36px 38px 26px;color:#2b2b2b;">
        <h2 style="margin:0 0 8px;font-size:21px;color:#1a1a1a;font-weight:700;">Confirme seu e-mail</h2>
        <p style="font-size:15px;line-height:1.6;margin:0 0 26px;color:#555;">
          Olá, <strong style="color:#1a1a1a;">{$nomeSeguro}</strong>! Falta pouco para ativar sua conta.
          Informe o código abaixo na tela de verificação:
        </p>

        <!-- Código em destaque -->
        <div style="text-align:center;margin:0 0 22px;">
          <div style="display:inline-block;background:#faf7f0;border:2px dashed #c9a227;border-radius:12px;padding:20px 26px 20px 38px;">
            <span style="font-size:38px;font-weight:700;letter-spacing:12px;color:#1a1a1a;">{$codigoSeguro}</span>
          </div>
        </div>

        <p style="text-align:center;font-size:14px;color:#777;margin:0;">
          Válido por <strong style="color:#1a1a1a;">{$minutos} minutos</strong>.
        </p>
      </div>

      <!-- Aviso -->
      <div style="padding:20px 38px 30px;border-top:1px solid #efe9df;">
        <p style="font-size:12.5px;line-height:1.6;color:#999;margin:0;">
          Não solicitou este cadastro? É só ignorar esta mensagem — nenhuma conta é ativada sem o código.
        </p>
      </div>
    </div>

    <!-- Rodapé -->
    <p style="text-align:center;font-size:11.5px;color:#b0a99c;margin:20px 0 0;letter-spacing:.3px;">
      © 2026 Tio Preto Barbearia · Douradina-PR
    </p>
  </div>
</div>
HTML;
    }

    private static function montarTextoCodigo(string $nome, string $codigo, int $minutos): string
    {
        return "Ola, {$nome}!\n\n"
            . "Seu codigo de verificacao da Tio Preto Barbearia e: {$codigo}\n"
            . "Ele e valido por {$minutos} minutos.\n\n"
            . "Se voce nao solicitou este cadastro, ignore esta mensagem.";
    }
}
