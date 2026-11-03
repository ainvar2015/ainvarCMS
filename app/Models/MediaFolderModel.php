<?php

namespace App\Models;

use CodeIgniter\Model;

class MediaFolderModel extends Model
{
    protected $table         = 'media_folders';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['name', 'parent_id'];
    protected $useTimestamps = false;
    protected $returnType    = 'array';
}