<?php

namespace App\Models;

use CodeIgniter\Model;

class ThemeModel extends Model
{
    protected $table      = 'themes';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name','slug','version','description','path','screenshot','is_default','active'];
}