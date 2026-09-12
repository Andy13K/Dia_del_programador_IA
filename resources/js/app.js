const icons = () => window.lucide?.createIcons();
window.toggleUserMenu = (button) => {
    const menu = button.closest('.kin-user-menu');
    const willOpen = !menu.classList.contains('is-open');
    document.querySelectorAll('.kin-user-menu.is-open').forEach(open => {
        open.classList.remove('is-open');
        open.querySelector('.kin-user-trigger')?.setAttribute('aria-expanded', 'false');
    });
    if (willOpen) {
        menu.classList.add('is-open');
        button.setAttribute('aria-expanded', 'true');
    }
};
document.addEventListener('click', event => {
    document.querySelectorAll('.kin-user-menu.is-open').forEach(menu => {
        if (!menu.contains(event.target)) {
            menu.classList.remove('is-open');
            menu.querySelector('.kin-user-trigger')?.setAttribute('aria-expanded', 'false');
        }
    });
});
document.addEventListener('keydown', event => {
    if (event.key !== 'Escape') return;
    document.querySelectorAll('.kin-user-menu.is-open').forEach(menu => {
        menu.classList.remove('is-open');
        menu.querySelector('.kin-user-trigger')?.setAttribute('aria-expanded', 'false');
    });
});
window.togglePasswordVisibility = (id, button) => {
    const field = document.getElementById(id);
    const nowVisible = field.type === 'password';
    field.type = nowVisible ? 'text' : 'password';
    button.setAttribute('aria-pressed', String(nowVisible));
    button.setAttribute('aria-label', nowVisible ? 'Ocultar contraseña' : 'Mostrar contraseña');
    const eyeOff = button.querySelector('.kin-eye-off');
    const eyeOn = button.querySelector('.kin-eye-on');
    if (eyeOff) eyeOff.style.display = nowVisible ? 'none' : '';
    if (eyeOn) eyeOn.style.display = nowVisible ? '' : 'none';
};
window.toggleDarkMode = () => {
    const dark = document.documentElement.classList.toggle('dark');
    localStorage.setItem('theme', dark ? 'dark' : 'light');
    window.dispatchEvent(new CustomEvent('kin:theme', {detail: {dark}}));
    icons();
};
let drawerTrigger = null;
const drawer = () => document.getElementById('mobileDrawer');
window.openMobileDrawer = () => {
    if (!drawer()) return;
    drawerTrigger = document.activeElement;
    drawer().inert = false;
    drawer().classList.add('is-open');
    drawer().setAttribute('aria-hidden', 'false');
    document.getElementById('mobileDrawerBackdrop').classList.add('is-open');
    document.querySelector('[aria-controls="mobileDrawer"]')?.setAttribute('aria-expanded','true');
    document.querySelector('.kin-workspace').inert = true;
    document.getElementById('mobileBottomNav').inert = true;
    drawer().querySelector('button')?.focus();
};
window.closeMobileDrawer = () => {
    if (!drawer()?.classList.contains('is-open')) return;
    drawer().classList.remove('is-open');
    drawer().setAttribute('aria-hidden', 'true');
    drawer().inert = true;
    document.getElementById('mobileDrawerBackdrop').classList.remove('is-open');
    document.querySelector('[aria-controls="mobileDrawer"]')?.setAttribute('aria-expanded','false');
    document.querySelector('.kin-workspace').inert = false;
    document.getElementById('mobileBottomNav').inert = false;
    drawerTrigger?.focus();
};
document.addEventListener('keydown', event => {
    if (event.key === 'Escape') window.closeMobileDrawer();
    if (event.key !== 'Tab' || !drawer()?.classList.contains('is-open')) return;
    const controls = Array.from(drawer().querySelectorAll('a,button,input,select,[tabindex="0"]')).filter(el => !el.disabled);
    const first = controls[0], last = controls.at(-1);
    if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last?.focus(); }
    else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first?.focus(); }
});
matchMedia('(min-width:1024px)').addEventListener('change', event => { if(event.matches) window.closeMobileDrawer(); });
const init = () => {
    icons();
    document.querySelectorAll('table').forEach(table => {
        table.classList.add('kin-table');
        table.parentElement.classList.add('kin-table-wrap');
        const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.trim());
        table.querySelectorAll('tbody tr,tfoot tr').forEach(row => {
            let column = 0;
            Array.from(row.cells).forEach(cell => {
                if (cell.colSpan === 1 && headers[column]) cell.dataset.label = headers[column];
                column += cell.colSpan;
            });
        });
    });
    document.querySelectorAll('[data-reactive-filter]').forEach(form => {
        let timer;
        form.querySelectorAll('input[name="search"]').forEach(input => input.addEventListener('input', () => {
            clearTimeout(timer); timer = setTimeout(() => form.requestSubmit(), 700);
        }));
        form.querySelectorAll('select').forEach(select => select.addEventListener('change', () => form.requestSubmit()));
    });
    document.querySelectorAll('form[method="POST"],form[method="post"]').forEach(form => form.addEventListener('submit', event => {
        queueMicrotask(() => {
            if (event.defaultPrevented) return;
            form.setAttribute('aria-busy','true');
            form.querySelectorAll('button[type="submit"]').forEach(button => { button.disabled = true; button.style.opacity = '.65'; });
        });
    }));
    const connection = document.querySelector('.kin-live');
    const updateConnection = () => {
        if (!connection) return;
        connection.title = navigator.onLine ? 'Conexión de red disponible; datos actualizados al cargar la página' : 'Sin conexión de red';
        connection.querySelector('span:last-child').textContent = navigator.onLine ? 'Conectado' : 'Sin conexión';
    };
    updateConnection();
    window.addEventListener('online',updateConnection);
    window.addEventListener('offline',updateConnection);
};
if(document.readyState === 'loading') document.addEventListener('DOMContentLoaded',init); else init();
window.addEventListener('pageshow', () => document.querySelectorAll('form[aria-busy]').forEach(form => {
    form.removeAttribute('aria-busy');
    form.querySelectorAll('button[type="submit"]').forEach(button => { button.disabled = false; button.style.opacity = ''; });
}));
