<?php

namespace App\Controllers\Admin;

class Dashboard extends BaseController
{
    public function index()
    {
        $pageModel   = new \App\Models\PageModel();
        $moduleModel = new \App\Models\ModuleModel();
        $themeModel  = new \App\Models\ThemeModel();

        $totalPages = $pageModel->countAll();
        $publishedPages = 0;
        $draftPages = 0;
        try {
            $publishedPages = $pageModel->where('status', 'published')->countAllResults();
            $draftPages     = $pageModel->where('status', 'draft')->countAllResults();
        } catch (\Exception $e) {
        }

        // Envíos de formularios
        $unreadSubmissions = 0;
        $recentSubmissions = [];
        $chartLabels = [];
        $chartData   = [];
        try {
            $subModel = new \App\Models\FormSubmissionModel();
            $unreadSubmissions = $subModel->where('read', 0)->countAllResults();
            $recentSubmissions = $subModel->orderBy('created_at', 'DESC')->limit(5)->findAll();

            // === GRÁFICO: envíos por semana (últimas 12 semanas) ===
            $startMonday = strtotime('monday this week -11 weeks');
            $buckets = [];
            for ($i = 0; $i < 12; $i++) {
                $ws = strtotime("+{$i} weeks", $startMonday);
                $buckets[date('Y-W', $ws)] = 0;
                $chartLabels[] = 'Sem. ' . date('d/m', $ws);
            }

            $rows = $subModel->where('created_at >=', date('Y-m-d H:i:s', $startMonday))
                ->select('created_at')
                ->findAll();
            foreach ($rows as $r) {
                $key = date('Y-W', strtotime($r['created_at']));
                if (isset($buckets[$key])) {
                    $buckets[$key]++;
                }
            }
            $chartData = array_values($buckets);
        } catch (\Exception $e) {
        }

        // Últimas páginas
        $recentPages = [];
        try {
            $recentPages = $pageModel->orderBy('id', 'DESC')->limit(5)->findAll();
        } catch (\Exception $e) {
        }

        // Extras de módulos (solo si están instalados)
        $totalProducts = null;
        $totalImages   = null;

        if (class_exists(\App\Modules\Catalog\Models\ProductModel::class)) {
            try {
                $totalProducts = (new \App\Modules\Catalog\Models\ProductModel())->countAll();
            } catch (\Throwable $e) {
                // tablas del módulo ausentes o cualquier otro fallo de BD
            }
        }

        if (class_exists(\App\Modules\Gallery\Models\ImageModel::class)) {
            try {
                $totalImages = (new \App\Modules\Gallery\Models\ImageModel())->countAll();
            } catch (\Throwable $e) {
            }
        }

        $activeModules = $moduleModel->where('active', 1)->findAll();
        $activeTheme   = $themeModel->where('active', 1)->first();

        return $this->render('dashboard', [
            'title'              => 'Dashboard',
            'total_pages'        => $totalPages,
            'published_pages'    => $publishedPages,
            'draft_pages'        => $draftPages,
            'unread_submissions' => $unreadSubmissions,
            'recent_submissions' => $recentSubmissions,
            'chart_labels'       => $chartLabels,
            'chart_data'         => $chartData,
            'recent_pages'       => $recentPages,
            'total_products'     => $totalProducts,
            'total_images'       => $totalImages,
            'active_modules'     => $activeModules,
            'total_modules'      => count($activeModules),
            'active_theme'       => $activeTheme,
        ]);
    }
}
