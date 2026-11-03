<?php

namespace App\Models;

use CodeIgniter\Model;

class TemplateModel extends Model
{
    protected $table      = 'templates';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name','slug','description','file_path','fields','active'];
    protected $useTimestamps = true;
}