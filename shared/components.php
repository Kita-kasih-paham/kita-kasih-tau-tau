<?php

function renderHead(string $title = ''): void
{
    $appName = APP_NAME;
    $pageTitle = $title ? "$title — $appName" : $appName;
    echo <<<HTML
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{$pageTitle}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
        <link rel="stylesheet" href="/assets/css/app.css">
        <link rel="stylesheet" href="/assets/css/datatables.css">
    </head>
    <body>
    HTML;
}

function renderSidebar(): void
{
    $current = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $nav = [
        ['href' => '/dashboard', 'icon' => 'bi-speedometer2', 'label' => 'Dashboard'],
        ['href' => '/barang', 'icon' => 'bi-box-seam', 'label' => 'Data Barang'],
        ['href' => '/stok-masuk', 'icon' => 'bi-box-arrow-in-down', 'label' => 'Stok Masuk'],
        ['href' => '/stok-keluar', 'icon' => 'bi-box-arrow-up', 'label' => 'Stok Keluar'],
        ['href' => '/stok-tersedia', 'icon' => 'bi-clipboard-data', 'label' => 'Stok Tersedia'],
        ['href' => '/report', 'icon' => 'bi-file-earmark-bar-graph', 'label' => 'Report'],
    ];

    $user = $_SESSION['user']['username'] ?? 'User';
    $initial = strtoupper(substr($user, 0, 1));

    echo '<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>';
    echo '<aside class="sidebar" id="sidebar">';
    echo '<div class="sidebar-brand">';
    echo '<div class="brand-icon"><i class="bi bi-box-seam"></i></div>';
    echo '<span>Barang</span>';
    echo '</div>';
    echo '<div class="sidebar-section">Menu</div>';
    echo '<nav class="sidebar-nav">';
    foreach ($nav as $item) {
        $active = str_starts_with($current, $item['href']) ? 'active' : '';
        echo "<a href=\"{$item['href']}\" class=\"nav-link {$active}\"><i class=\"bi {$item['icon']}\"></i> {$item['label']}</a>";
    }
    echo '</nav>';
    echo '<div class="sidebar-section">Akun</div>';
    $profileActive = str_starts_with($current, '/profile') ? 'active' : '';
    echo "<a href=\"/profile\" class=\"nav-link {$profileActive}\"><i class=\"bi bi-person-gear\"></i> Ganti Password</a>";
    echo '<div class="sidebar-footer">';
    echo '<a href="#" class="nav-link" onclick="confirmLogout()"><i class="bi bi-box-arrow-left"></i> Logout</a>';
    echo '</div>';
    echo '</aside>';
}

function renderTopbar(string $title): void
{
    $user = $_SESSION['user']['username'] ?? 'User';
    $initial = strtoupper(substr($user, 0, 1));
    echo <<<HTML
    <header class="topbar">
        <div class="d-flex align-items-center">
            <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="Toggle menu">
                <i class="bi bi-list"></i>
            </button>
            <span class="topbar-title">{$title}</span>
        </div>
        <div class="topbar-user" style="position:relative">
            <div class="avatar-btn d-flex align-items-center gap-2"
                 onclick="toggleUserMenu(event)"
                 style="cursor:pointer;user-select:none;padding:0.25rem 0.5rem;border-radius:8px;transition:background 0.15s"
                 onmouseover="this.style.background='rgba(255,255,255,0.08)'"
                 onmouseout="this.style.background='transparent'">
                <div class="avatar">{$initial}</div>
                <span class="d-none d-sm-inline">{$user}</span>
                <i class="bi bi-chevron-down d-none d-sm-inline" style="font-size:0.7rem;opacity:0.7;transition:transform 0.2s" id="userChevron"></i>
            </div>

            <!-- Dropdown menu -->
            <div id="userDropdown"
                 style="display:none;position:absolute;top:calc(100% + 8px);right:0;
                        background:#fff;border:1px solid #e5e7eb;border-radius:10px;
                        box-shadow:0 8px 24px rgba(0,0,0,0.12);min-width:180px;z-index:9999;overflow:hidden">
                <!-- User info header -->
                <div style="padding:0.75rem 1rem;background:#f8fafc;border-bottom:1px solid #e5e7eb">
                    <div style="font-size:0.7rem;color:#9ca3af;text-transform:uppercase;letter-spacing:0.5px;font-weight:600">Login sebagai</div>
                    <div style="font-weight:600;color:#1f2937;font-size:0.9rem;margin-top:0.1rem">{$user}</div>
                </div>
                <!-- Menu items -->
                <div style="padding:0.375rem 0">
                    <a href="/profile"
                       style="display:flex;align-items:center;gap:0.6rem;padding:0.55rem 1rem;
                              color:#374151;text-decoration:none;font-size:0.875rem;transition:background 0.15s"
                       onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='transparent'">
                        <i class="bi bi-key" style="color:#6b7280;font-size:0.9rem"></i>
                        Ganti Password
                    </a>
                    <div style="height:1px;background:#f3f4f6;margin:0.25rem 0"></div>
                    <a href="#"
                       onclick="closeUserMenu();confirmLogout();return false;"
                       style="display:flex;align-items:center;gap:0.6rem;padding:0.55rem 1rem;
                              color:#dc2626;text-decoration:none;font-size:0.875rem;transition:background 0.15s"
                       onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='transparent'">
                        <i class="bi bi-box-arrow-left" style="font-size:0.9rem"></i>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </header>
    HTML;
}

function renderFlashToast(): void
{
    $success = \Core\Flash::get('success');
    $error = \Core\Flash::get('error');
    if (!$success && !$error)
        return;

    $type = $success ? 'success' : 'danger';
    $icon = $success ? 'bi-check-circle-fill' : 'bi-x-circle-fill';
    $message = htmlspecialchars($success ?? $error);

    echo <<<HTML
    <div id="flashToast" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index:9999">
        <div class="toast show align-items-center text-bg-{$type} border-0 shadow" role="alert">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <i class="bi {$icon}"></i> {$message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
    HTML;
}

function renderLayout(string $title, string $content): void
{
    renderHead($title);
    echo '<div class="app-wrapper">';
    renderSidebar();
    echo '<div class="main-content">';
    renderTopbar($title);
    echo '<main>' . $content . '</main>';
    echo '</div></div>';
    renderFlashToast();
    echo <<<HTML
    <!-- Global Confirm Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow">
                <div class="modal-body text-center p-4">
                    <div class="mb-3" style="font-size:2.5rem;color:#e02424">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <p class="fw-600 mb-1" id="confirmTitle">Hapus Data?</p>
                    <p class="text-muted mb-4" style="font-size:0.85rem" id="confirmMessage">
                        Tindakan ini tidak dapat dibatalkan.
                    </p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button class="btn btn-light border px-4" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-danger px-4" id="confirmBtn">Hapus</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Logout form -->
    <form id="logoutForm" action="/logout" method="GET" style="display:none"></form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script>
        // Sidebar toggle
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.getElementById('sidebarOverlay').classList.toggle('open');
        }
        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('sidebarOverlay').classList.remove('open');
        }

        // User dropdown
        function toggleUserMenu(e) {
            e.stopPropagation();
            const dd       = document.getElementById('userDropdown');
            const chevron  = document.getElementById('userChevron');
            const isOpen   = dd.style.display !== 'none';
            dd.style.display  = isOpen ? 'none' : 'block';
            if (chevron) chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
        }
        function closeUserMenu() {
            const dd      = document.getElementById('userDropdown');
            const chevron = document.getElementById('userChevron');
            if (dd) dd.style.display = 'none';
            if (chevron) chevron.style.transform = 'rotate(0deg)';
        }
        // Close dropdown when clicking outside
        document.addEventListener('click', function() { closeUserMenu(); });

        // Auto-dismiss toast after 4s
        document.addEventListener('DOMContentLoaded', () => {
            const toastEl = document.querySelector('#flashToast .toast');
            if (toastEl) {
                setTimeout(() => {
                    bootstrap.Toast.getOrCreateInstance(toastEl).hide();
                }, 4000);
            }
        });

        // Global confirm modal
        // Usage: <button onclick="confirmDelete(this)" data-form="formId" data-message="...">
        function confirmDelete(btn) {
            const formId  = btn.dataset.form;
            const message = btn.dataset.message || 'Tindakan ini tidak dapat dibatalkan.';
            const title   = btn.dataset.title   || 'Hapus Data?';

            document.getElementById('confirmTitle').textContent   = title;
            document.getElementById('confirmMessage').textContent = message;

            const modal   = new bootstrap.Modal(document.getElementById('confirmModal'));
            const confirm = document.getElementById('confirmBtn');

            confirm.onclick = () => {
                document.getElementById(formId).submit();
            };
            modal.show();
        }

        function confirmLogout() {
            document.getElementById('confirmTitle').textContent   = 'Konfirmasi Logout';
            document.getElementById('confirmMessage').textContent = 'Apakah kamu yakin ingin keluar?';

            const confirmBtn = document.getElementById('confirmBtn');
            confirmBtn.textContent = 'Logout';
            confirmBtn.className   = 'btn btn-danger px-4';
            confirmBtn.onclick     = () => document.getElementById('logoutForm').submit();

            new bootstrap.Modal(document.getElementById('confirmModal')).show();
        }
    </script>
    </body></html>
    HTML;
}
