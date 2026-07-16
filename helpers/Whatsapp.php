<?php

/**
 * Whatsapp.php
 * Serviço reutilizável de envio de mensagens via API r4dev WhatsApp.
 *
 * Centraliza a integração usada tanto pelo painel admin (lembretes/avisos)
 * quanto pelos fluxos de verificação de telefone e recuperação de senha.
 *
 * O telefone é sempre normalizado para 55{DDD}{numero} (só dígitos) antes do
 * envio. Nenhum dado sensível (código, senha) é gravado em log.
 */
class Whatsapp
{
    private const ENDPOINT = 'https://dev-api.r4dev.com.br/v1/instance/cmqqzc2j1002d104shfslo3sj/messages/chat';
    private const TOKEN     = 'cmqqzc2j2002e104so1o09hqy';

    /**
     * Envia uma mensagem de texto para um telefone.
     * Retorna ['success' => bool, 'id'|'message' => ..., 'telefone' => ...].
     */
    public static function enviar(string $telefone, string $mensagem): array
    {
        $telefone = preg_replace('/\D/', '', $telefone);
        if ($telefone === '') {
            return ['success' => false, 'message' => 'Telefone nao informado.'];
        }
        if (!str_starts_with($telefone, '55')) {
            $telefone = '55' . $telefone;
        }
        if (strlen($telefone) < 12 || strlen($telefone) > 13) {
            return ['success' => false, 'message' => 'Numero de telefone invalido.'];
        }

        $ch = curl_init(self::ENDPOINT);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Token: ' . self::TOKEN,
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS     => json_encode(['to' => $telefone, 'body' => $mensagem], JSON_UNESCAPED_UNICODE),
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response  = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($curlError) {
            return ['success' => false, 'message' => 'Erro de conexao com a API de mensagens.'];
        }

        $decoded = json_decode($response, true);
        if ($httpCode >= 200 && $httpCode < 300 && !empty($decoded['id'])) {
            return ['success' => true, 'id' => $decoded['id'], 'telefone' => $telefone];
        }

        return ['success' => false, 'message' => 'A API de mensagens retornou um erro.', 'http_code' => $httpCode];
    }

    /**
     * Envia um código de verificação/recuperação por WhatsApp.
     * Lança RuntimeException se o envio falhar (para o chamador tratar).
     */
    public static function enviarCodigo(string $telefone, string $codigo, int $minutosValidade, string $contexto = 'verificação'): void
    {
        $mensagem = "*Tio Preto Barbearia* 💈\n\n"
            . "Seu código de {$contexto} é:\n\n"
            . "*{$codigo}*\n\n"
            . "Válido por {$minutosValidade} minutos. Não compartilhe este código com ninguém.\n\n"
            . "Se você não solicitou, ignore esta mensagem.";

        $resultado = self::enviar($telefone, $mensagem);
        if (!$resultado['success']) {
            throw new RuntimeException($resultado['message'] ?? 'Falha ao enviar a mensagem de WhatsApp.');
        }
    }
}
