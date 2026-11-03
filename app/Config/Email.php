<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    public string $fromEmail  = 'noreply@ainvarcms.loc';
    public string $fromName   = 'CMS Ainvar';
    public string $recipients = '';

    public string $userAgent = 'CodeIgniter';
    public string $protocol  = 'mail'; // En local usa 'mail', en producción 'smtp'

    // Si usas SMTP en producción, descomenta y configura:
    // public string $SMTPHost  = 'smtp.tudominio.com';
    // public string $SMTPUser  = 'tuemail@tudominio.com';
    // public string $SMTPPass  = 'tucontraseña';
    // public int    $SMTPPort  = 587;
    // public string $SMTPCrypto = 'tls';

    public string $mailType = 'html';
    public string $charset  = 'UTF-8';
    public bool   $wordWrap = true;
    public int    $wrapChars = 76;
    public bool   $validate = false;
    public int    $priority = 3;
    public string $newline  = "\r\n";
    public string $CRLF     = "\r\n";
}
