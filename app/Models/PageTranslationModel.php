<?php

namespace App\Models;

use CodeIgniter\Model;

class PageTranslationModel extends Model
{
    protected $table         = 'page_translations';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['page_id', 'lang', 'title', 'slug', 'content', 'meta_title', 'meta_desc'];
    protected $useTimestamps = true;
    protected $returnType    = 'array';
}