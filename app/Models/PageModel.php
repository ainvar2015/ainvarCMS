<?php

namespace App\Models;

use CodeIgniter\Model;

class PageModel extends Model
{
    protected $table      = 'pages';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title','slug','type','content','template_id','module_name','meta_title','meta_desc','status','sort_order','lang'];
    protected $useTimestamps = true;

    public function getBySlug($slug, $lang = null)
    {
        $builder = $this->where('slug', $slug)->where('status', 'published');
        if ($lang) {
            $builder->where('lang', $lang);
        }
        return $builder->first();
    }
}