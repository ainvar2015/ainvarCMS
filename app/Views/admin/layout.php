<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token-name" content="<?= csrf_token() ?>">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <script>
        window.CSRF_NAME = '<?= csrf_token() ?>';
        window.ADMIN_URL = '<?= base_url('admin/') ?>';
    </script>
    <script src="<?= base_url('assets/admin/js/media-picker.js') ?>"></script>
    <title>CMS Admin - <?= esc($title ?? 'Panel') ?></title>
    <script>
        // Aplica el tema lo antes posible para evitar flash de tema claro
        (function() {
            try {
                var t = localStorage.getItem('admin-theme');
                if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.setAttribute('data-bs-theme', 'dark');
                }
            } catch (e) {}
        })();
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .sidebar {
            min-height: 100vh;
            /* !important necesario: .offcanvas-md fuerza background:transparent !important desde 768px */
            background: #2c3e50 !important;
        }

        .sidebar .nav-link {
            color: #ecf0f1;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: #34495e;
            color: #fff;
        }

        .main-content {
            padding: 20px;
        }

        .diff-ins {
            background: #d4edda;
            color: #155724;
            border-radius: 3px;
        }

        .diff-del {
            background: #f8d7da;
            color: #721c24;
            border-radius: 3px;
            text-decoration: line-through;
        }

        /* === Modo oscuro: ajustes de componentes propios === */
        [data-bs-theme="dark"] body {
            background: #1a1d21;
        }

        [data-bs-theme="dark"] .menu-item,
        [data-bs-theme="dark"] .drop-zone {
            background: #212529;
            border-color: #495057;
        }

        [data-bs-theme="dark"] .drop-zone:hover,
        [data-bs-theme="dark"] .drop-zone.drag-active {
            background: #1a2634;
            border-color: #0d6efd;
        }

        [data-bs-theme="dark"] .menu-tree ul {
            border-left-color: #495057;
        }
    </style>
    <?= $this->renderSection('css') ?>
</head>

<body>
    <!-- Barra superior solo móvil: botón hamburguesa -->
    <nav class="navbar navbar-dark sticky-top d-md-none" style="background:#2c3e50;">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-label="Abrir menú">
                <span class="navbar-toggler-icon"></span>
            </button>
            <span class="navbar-brand mb-0">CMS Admin</span>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar (offcanvas en móvil, columna fija desde md) -->
            <nav class="col-md-2 sidebar p-0 offcanvas-md offcanvas-start" id="sidebarMenu" tabindex="-1"
                aria-label="Menú de administración">
                <div class="d-flex justify-content-end p-2 d-md-none">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                        data-bs-target="#sidebarMenu" aria-label="Cerrar menú"></button>
                </div>
                <div class="offcanvas-body p-0 flex-column">
                <div class="p-3 text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">CMS Admin</h4>
                        <p class="mb-0 small">Tipo usuario: <?= esc(session()->get('role')) ?></p>
                    </div>
                    <div class="btn-group" role="group" aria-label="Ayuda-tema">
                        <button type="button" id="helpButton" class="btn btn-sm btn-outline-light border-0" title="Ayuda"
                            target="_blank" onclick="window.open('<?= base_url('admin/help') ?>', '_blank')">
                            <i class="bi bi-question-circle"></i>
                        </button>
                        <button type="button" id="themeToggle" class="btn btn-sm btn-outline-light border-0"
                            title="Cambiar tema claro/oscuro">
                            <i class="bi bi-moon-stars"></i>
                        </button>
                    </div>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link <?= ($segment ?? 'dashboard') === 'dashboard' ? 'active' : '' ?>" href="<?= base_url('admin/dashboard') ?>">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <?php
                        $unreadCount = 0;
                        try {
                            $unreadCount = (new \App\Models\FormSubmissionModel())->where('read', 0)->countAllResults();
                        } catch (\Throwable $e) {
                        }
                        ?>
                        <a class="nav-link <?= ($segment ?? 'submissions') === 'submissions' ? 'active' : '' ?>" href="<?= base_url('admin/submissions') ?>">
                            <i class="bi bi-envelope"></i> Envíos
                            <span id="submissionsBadge"
                                class="badge bg-danger rounded-pill ms-1 <?= $unreadCount ? '' : 'd-none' ?>">
                                <?= $unreadCount ?>
                            </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($segment ?? '') === 'pages' ? 'active' : '' ?>" href="<?= base_url('admin/pages') ?>">
                            <i class="bi bi-file-earmark-text"></i> Páginas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($segment ?? '') === 'templates' ? 'active' : '' ?>" href="<?= base_url('admin/templates') ?>">
                            <i class="bi bi-layout-text-window"></i> Plantillas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($segment ?? '') === 'menus' ? 'active' : '' ?>" href="<?= base_url('admin/menus') ?>">
                            <i class="bi bi-menu-button"></i> Menús
                        </a>
                    </li>
                    <?php if (can('admin')): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment ?? '') === 'modules' ? 'active' : '' ?>" href="<?= base_url('admin/modules') ?>">
                                <i class="bi bi-box-seam"></i> Módulos
                            </a>
                            <?php
                            // Menús dinámicos de módulos activos
                            $moduleModel = new \App\Models\ModuleModel();
                            $activeModules = $moduleModel->where('active', 1)->findAll();
                            echo '<ul class="nav flex-column ms-3">'; // submenú de módulos
                            foreach ($activeModules as $mod) {
                                $menuFile = ROOTPATH . $mod['path'] . '/menu.php';
                                if (!file_exists($menuFile)) continue;

                                $menu = require $menuFile;
                                if (empty($menu['items'])) continue;

                                $isActive = ($segment ?? '') === ($menu['segment'] ?? $mod['slug']);
                                $inSub = false;
                                foreach ($menu['items'] as $item) {
                                    $itemSeg = explode('/', $item['url'])[1] ?? '';
                                    if (($segment ?? '') === $itemSeg) $inSub = true;
                                }
                                $expanded = $isActive || $inSub;
                            ?>
                        <li class="nav-item">
                            <a class="nav-link d-flex justify-content-between align-items-center <?= $expanded ? 'active' : '' ?>"
                                data-bs-toggle="collapse" href="#menuMod<?= $mod['id'] ?>" role="button">
                                <span><i class="bi <?= $menu['icon'] ?? 'bi-puzzle' ?>"></i> <?= esc($menu['label'] ?? $mod['name']) ?></span>
                                <i class="bi bi-chevron-down small"></i>
                            </a>
                            <div class="collapse <?= $expanded ? 'show' : '' ?>" id="menuMod<?= $mod['id'] ?>">
                                <ul class="nav flex-column ms-3">
                                    <?php foreach ($menu['items'] as $item):
                                        $itemActive = (service('uri')->getPath() === '/' . $item['url']);
                                    ?>
                                        <li class="nav-item">
                                            <a class="nav-link py-1 <?= $itemActive ? 'active' : '' ?>" href="<?= base_url($item['url']) ?>">
                                                <i class="bi <?= $item['icon'] ?? 'bi-circle' ?>"></i> <?= esc($item['label']) ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
                </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link <?= ($segment ?? '') === 'themes' ? 'active' : '' ?>" href="<?= base_url('admin/themes') ?>">
                    <i class="bi bi-palette"></i> Temas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($segment ?? '') === 'media' ? 'active' : '' ?>" href="<?= base_url('admin/media') ?>">
                    <i class="bi bi-images"></i> Medios
                </a>
            </li>

            <?php if ((session()->get('role') === 'admin') || (session()->get('role') === 'superadmin')): ?>
                <li class="nav-item">
                    <a class="nav-link <?= ($segment ?? '') === 'users' ? 'active' : '' ?>" href="<?= base_url('admin/users') ?>">
                        <i class="bi bi-people"></i> Usuarios
                    </a>
                </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link <?= ($segment ?? '') === 'settings' ? 'active' : '' ?>" href="<?= base_url('admin/settings') ?>">
                    <i class="bi bi-gear"></i> Configuración
                </a>
            </li>
            <?php if (session()->get('role') === 'superadmin'): ?>
                <a href="<?= base_url('admin/backups') ?>" class="nav-link">
                    <i class="bi bi-archive"></i> Copias de seguridad
                </a>
            <?php endif; ?>
            <li class="nav-item mt-3">
                <a class="nav-link text-danger" href="<?= base_url('logout') ?>">
                    <i class="bi bi-box-arrow-right"></i> Salir
                </a>
            </li>
            </ul>
                </div><!-- /.offcanvas-body -->
            </nav>

            <!-- Main -->
            <main class="col-md-10 ms-sm-auto main-content">
                <!-- Buscador global -->
                <div class="position-relative mb-3" id="globalSearchWrap" style="max-width: 480px;">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" id="globalSearch" class="form-control" placeholder="Buscar páginas, medios, productos, imágenes..." autocomplete="off">
                    </div>
                    <div id="globalSearchResults" class="dropdown-menu w-100 shadow-sm"
                        style="display:none; max-height: 420px; overflow-y: auto;"></div>
                </div>
                <!-- Alertas flash globales -->
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill"></i> <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i> <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <?= $this->renderSection('content') ?>
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->renderSection('js') ?>
    <script>
        (function() {
            const TIMEOUT_MS = <?= max(1, (int) (session()->get('lock_timeout') ?: 15)) * 60 * 1000 ?>;

            let timer = null;
            let locked = false;

            function goLock() {
                const returnUrl = encodeURIComponent(window.location.pathname + window.location.search);
                window.location.href = '<?= base_url('admin/lock') ?>?return=' + returnUrl;
            }

            function showScreensaver() {
                if (locked) return;
                locked = true;

                const ov = document.createElement('div');
                ov.style.cssText = 'position:fixed;inset:0;z-index:99999;' +
                    'background:linear-gradient(135deg,#2c3e50,#4ca1af);' +
                    'display:flex;align-items:center;justify-content:center;cursor:pointer;';
                ov.innerHTML =
                    '<div class="text-center text-white">' +
                    '  <i class="bi bi-lock" style="font-size:5rem;"></i>' +
                    '  <p class="mt-3 fs-5 mb-0">Pantalla bloqueada</p>' +
                    '  <p class="text-white-50 small">Pulsa cualquier tecla o mueve el ratón para desbloquear</p>' +
                    '</div>';
                document.body.appendChild(ov);

                // Cualquier interacción → página de desbloqueo
                ['mousemove', 'mousedown', 'keydown', 'click', 'touchstart'].forEach(evt =>
                    document.addEventListener(evt, goLock, {
                        once: true
                    })
                );
            }

            function reset() {
                if (locked) return;
                clearTimeout(timer);
                timer = setTimeout(showScreensaver, TIMEOUT_MS);
            }

            // Actividad del usuario reinicia la cuenta atrás
            ['mousemove', 'mousedown', 'keydown', 'scroll', 'touchstart'].forEach(evt =>
                document.addEventListener(evt, reset, {
                    passive: true
                })
            );

            reset();
        })();

        (function() {
            const badge = document.getElementById('submissionsBadge');
            if (!badge) return; // no estamos en el admin o no hay menú de envíos

            let lastCount = null;
            const INTERVAL = 30000; // 30 segundos

            function updateBadge(count) {
                count = parseInt(count) || 0;
                if (count > 0) {
                    badge.textContent = count > 99 ? '99+' : count;
                    badge.classList.remove('d-none');
                } else {
                    badge.classList.add('d-none');
                }
            }

            function showToast(count) {
                let toastEl = document.getElementById('notifToast');
                if (!toastEl) {
                    toastEl = document.createElement('div');
                    toastEl.id = 'notifToast';
                    toastEl.className = 'toast align-items-center text-bg-primary border-0 position-fixed bottom-0 end-0 m-3';
                    toastEl.setAttribute('role', 'alert');
                    toastEl.innerHTML =
                        '<div class="d-flex">' +
                        '  <div class="toast-body" id="notifToastBody"></div>' +
                        '  <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>' +
                        '</div>';
                    document.body.appendChild(toastEl);
                }

                document.getElementById('notifToastBody').innerHTML =
                    '<i class="bi bi-envelope-fill"></i> ' +
                    (count === 1 ? 'Tienes 1 envío nuevo' : 'Tienes ' + count + ' envíos sin leer') +
                    ' — <a href="<?= base_url('admin/submissions') ?>" class="text-white fw-bold">Ver</a>';

                bootstrap.Toast.getOrCreateInstance(toastEl, {
                    delay: 8000
                }).show();
            }

            async function poll() {
                try {
                    const res = await fetch('<?= base_url('admin/notifications/unread') ?>', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                    });
                    const data = await res.json();
                    updateBadge(data.unread);

                    // Solo avisar cuando AUMENTA (no al cargar la página)
                    if (lastCount !== null && data.unread > lastCount) {
                        showToast(data.unread);
                    }
                    lastCount = data.unread;
                } catch (e) {
                    // Sin conexión o sesión caducada: se reintenta en el siguiente ciclo
                }
            }

            poll(); // primera comprobación al cargar
            setInterval(poll, INTERVAL);
        })();

        // === Buscador global ===
        $(function() {
            const $input = $('#globalSearch');
            const $results = $('#globalSearchResults');
            let timer = null;
            let urls = [];

            function esc(s) {
                return $('<div>').text(s ?? '').html();
            }

            function render(data) {
                urls = [];
                const groups = [{
                        key: 'pages',
                        label: 'Páginas',
                        icon: 'bi-file-earmark-text'
                    },
                    {
                        key: 'media',
                        label: 'Medios',
                        icon: 'bi-images'
                    },
                    {
                        key: 'products',
                        label: 'Productos',
                        icon: 'bi-box-seam'
                    },
                    {
                        key: 'gallery',
                        label: 'Galería',
                        icon: 'bi-image'
                    },
                ];
                let html = '';
                groups.forEach(g => {
                    const items = data[g.key] || [];
                    if (!items.length) return;
                    html += '<h6 class="dropdown-header">' + g.label + '</h6>';
                    items.forEach(it => {
                        urls.push(it.url);
                        html += '<a class="dropdown-item" href="' + it.url + '">' +
                            '<i class="bi ' + g.icon + ' text-muted me-2"></i>' +
                            '<span class="text-truncate">' + esc(it.label) + '</span>' +
                            (it.meta ? '<small class="text-muted ms-2">' + esc(it.meta) + '</small>' : '') +
                            '</a>';
                    });
                });
                if (!html) html = '<span class="dropdown-item-text text-muted">Sin resultados</span>';
                $results.html(html).show();
            }

            $input.on('input', function() {
                clearTimeout(timer);
                const q = $(this).val().trim();
                if (q.length < 2) {
                    $results.hide();
                    return;
                }
                timer = setTimeout(async () => {
                    try {
                        const res = await fetch(ADMIN_URL + 'search?q=' + encodeURIComponent(q), {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        render(await res.json());
                    } catch (e) {
                        $results.hide();
                    }
                }, 300); // debounce
            });

            $input.on('keydown', function(e) {
                if (e.key === 'Enter' && urls.length) window.location.href = urls[0];
                if (e.key === 'Escape') {
                    $results.hide();
                    $(this).blur();
                }
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('#globalSearchWrap').length) $results.hide();
            });

            // === Toggle tema claro/oscuro ===
            (function() {
                const btn = document.getElementById('themeToggle');
                if (!btn) return;

                const icon = btn.querySelector('i');

                function apply(theme) {
                    document.documentElement.setAttribute('data-bs-theme', theme);
                    icon.className = theme === 'dark' ? 'bi bi-sun' : 'bi bi-moon-stars';
                    try {
                        localStorage.setItem('admin-theme', theme);
                    } catch (e) {}
                }

                // Estado inicial (puede haberlo fijado el script del <head>)
                apply(document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light');

                btn.addEventListener('click', () => {
                    apply(document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark');
                });
            })();
        });
        // Convierte cualquier [data-post] en un POST con CSRF + confirmación opcional
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('[data-post]');
            if (!btn) return;
            e.preventDefault();
            if (btn.dataset.confirm && !confirm(btn.dataset.confirm)) return;

            const form = document.createElement('form');
            form.method = 'post';
            form.action = btn.dataset.post;
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = document.querySelector('meta[name="csrf-token-name"]').content;
            input.value = document.querySelector('meta[name="csrf-token"]').content;
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        });
    </script>
</body>

</html>