<?php

namespace App\Models;

use CodeIgniter\Model;

class CookieConsentModel extends Model
{
    protected $table         = 'cookie_consents';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['categories', 'ip', 'user_agent'];
    protected $useTimestamps = true;
    protected $updatedField  = '';   // solo created_at
}