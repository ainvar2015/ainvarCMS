<?php

namespace App\Models;

use CodeIgniter\Model;

class ConfigModel extends Model
{
    protected $table      = 'settings';
    protected $primaryKey = 'id';
    protected $allowedFields = ['key','value','type'];
    
    public function get($key, $default = null)
    {
        $row = $this->where('key', $key)->first();
        if (!$row) return $default;
        
        return match($row['type']) {
            'int'  => (int) $row['value'],
            'bool' => (bool) $row['value'],
            'json' => json_decode($row['value'], true),
            default => $row['value'],
        };
    }
    
    public function saveValue($key, $value, $type = 'string')
    {
        if ($type === 'json') $value = json_encode($value);
        
        $exists = $this->where('key', $key)->first();
        if ($exists) {
            return $this->update($exists['id'], ['value' => $value, 'type' => $type]);
        }
        return $this->insert(['key' => $key, 'value' => $value, 'type' => $type]);
    }
}