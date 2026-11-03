<?php

namespace App\Models;

use CodeIgniter\Model;

class MenuModel extends Model
{
    protected $table      = 'menus';
    protected $primaryKey = 'id';
    protected $allowedFields = ['page_id', 'label', 'url', 'icon', 'parent_id', 'sort_order', 'target', 'active'];

    public function getMenuTree($parentId = null, $lang = null)
    {
        $builder = $this->where('active', 1);

        // ← NUEVO: solo items sin página asociada (enlaces personalizados)
        // o cuya página esté publicada
        $builder->groupStart()
            ->where('page_id IS NULL')
            ->orWhereIn('page_id', function ($sub) {
                $sub->select('id')->from('pages')->where('status', 'published');
            })
            ->groupEnd();

        $fields = $this->db->getFieldNames($this->table);
        if (in_array('sort_order', $fields)) {
            $builder->orderBy('sort_order', 'ASC');
        } else {
            $builder->orderBy('id', 'ASC');
        }

        if ($lang && in_array('lang', $fields)) {
            $builder->where('lang', $lang);
        }

        if ($parentId === null) {
            $builder->where('parent_id IS NULL');
        } else {
            $builder->where('parent_id', $parentId);
        }

        $items = $builder->findAll();

        foreach ($items as &$item) {
            $item['children'] = $this->getMenuTree($item['id'], $lang);
        }
        return $items;
    }

    public function getAllWithSlug()
    {
        $builder = $this->db->table('menus m');
        $builder->select('m.*, p.slug as page_slug');
        $builder->join('pages p', 'p.id = m.page_id', 'left');
        $builder->orderBy('m.parent_id', 'ASC');
        $builder->orderBy('m.sort_order', 'ASC');
        return $builder->get()->getResultArray();
    }

    /** Options HTML del árbol de menús, indentado por profundidad. Excluye $excludeId (anti auto-padre). */
    public function getOptions(int $excludeId = null, int $parentId = null, int $depth = 0): string
    {
        $rows = $this->db->table('menus')
            ->where('parent_id', $parentId)
            ->orderBy('sort_order', 'ASC')
            ->get()->getResultArray();

        $html = '';
        foreach ($rows as $row) {
            if ($excludeId !== null && (int) $row['id'] === $excludeId) continue;
            $html .= '<option value="' . $row['id'] . '">'
                . ($depth ? str_repeat('— ', $depth) : '')
                . esc($row['label'])
                . '</option>';
            $html .= $this->getOptions($excludeId, (int) $row['id'], $depth + 1);
        }
        return $html;
    }
}
