<?php

namespace App\Models;

use CodeIgniter\Model;

class PageRevisionModel extends Model
{
    protected $table         = 'page_revisions';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['page_id', 'user_id', 'title', 'content', 'meta_title', 'meta_desc', 'created_at'];
    protected $useTimestamps = false;
    protected $returnType    = 'array';

    // Guarda una foto del estado de la página
    public function createSnapshot(array $page, ?int $userId): int
    {
        return (int) $this->insert([
            'page_id'    => $page['id'],
            'user_id'    => $userId,
            'title'      => $page['title'] ?? '',
            'content'    => $page['content'] ?? null,
            'meta_title' => $page['meta_title'] ?? null,
            'meta_desc'  => $page['meta_desc'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    // Mantiene solo las últimas $keep revisiones de una página
    public function prune(int $pageId, int $keep = 20): void
    {
        $keepIds = array_column(
            $this->where('page_id', $pageId)->orderBy('id', 'DESC')->limit($keep)->findAll(),
            'id'
        );
        if ($keepIds) {
            $this->where('page_id', $pageId)->whereNotIn('id', $keepIds)->delete();
        }
    }
}