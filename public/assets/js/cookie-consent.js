(function () {
    'use strict';

    const COOKIE_NAME = 'ainvar_consent';
    const banner = document.getElementById('cookieBanner');   // puede no existir (ya consentido)
    const modalEl = document.getElementById('cookieModal');
    const modal = modalEl ? new bootstrap.Modal(modalEl) : null;

    function getConsent() {
        const m = document.cookie.match(new RegExp('(?:^|; )' + COOKIE_NAME + '=([^;]*)'));
        if (!m) return null;
        try { return JSON.parse(decodeURIComponent(m[1])); } catch (e) { return null; }
    }

    // Activa scripts diferidos: clona el <script type="text/plain"> y lo ejecuta.
    // El ORIGINAL se conserva inerte como plantilla, para poder reactivar tras una revocación.
    function applyConsent(c) {
        document.querySelectorAll('script[type="text/plain"][data-cookieconsent]').forEach(function (s) {
            const cat = s.dataset.cookieconsent;
            if (!c || !c[cat] || s.dataset.activated) return;
            s.dataset.activated = '1';
            const ns = document.createElement('script');
            Array.from(s.attributes).forEach(function (a) {
                if (a.name !== 'type' && a.name !== 'data-activated') {
                    ns.setAttribute(a.name, a.value);
                }
            });
            ns.removeAttribute('type');           // sin type → JavaScript
            ns.dataset.consentClone = '1';        // marca para poder apagarlo
            ns.text = s.text;
            s.parentNode.insertBefore(ns, s.nextSibling);
        });
    }

    // Apaga las categorías que ya NO están consentidas
    function revokeConsent(c) {
        document.querySelectorAll('script[data-cookieconsent]').forEach(function (s) {
            const cat = s.dataset.cookieconsent;
            if (!cat || (c && c[cat])) return;    // consentida: no tocar
            if (s.dataset.consentClone) {
                s.remove();                        // apaga el script activado
            } else {
                s.removeAttribute('data-activated'); // deja la plantilla lista por si re-consiente
            }
        });
        if (c && !c.analytics) {
            if (window.gtag) {
                gtag('consent', 'update', { analytics_storage: 'denied' });
            }
            deleteCookiesByPrefix(['_ga', '_gid', '_gat']);
        }
    }

    function deleteCookiesByPrefix(prefixes) {
        const domains = [location.hostname, '.' + location.hostname];
        document.cookie.split(';').forEach(function (c) {
            const name = c.split('=')[0].trim();
            if (!prefixes.some(function (p) { return name.indexOf(p) === 0; })) return;
            domains.forEach(function (d) {
                document.cookie = name + '=;path=/;max-age=0;domain=' + d;
            });
            document.cookie = name + '=;path=/;max-age=0';
        });
    }

    function hideBanner() {
        if (banner) banner.remove();
        if (modal) modal.hide();
    }

    let existing = getConsent();

    async function save(consent) {
        try {
            const res = await fetch(window.CC_BASE + 'cookies/consent', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': window.CC_CSRF
                },
                body: JSON.stringify(consent)
            });

            if (!res.ok) {
                const t = await res.text();
                console.error('[consent] HTTP ' + res.status, t.substring(0, 300));
                alert('No se pudo guardar el consentimiento (HTTP ' + res.status + '). Mira la consola.');
                return; // banner NO se cierra si falla
            }

            const data = await res.json();
            if (!data.consent) {
                console.error('[consent] respuesta sin data.consent:', data);
                alert('Respuesta inesperada del servidor. Mira la consola.');
                return;
            }

            if (data.csrf) window.CC_CSRF = data.csrf;
            document.cookie = COOKIE_NAME + '=' + encodeURIComponent(JSON.stringify(data.consent))
                + ';path=/;max-age=31536000;SameSite=Lax';
            revokeConsent(data.consent);
            applyConsent(data.consent);
            existing = data.consent;
            hideBanner();   // solo se cierra si todo fue bien
        } catch (e) {
            console.error('Consent save failed', e);
            alert('Error de red guardando el consentimiento. Mira la consola.');
        }
    }

    applyConsent(existing);   // en cada página, activa lo ya consentido

    function on(id, fn) { const el = document.getElementById(id); if (el) el.addEventListener('click', fn); }

    on('cookieAccept', () => save({ token: existing?.token, analytics: 1, marketing: 1 }));
    on('cookieReject', () => save({ token: existing?.token, analytics: 0, marketing: 0 }));
    on('cookieAcceptAll', () => save({ token: existing?.token, analytics: 1, marketing: 1 }));
    on('cookieConfigure', () => {
        const c = getConsent();
        document.getElementById('swAnalytics').checked = !!(c && c.analytics);
        document.getElementById('swMarketing').checked = !!(c && c.marketing);
        if (modal) modal.show();
    });
    on('cookieSaveChoice', () => save({
        token: existing?.token,
        analytics: document.getElementById('swAnalytics').checked ? 1 : 0,
        marketing: document.getElementById('swMarketing').checked ? 1 : 0
    }));

    window.CookieConsent = {
        open: function () {
            if (!modal) return;
            const c = getConsent();
            document.getElementById('swAnalytics').checked = !!(c && c.analytics);
            document.getElementById('swMarketing').checked = !!(c && c.marketing);
            modal.show();
        }
    };
})();