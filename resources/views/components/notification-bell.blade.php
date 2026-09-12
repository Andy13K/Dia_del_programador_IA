<div class="relative inline-block text-left" id="notificationCenterWrapper">
    <!-- Botón Campanita con Contador Badge -->
    <button type="button" 
            id="btnNotificationBell" 
            onclick="toggleNotificationDropdown(event)" 
            class="kin-icon-button relative transition-transform active:scale-95 cursor-pointer" 
            aria-haspopup="true" 
            aria-expanded="false" 
            aria-label="Centro de notificaciones y alertas">
        <i data-lucide="bell" class="w-5 h-5 text-slate-600 dark:text-slate-300 transition-transform duration-300" id="bellIcon"></i>
        <!-- Contador de alertas no atendidas -->
        <span id="notificationBadge" 
              class="hidden absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-rose-600 text-white text-[10px] font-black flex items-center justify-center border-2 border-white dark:border-slate-900 shadow-sm animate-pulse">
            0
        </span>
    </button>

    <!-- Menú Pop-up Desplegable de Notificaciones -->
    <div id="notificationDropdown" 
         class="hidden absolute right-0 top-full mt-2 w-[calc(100vw-2rem)] sm:w-96 max-w-sm bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl z-50 overflow-hidden transition-all duration-200 opacity-0 transform -translate-y-2 pointer-events-none"
         role="dialog" 
         aria-label="Bandeja de alertas recientes">
        
        <!-- Encabezado del Dropdown -->
        <div class="p-3.5 px-4 bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="bell-ring" class="w-4 h-4 text-rose-500"></i>
                <strong class="text-xs font-bold text-slate-900 dark:text-white">Alertas y Notificaciones</strong>
                <span id="dropdownUnreadPill" class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-500/15 text-rose-600 dark:text-rose-400">
                    0 nuevas
                </span>
            </div>
            <button type="button" 
                    onclick="markAllAlertsAsRead()" 
                    class="text-[11px] font-semibold text-slate-500 hover:text-amber-600 dark:text-slate-400 dark:hover:text-amber-400 transition cursor-pointer">
                Marcar todas leídas
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
                <span>Ver todas</span>
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

    // Reproducción de sonido armónico nativo con Web Audio API (cero dependencias externas)
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

    // Renderizar la interfaz de la campanita y del dropdown
    function renderNotificationUI() {
        const readIds = getReadAlertIds();
        const unreadAlerts = latestAlerts.filter(a => !readIds.includes(a.id));
        const unreadCount = unreadAlerts.length;

        // 1. Actualizar Badge con el numerito
        const badge = document.getElementById('notificationBadge');
        const bellIcon = document.getElementById('bellIcon');
        const unreadPill = document.getElementById('dropdownUnreadPill');

        if (badge) {
            if (unreadCount > 0) {
                badge.textContent = unreadCount > 99 ? '99+' : unreadCount;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        }

        if (unreadPill) {
            unreadPill.textContent = unreadCount === 1 ? '1 nueva' : `${unreadCount} nuevas`;
            unreadPill.className = unreadCount > 0 
                ? 'px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-500/15 text-rose-600 dark:text-rose-400' 
                : 'px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-500';
        }

        // 2. Si aumentó el número de alertas no atendidas, hacer sonar la campana y animarla
        if (isInitialized && unreadCount > prevUnreadCount) {
            playNotificationSound();
            if (bellIcon) {
                bellIcon.classList.add('rotate-12', 'scale-125', 'text-rose-600');
                setTimeout(() => {
                    bellIcon.classList.remove('rotate-12', 'scale-125', 'text-rose-600');
                }, 1000);
            }
        }
        prevUnreadCount = unreadCount;
        isInitialized = true;

        // 3. Renderizar items en el contenedor del dropdown
        const container = document.getElementById('notificationListContainer');
        if (!container) return;

        if (latestAlerts.length === 0) {
            container.innerHTML = `
                <div class="p-8 text-center text-slate-400 flex flex-col items-center gap-2">
                    <div class="w-10 h-10 rounded-full bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                        <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                    </div>
                    <strong class="text-xs text-slate-700 dark:text-slate-200">Sin alertas pendientes</strong>
                    <span class="text-[11px]">Todas las granjas solares operan dentro de los márgenes nominales.</span>
                </div>
            `;
            window.lucide?.createIcons();
            return;
        }

        container.innerHTML = latestAlerts.map(alert => {
            const isUnread = !readIds.includes(alert.id);
            const deviation = Number(alert.deviation_percentage || 0).toFixed(1);

            return `
                <div class="p-3.5 px-4 transition-colors duration-150 ${isUnread ? 'bg-rose-500/5 dark:bg-rose-950/20' : 'hover:bg-slate-50 dark:hover:bg-slate-800/40'}">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-start gap-2.5 min-w-0">
                            <span class="mt-1 flex-shrink-0 w-2 h-2 rounded-full ${isUnread ? 'bg-rose-600 animate-ping' : 'bg-transparent'}"></span>
                            <div class="min-w-0">
                                <h5 class="font-bold text-xs text-slate-900 dark:text-white truncate">
                                    ${escapeHtml(alert.farm_name)}
                                </h5>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                    ${escapeHtml(alert.department_name)} · Período: <strong>${escapeHtml(alert.period)}</strong>
                                </p>
                                <p class="text-[11px] text-slate-600 dark:text-slate-300 mt-1 line-clamp-2">
                                    ${escapeHtml(alert.notes)}
                                </p>
                            </div>
                        </div>
                        <span class="flex-shrink-0 px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-500/15 text-rose-700 dark:text-rose-400 border border-rose-500/30">
                            −${deviation}%
                        </span>
                    </div>
                    <div class="mt-2.5 flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800/50">
                        <span class="text-[10px] text-slate-400">${escapeHtml(alert.created_at_human)}</span>
                        <a href="${escapeHtml(alert.show_url)}" 
                           onclick="markAlertAsRead(${alert.id})" 
                           class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-500 hover:bg-amber-600 text-slate-950 transition shadow-sm cursor-pointer">
                            <span>Más detalles</span>
                            <i data-lucide="arrow-up-right" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>
                </div>
            `;
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
