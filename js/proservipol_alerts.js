// ============================================================
// PROSERVIPOL ALERTS — Sistema de modales modernos
// Reemplaza alert() y confirm() nativos en todo el sistema
// Vanilla JS puro — sin dependencias
// ============================================================

(function() {

    // ── ESTILOS GLOBALES ─────────────────────────────────────
    var style = document.createElement('style');
    style.textContent = [
        '.psa-overlay{position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:99999;',
        'display:flex;align-items:center;justify-content:center;',
        'opacity:0;transition:opacity .2s ease;}',
        '.psa-overlay.psa-visible{opacity:1;}',

        '.psa-box{background:#fff;border-radius:12px;padding:28px 32px;max-width:420px;width:90%;',
        'box-shadow:0 20px 60px rgba(0,0,0,.2);transform:scale(.93) translateY(8px);',
        'transition:transform .22s cubic-bezier(.34,1.3,.64,1),opacity .2s ease;opacity:0;}',
        '.psa-overlay.psa-visible .psa-box{transform:scale(1) translateY(0);opacity:1;}',

        '.psa-icon{width:72px;height:72px;display:flex;',
        'align-items:center;justify-content:center;margin:0 auto 16px;}',
        '.psa-icon svg{display:block;width:72px;height:72px;}',

        '.psa-title{font-size:16px;font-weight:700;color:#111827;text-align:center;margin-bottom:8px;}',
        '.psa-msg{font-size:13px;color:#4b5563;text-align:center;line-height:1.6;margin-bottom:20px;',
        'max-width:340px;margin-left:auto;margin-right:auto;}',

        '.psa-btns{display:flex;gap:10px;justify-content:center;}',
        '.psa-btn{padding:9px 24px;border-radius:7px;font-size:13px;font-weight:600;',
        'cursor:pointer;border:none;transition:filter .15s;}',
        '.psa-btn:hover{filter:brightness(.92);}',
        '.psa-btn-ok-success{background:#16a34a;color:#fff;}',
        '.psa-btn-ok-error{background:#dc2626;color:#fff;}',
        '.psa-btn-ok-warning{background:#ca8a04;color:#fff;}',
        '.psa-btn-ok-info{background:#2563eb;color:#fff;}',
        '.psa-btn-ok-confirm{background:#2563eb;color:#fff;}',
        '.psa-btn-cancel{background:#e5e7eb;color:#374151;}',
    ].join('');
    document.head.appendChild(style);

    // ── ÍCONOS SVG INLINE ────────────────────────────────────
    var SVG_ICONS = {

        // Círculo azul con signo de interrogación — para confirmaciones
        confirm:
            '<svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">' +
                '<circle cx="32" cy="32" r="31" fill="#2563eb"/>' +
                '<path d="M24 24c0-4.4 3.6-8 8-8s8 3.6 8 8c0 3.5-2.1 5.8-4.5 7.5C33.5 33 32 34.5 32 37"' +
                ' fill="none" stroke="#fff" stroke-width="4.5" stroke-linecap="round"/>' +
                '<circle cx="32" cy="44" r="3" fill="#fff"/>' +
            '</svg>',

        // Círculo verde con palomita — operación exitosa
        success:
            '<svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">' +
                '<circle cx="32" cy="32" r="31" fill="#32BA7C"/>' +
                '<path d="M19 33l9 9 17-19" fill="none" stroke="#fff" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>' +
            '</svg>',

        // Círculo rojo con exclamación — error
        error:
            '<svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">' +
                '<circle cx="32" cy="32" r="31" fill="#FF4141"/>' +
                '<path d="M32 16v20" stroke="#fff" stroke-width="5" stroke-linecap="round"/>' +
                '<circle cx="32" cy="45" r="3" fill="#fff"/>' +
            '</svg>',

        // Círculo naranja con exclamación — advertencia
        warning:
            '<svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">' +
                '<circle cx="32" cy="32" r="31" fill="#FFC048"/>' +
                '<path d="M32 16v20" stroke="#000" stroke-width="5" stroke-linecap="round"/>' +
                '<circle cx="32" cy="45" r="3" fill="#000"/>' +
            '</svg>',

        // Círculo celeste con info — informativo
        info:
            '<svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">' +
                '<circle cx="32" cy="32" r="31" fill="#23B0E2"/>' +
                '<circle cx="32" cy="19" r="3" fill="#fff"/>' +
                '<path d="M32 28v18" stroke="#fff" stroke-width="5" stroke-linecap="round"/>' +
            '</svg>'
    };

    // ── CONSTRUCTOR DE MODAL BASE ────────────────────────────
    function crearModal(tipo, titulo, mensaje) {
        var overlay = document.createElement('div');
        overlay.className = 'psa-overlay';

        var box = document.createElement('div');
        box.className = 'psa-box';
        box.innerHTML =
            '<div class="psa-icon">' + (SVG_ICONS[tipo] || SVG_ICONS.info) + '</div>' +
            '<div class="psa-title">' + titulo + '</div>' +
            '<div class="psa-msg">' + mensaje + '</div>' +
            '<div class="psa-btns" id="psa-btns-zona"></div>';

        overlay.appendChild(box);
        document.body.appendChild(overlay);

        overlay.getBoundingClientRect();
        overlay.classList.add('psa-visible');

        return { overlay: overlay, btnZona: box.querySelector('#psa-btns-zona') };
    }

    function cerrarModal(overlay, callback) {
        overlay.classList.remove('psa-visible');
        setTimeout(function() {
            if (overlay.parentNode) {
                overlay.parentNode.removeChild(overlay);
            }
            if (callback) callback();
        }, 220);
    }

    // ── API PÚBLICA ──────────────────────────────────────────
    window.PSAlert = {

        exito: function(mensaje, titulo) {
            var t = titulo || '¡Operación exitosa!';
            var m = crearModal('success', t, mensaje);
            var btn = document.createElement('button');
            btn.className   = 'psa-btn psa-btn-ok-success';
            btn.textContent = 'Aceptar';
            btn.onclick = function() { cerrarModal(m.overlay); };
            m.btnZona.appendChild(btn);
            setTimeout(function() { btn.focus(); }, 230);
        },

        error: function(mensaje, titulo) {
            var t = titulo || 'Ha ocurrido un error';
            var m = crearModal('error', t, mensaje);
            var btn = document.createElement('button');
            btn.className   = 'psa-btn psa-btn-ok-error';
            btn.textContent = 'Cerrar';
            btn.onclick = function() { cerrarModal(m.overlay); };
            m.btnZona.appendChild(btn);
            setTimeout(function() { btn.focus(); }, 230);
        },

        advertencia: function(mensaje, titulo) {
            var t = titulo || 'Advertencia';
            var m = crearModal('warning', t, mensaje);
            var btn = document.createElement('button');
            btn.className   = 'psa-btn psa-btn-ok-warning';
            btn.textContent = 'Entendido';
            btn.onclick = function() { cerrarModal(m.overlay); };
            m.btnZona.appendChild(btn);
            setTimeout(function() { btn.focus(); }, 230);
        },

        info: function(mensaje, titulo) {
            var t = titulo || 'Información';
            var m = crearModal('info', t, mensaje);
            var btn = document.createElement('button');
            btn.className   = 'psa-btn psa-btn-ok-info';
            btn.textContent = 'Aceptar';
            btn.onclick = function() { cerrarModal(m.overlay); };
            m.btnZona.appendChild(btn);
            setTimeout(function() { btn.focus(); }, 230);
        },

        // acepta 'tipo' como 5to parámetro opcional
        confirmar: function(mensaje, callbackSi, titulo, callbackNo, tipo) {
            var t         = titulo    || 'Confirmar acción';
            var tipoModal = tipo      || 'confirm';
            var m         = crearModal(tipoModal, t, mensaje);

            var clasesSi = {
                confirm: 'psa-btn psa-btn-ok-confirm',
                warning: 'psa-btn psa-btn-ok-warning',
                error:   'psa-btn psa-btn-ok-error',
                info:    'psa-btn psa-btn-ok-info'
            };

            var btnSi = document.createElement('button');
            btnSi.className   = clasesSi[tipoModal] || clasesSi['confirm'];
            btnSi.textContent = 'Sí, confirmar';

            var btnNo = document.createElement('button');
            btnNo.className   = 'psa-btn psa-btn-cancel';
            btnNo.textContent = 'Cancelar';

            btnSi.onclick = function() {
                cerrarModal(m.overlay, function() {
                    if (callbackSi) callbackSi();
                });
            };
            btnNo.onclick = function() {
                cerrarModal(m.overlay, function() {
                    if (callbackNo) callbackNo();
                });
            };

            function onKey(e) {
                if (e.key === 'Escape' || e.keyCode === 27) {
                    document.removeEventListener('keydown', onKey);
                    cerrarModal(m.overlay, function() {
                        if (callbackNo) callbackNo();
                    });
                }
            }
            document.addEventListener('keydown', onKey);

            m.btnZona.appendChild(btnNo);
            m.btnZona.appendChild(btnSi);
            setTimeout(function() { btnNo.focus(); }, 230);
        }
    };

})();