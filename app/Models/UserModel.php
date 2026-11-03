<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table         = 'users';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'username', 'first_name', 'last_name', 'email', 'password', 'role', 'active',
        'failed_attempts', 'locked_until',
        'pin', 'phone', 'avatar', 'address', 'city', 'province', 'zip', 'bio',
        'show_email', 'show_name', 'allow_contact',
    ];

    protected $beforeInsert = ['hashSecrets'];
    protected $beforeUpdate = ['hashSecrets'];

    /**
     * Hashea password/pin solo si llegan en plano.
     * Un valor ya hasheado (password_needs_rehash = false) se respeta.
     */
    protected function hashSecrets(array $data): array
    {
        foreach (['password', 'pin'] as $field) {
            $value = $data['data'][$field] ?? null;
            if (!empty($value) && password_needs_rehash($value, PASSWORD_DEFAULT)) {
                $data['data'][$field] = password_hash($value, PASSWORD_DEFAULT);
            }
        }

        return $data;
    }
}