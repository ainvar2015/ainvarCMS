<?php

namespace App\Models;

use CodeIgniter\Model;

class MediaModel extends Model
{
    protected $table         = 'media';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'folder_id', 'file_path', 'original_name', 'mime_type', 'size',
        'width', 'height', 'title', 'alt_text', 'uploaded_by',
    ];
    protected $useTimestamps = false;
    protected $returnType    = 'array';
}