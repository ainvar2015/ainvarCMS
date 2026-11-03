<?php

namespace App\Models;

use CodeIgniter\Model;

class ModuleModel extends Model
{
    protected $table      = 'modules';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name','slug','version','description','namespace','path','has_admin','has_front','active','installed_at'];
}