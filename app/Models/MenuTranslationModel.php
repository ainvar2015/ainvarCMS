<?php

namespace App\Models;

use CodeIgniter\Model;

class MenuTranslationModel extends Model
{
    protected $table         = 'menu_translations';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['menu_id', 'lang', 'label'];
    protected $useTimestamps = true;
    protected $returnType    = 'array';
}