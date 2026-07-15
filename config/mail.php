<?php

/*
 * mail.php
 * Configuração de envio de e-mail, lida a partir do .env (via Env).
 * Nenhuma credencial fica hardcoded aqui — todas vêm do ambiente.
 *
 * Uso:  $cfg = require __DIR__ . '/mail.php';
 */

require_once __DIR__ . '/Env.php';

return [
    'from_name'  => Env::get('MAIL_FROM_NAME', 'Tio Preto Barbearia'),
    'from_email' => Env::get('MAIL_FROM_EMAIL', 'nao-responda@tiopretobarbearia.com.br'),
    'host'       => Env::get('MAIL_HOST', ''),
    'port'       => (int) Env::get('MAIL_PORT', '587'),
    'encryption' => Env::get('MAIL_ENCRYPTION', 'tls'),
    'username'   => Env::get('MAIL_USERNAME', ''),
    'password'   => Env::get('MAIL_PASSWORD', ''),
    'debug'      => Env::get('MAIL_DEBUG', '0') === '1',
];
