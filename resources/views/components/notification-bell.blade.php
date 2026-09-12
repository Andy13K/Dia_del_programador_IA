<div class="relative inline-block text-left" id="notificationCenterWrapper">
    <!-- Estilos exclusivos para el parpadeo sutil difuminado y la distinción de lectura -->
    <style>
        @keyframes kinSubtleDiffuseGlow {
            0%, 100% {
                background-color: rgba(244, 63, 94, 0.05);
                box-shadow: inset 0 0 10px rgba(244, 63, 94, 0.04);
            }
            50% {
                background-color: rgba(244, 63, 94, 0.16);
                box-shadow: inset 0 0 22px rgba(244, 63, 94, 0.12);
            }
        }
        .kin-alert-unread-pulse {
            border-left: 4px solid #f43f5e !important;
            animation: kinSubtleDiffuseGlow 2.8s ease-in-out infinite;
        }
        .kin-alert-read-item {
            border-left: 4px solid #cbd5e1 !important;
            opacity: 0.72;
            transition: opacity 0.2s ease, background-color 0.2s ease;
        }
        .dark .kin-alert-read-item {
            border-left-color: #334155 !important;
        }
        .kin-alert-read-item:hover {
            opacity: 1;
        }
        /* Garantizar que la campanita nunca rote ni se desplace */
        #btnNotificationBell, #bellIcon {
            transform: none !important;
            transition: color 0.2s ease !important;
        }
    </style>

    <!-- Botón Campanita: 100% estática en su posición sin movimiento -->
    <button type="button" 
            id="btnNotificationBell" 
            onclick="toggleNotificationDropdown(event)" 
            class="kin-icon-button relative cursor-pointer" 
            aria-haspopup="true" 
            aria-expanded="false" 
            aria-label="Centro de notificaciones y alertas">
        <i data-lucide="bell" class="w-5 h-5 text-slate-600 dark:text-slate-300" id="bellIcon"></i>
        <!-- Contador Badge de alertas no leídas -->
        <span id="notificationBadge" 
              class="hidden absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-rose-600 text-white text-[10px] font-black flex items-center justify-center border-2 border-white dark:border-slate-900 shadow-sm pointer-events-none">
            0
        </span>
    </button>

    <!-- Menú Pop-up Desplegable de Notificaciones -->
    <div id="notificationDropdown" 
         class="hidden absolute right-0 top-full mt-2 w-[calc(100vw-2rem)] sm:w-96 max-w-sm bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl z-50 overflow-hidden transition-all duration-200 opacity-0 transform -translate-y-2 pointer-events-none"
         role="dialog" 
         aria-label="Bandeja de alertas recientes">
        
        <!-- Encabezado del Dropdown con métricas y botón de acción -->
        <div class="p-3.5 px-4 bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="bell-ring" class="w-4 h-4 text-rose-500"></i>
                <strong class="text-xs font-bold text-slate-900 dark:text-white">Alertas K'in Solar</strong>
                <span id="dropdownUnreadPill" class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-500/15 text-rose-600 dark:text-rose-400">
                    0 no leídas
                </span>
            </div>
            <button type="button" 
                    onclick="markAllAlertsAsRead()" 
                    id="btnMarkAllRead"
                    class="text-[11px] font-semibold text-slate-500 hover:text-amber-600 dark:text-slate-400 dark:hover:text-amber-400 transition cursor-pointer">
                Marcar todas leídas
            </button>
        </div>

        <!-- Filtros rápidos para alternar visualización (Todas / No Leídas / Leídas) -->
        <div class="px-3 py-2 bg-slate-100/60 dark:bg-slate-900/60 border-b border-slate-200/80 dark:border-slate-800/80 flex items-center gap-1.5 text-[11px]">
            <button type="button" 
                    id="tabFilterAll" 
                    onclick="setNotificationFilter('all')" 
                    class="px-2.5 py-1 rounded-lg font-bold bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-xs cursor-pointer transition">
                Todas (<span id="countAll">0</span>)
            </button>
            <button type="button" 
                    id="tabFilterUnread" 
                    onclick="setNotificationFilter('unread')" 
                    class="px-2.5 py-1 rounded-lg font-semibold text-slate-600 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 cursor-pointer transition">
                No leídas (<span id="countUnread">0</span>)
            </button>
            <button type="button" 
                    id="tabFilterRead" 
                    onclick="setNotificationFilter('read')" 
                    class="px-2.5 py-1 rounded-lg font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white cursor-pointer transition">
                Leídas (<span id="countRead">0</span>)
            </button>
        </div>

        <!-- Contenedor de la lista de alertas dinámicas -->
        <div id="notificationListContainer" class="max-h-80 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800/60 text-xs scrollbar-thin">
            <div class="p-8 text-center text-slate-400 flex flex-col items-center gap-2">
                <i data-lucide="loader" class="w-5 h-5 animate-spin"></i>
                <span>Cargando notificaciones...</span>
            </div>
        </div>

        <!-- Pie de página del Dropdown -->
        <div class="p-3 px-4 bg-slate-50 dark:bg-slate-800/60 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs">
            <span class="text-[11px] text-slate-400 font-medium">Regla RF-14 (Déficit ≥ 20%)</span>
            <a href="{{ route('alerts.index') }}" class="font-bold text-amber-600 dark:text-amber-400 hover:underline flex items-center gap-1">
                <span>Ver historial completo</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>
    </div>
</div>

<script>
(function() {
    let latestAlerts = [];
    let prevUnreadCount = 0;
    let isInitialized = false;
    let currentFilter = 'all'; // 'all', 'unread', 'read'

    // Obtener IDs de alertas ya leídas desde localStorage
    function getReadAlertIds() {
        try {
            return JSON.parse(localStorage.getItem('kin_read_alerts') || '[]');
        } catch (e) {
            return [];
        }
    }

    // Guardar IDs leídos en localStorage
    function saveReadAlertIds(ids) {
        try {
            localStorage.setItem('kin_read_alerts', JSON.stringify(ids));
        } catch (e) {}
    }

    // Reproducción de sonido armónico nativo con Web Audio API (sin dependencias externas)
    function playNotificationSound() {
        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (!AudioContext) return;
            const ctx = new AudioContext();
            const now = ctx.currentTime;

            // Tono 1 (783.99 Hz - G5)
            const osc1 = ctx.createOscillator();
            const gain1 = ctx.createGain();
            osc1.type = 'sine';
            osc1.frequency.setValueAtTime(783.99, now);
            gain1.gain.setValueAtTime(0.18, now);
            gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.28);
            osc1.connect(gain1);
            gain1.connect(ctx.destination);
            osc1.start(now);
            osc1.stop(now + 0.28);

            // Tono 2 Armónico (1046.50 Hz - C6)
            const osc2 = ctx.createOscillator();
            const gain2 = ctx.createGain();
            osc2.type = 'sine';
            osc2.frequency.setValueAtTime(1046.50, now + 0.09);
            gain2.gain.setValueAtTime(0.22, now + 0.09);
            gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.48);
            osc2.connect(gain2);
            gain2.connect(ctx.destination);
            osc2.start(now + 0.09);
            osc2.stop(now + 0.48);
        } catch (e) {}
    }

    // Consultar alertas activas en el servidor
    async function fetchNotifications() {
        try {
            const response = await fetch("{{ route('alerts.notifications') }}", {
                headers: { 'Accept': 'application/json' }
            });
            if (!response.ok) return;

            const res = await response.json();
            if (!res.success) return;

            latestAlerts = res.alerts || [];
            renderNotificationUI();
        } catch (e) {}
    }

    // Cambiar filtro de visualización en las pestañas
    window.setNotificationFilter = function(filter) {
        currentFilter = filter;
        updateTabStyles();
        renderAlertList();
    };

    function updateTabStyles() {
        const btnAll = document.getElementById('tabFilterAll');
        const btnUnread = document.getElementById('tabFilterUnread');
        const btnRead = document.getElementById('tabFilterRead');
        if (!btnAll || !btnUnread || !btnRead) return;

        const activeClass = 'px-2.5 py-1 rounded-lg font-bold bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-xs cursor-pointer transition';
        const inactiveClass = 'px-2.5 py-1 rounded-lg font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white cursor-pointer transition';

        btnAll.className = currentFilter === 'all' ? activeClass : inactiveClass;
        btnUnread.className = currentFilter === 'unread' ? activeClass : inactiveClass;
        btnRead.className = currentFilter === 'read' ? activeClass : inactiveClass;
    }

    // Renderizar la interfaz de la campanita y del dropdown
    function renderNotificationUI() {
        const readIds = getReadAlertIds();
        const unreadAlerts = latestAlerts.filter(a => !readIds.includes(a.id));
        const readAlerts = latestAlerts.filter(a => readIds.includes(a.id));
        const unreadCount = unreadAlerts.length;
        const readCount = readAlerts.length;

        // 1. Actualizar Badge con el número de alertas no atendidas
        const badge = document.getElementById('notificationBadge');
        const unreadPill = document.getElementById('dropdownUnreadPill');
        const countAll = document.getElementById('countAll');
        const countUnread = document.getElementById('countUnread');
        const countRead = document.getElementById('countRead');

        if (badge) {
            if (unreadCount > 0) {
                badge.textContent = unreadCount > 99 ? '99+' : unreadCount;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        }

        if (unreadPill) {
            unreadPill.textContent = unreadCount === 1 ? '1 no leída' : `${unreadCount} no leídas`;
            unreadPill.className = unreadCount > 0 
                ? 'px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-500/15 text-rose-600 dark:text-rose-400' 
                : 'px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/15 text-emerald-600 dark:text-emerald-400';
            if (unreadCount === 0) {
                unreadPill.textContent = 'Al día';
            }
        }

        if (countAll) countAll.textContent = latestAlerts.length;
        if (countUnread) countUnread.textContent = unreadCount;
        if (countRead) countRead.textContent = readCount;

        // 2. Si aumentó el número de alertas no atendidas, reproducir el sonido armónico
        // NOTA: La campanita NO se mueve ni gira para respetar la posición fija solicitada
        if (isInitialized && unreadCount > prevUnreadCount) {
            playNotificationSound();
        }
        prevUnreadCount = unreadCount;
        isInitialized = true;

        updateTabStyles();
        renderAlertList();
    }

    // Renderizar la lista de alertas según el filtro seleccionado y su estado de lectura
    function renderAlertList() {
        const container = document.getElementById('notificationListContainer');
        if (!container) return;

        const readIds = getReadAlertIds();
        
        // Separar y ordenar: primero las no leídas, luego las leídas
        const unreadAlerts = latestAlerts.filter(a => !readIds.includes(a.id));
        const readAlerts = latestAlerts.filter(a => readIds.includes(a.id));

        let displayAlerts = [];
        if (currentFilter === 'unread') {
            displayAlerts = unreadAlerts;
        } else if (currentFilter === 'read') {
            displayAlerts = readAlerts;
        } else {
            // En "Todas", poner las NO leídas primero para máxima visibilidad
            displayAlerts = [...unreadAlerts, ...readAlerts];
        }

        if (displayAlerts.length === 0) {
            let emptyMsg = 'Sin alertas registradas.';
            if (currentFilter === 'unread') {
                emptyMsg = 'No tienes alertas pendientes por revisar.';
            } else if (currentFilter === 'read') {
                emptyMsg = 'Aún no has marcado alertas como leídas.';
            }

            container.innerHTML = `
                <div class="p-8 text-center text-slate-400 flex flex-col items-center gap-2">
                    <div class="w-10 h-10 rounded-full bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                        <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                    </div>
                    <strong class="text-xs text-slate-700 dark:text-slate-200">Bandeja despejada</strong>
                    <span class="text-[11px]">${emptyMsg}</span>
                </div>
            `;
            window.lucide?.createIcons();
            return;
        }

        container.innerHTML = displayAlerts.map(alert => {
            const isUnread = !readIds.includes(alert.id);
            const deviation = Number(alert.deviation_percentage || 0).toFixed(1);

            if (isUnread) {
                // ==========================================
                // ALERTA NO LEÍDA: Borde rojo, parpadeo sutil difuminado y destaque
                // ==========================================
                return `
                    <div class="p-3.5 px-4 kin-alert-unread-pulse transition-all duration-200">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-start gap-2.5 min-w-0">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h5 class="font-bold text-xs text-slate-900 dark:text-white truncate">
                                            ${escapeHtml(alert.farm_name)}
                                        </h5>
                                        <!-- Insignia visual destacada: NO LEÍDA -->
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-black uppercase tracking-wider bg-rose-600 text-white shadow-xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                                            Nueva
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-slate-600 dark:text-slate-300 font-medium mt-0.5">
                                        ${escapeHtml(alert.department_name)} · Período: <strong>${escapeHtml(alert.period)}</strong>
                                    </p>
                                    <p class="text-[11px] text-slate-700 dark:text-slate-200 mt-1 line-clamp-2">
                                        ${escapeHtml(alert.notes)}
                                    </p>
                                </div>
                            </div>
                            <span class="flex-shrink-0 px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-500/20 text-rose-700 dark:text-rose-300 border border-rose-500/40">
                                −${deviation}%
                            </span>
                        </div>
                        <div class="mt-2.5 flex items-center justify-between pt-2 border-t border-rose-200/50 dark:border-rose-900/40">
                            <span class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">${escapeHtml(alert.created_at_human)}</span>
                            <a href="${escapeHtml(alert.show_url)}" 
                               onclick="markAlertAsRead(${alert.id})" 
                               class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-bold bg-amber-500 hover:bg-amber-600 text-slate-950 transition shadow-sm cursor-pointer active:scale-95">
                                <span>Más detalles</span>
                                <i data-lucide="arrow-up-right" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                    </div>
                `;
            } else {
                // ==========================================
                // ALERTA LEÍDA: Atenuada, estática, sin parpadeo, badge "Leída"
                // ==========================================
                return `
                    <div class="p-3.5 px-4 kin-alert-read-item bg-slate-50/50 dark:bg-slate-900/30">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-start gap-2.5 min-w-0">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h5 class="font-medium text-xs text-slate-700 dark:text-slate-300 truncate">
                                            ${escapeHtml(alert.farm_name)}
                                        </h5>
                                        <!-- Insignia visual atenuada: LEÍDA -->
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-semibold bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-300 dark:border-slate-700">
                                            <i data-lucide="check" class="w-2.5 h-2.5 text-emerald-500"></i>
                                            Leída
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">
                                        ${escapeHtml(alert.department_name)} · Período: ${escapeHtml(alert.period)}
                                    </p>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 line-clamp-2">
                                        ${escapeHtml(alert.notes)}
                                    </p>
                                </div>
                            </div>
                            <span class="flex-shrink-0 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                −${deviation}%
                            </span>
                        </div>
                        <div class="mt-2.5 flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800/50">
                            <span class="text-[10px] text-slate-400">${escapeHtml(alert.created_at_human)}</span>
                            <a href="${escapeHtml(alert.show_url)}" 
                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition cursor-pointer">
                                <span>Ver detalles</span>
                                <i data-lucide="arrow-up-right" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                    </div>
                `;
            }
        }).join('');

        window.lucide?.createIcons();
    }

    // Marcar una alerta específica como leída (al hacer clic en "Más detalles")
    window.markAlertAsRead = function(alertId) {
        const readIds = getReadAlertIds();
        if (!readIds.includes(alertId)) {
            readIds.push(alertId);
            saveReadAlertIds(readIds);
        }
        renderNotificationUI();
    };

    // Marcar todas las alertas activas como leídas
    window.markAllAlertsAsRead = function() {
        const readIds = getReadAlertIds();
        latestAlerts.forEach(a => {
            if (!readIds.includes(a.id)) {
                readIds.push(a.id);
            }
        });
        saveReadAlertIds(readIds);
        renderNotificationUI();
    };

    // Abrir o cerrar el menú pop-up de notificaciones
    window.toggleNotificationDropdown = function(event) {
        event?.stopPropagation();
        const dropdown = document.getElementById('notificationDropdown');
        const trigger = document.getElementById('btnNotificationBell');
        if (!dropdown || !trigger) return;

        const isOpen = !dropdown.classList.contains('hidden');

        // Cerrar menú de usuario si está abierto
        document.querySelectorAll('.kin-user-menu.is-open').forEach(m => m.classList.remove('is-open'));

        if (isOpen) {
            dropdown.classList.add('opacity-0', '-translate-y-2', 'pointer-events-none');
            setTimeout(() => dropdown.classList.add('hidden'), 150);
            trigger.setAttribute('aria-expanded', 'false');
        } else {
            dropdown.classList.remove('hidden');
            setTimeout(() => {
                dropdown.classList.remove('opacity-0', '-translate-y-2', 'pointer-events-none');
            }, 10);
            trigger.setAttribute('aria-expanded', 'true');
        }
    };

    // Cerrar al hacer clic afuera o con la tecla Escape
    document.addEventListener('click', function(event) {
        const wrapper = document.getElementById('notificationCenterWrapper');
        const dropdown = document.getElementById('notificationDropdown');
        if (dropdown && !dropdown.classList.contains('hidden') && wrapper && !wrapper.contains(event.target)) {
            dropdown.classList.add('opacity-0', '-translate-y-2', 'pointer-events-none');
            setTimeout(() => dropdown.classList.add('hidden'), 150);
            document.getElementById('btnNotificationBell')?.setAttribute('aria-expanded', 'false');
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const dropdown = document.getElementById('notificationDropdown');
            if (dropdown && !dropdown.classList.contains('hidden')) {
                dropdown.classList.add('opacity-0', '-translate-y-2', 'pointer-events-none');
                setTimeout(() => dropdown.classList.add('hidden'), 150);
                document.getElementById('btnNotificationBell')?.setAttribute('aria-expanded', 'false');
            }
        }
    });

    // Sanitización contra XSS
    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/[&<>"']/g, function(m) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m];
        });
    }

    // Exponer función global para refrescar de inmediato tras un evento de simulación
    window.refreshNotifications = fetchNotifications;

    // Inicializar y consultar cada 5 segundos
    fetchNotifications();
    setInterval(fetchNotifications, 5000);
})();
</script>
