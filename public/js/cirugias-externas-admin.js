/* ============================================================
   Consola admin de Cirugías Externas (diseño prototipo CEMSA)
   Calendario mensual, modal de recibo, toast y guardado inline
   de precios. Datos inyectados vía window.CEA_CONFIG.
============================================================ */
(function () {
  const C = window.CEA_CONFIG || {};
  const RES = (C.reservas || []).filter(r => r.estado !== 'rechazado');
  const $ = id => document.getElementById(id);
  const pad = n => String(n).padStart(2, '0');
  const fkey = (y, m, d) => y + '-' + pad(m + 1) + '-' + pad(d);
  const firstWord = s => (String(s || '').trim().split(' ')[0] || '—');
  const MESES = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
  const DOW = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];

  let calY = new Date().getFullYear(), calM = new Date().getMonth(), diaSel = null;

  function estadoCls(e) { return e === 'pagado' ? 'pagado' : 'pendiente'; }
  function estadoLabel(e) { return e === 'pagado' ? 'Pagado' : 'Pendiente'; }

  function navCal(d) {
    if (d === 0) { const h = new Date(); calY = h.getFullYear(); calM = h.getMonth(); }
    else { calM += d; if (calM < 0) { calM = 11; calY--; } if (calM > 11) { calM = 0; calY++; } }
    diaSel = null; renderCal();
  }

  function renderCal() {
    if (!$('calGrid')) return;
    $('calTitulo').textContent = MESES[calM] + ' ' + calY;
    const porDia = {};
    RES.forEach(r => (porDia[r.fecha] || (porDia[r.fecha] = [])).push(r));
    const primero = new Date(calY, calM, 1);
    const diasMes = new Date(calY, calM + 1, 0).getDate();
    const offset = (primero.getDay() + 6) % 7;
    const hoyK = fkey(new Date().getFullYear(), new Date().getMonth(), new Date().getDate());
    let html = DOW.map(d => '<div class="month-dow">' + d + '</div>').join('');
    for (let i = 0; i < offset; i++) html += '<div class="month-day other"></div>';
    for (let d = 1; d <= diasMes; d++) {
      const k = fkey(calY, calM, d);
      const evs = (porDia[k] || []).sort((a, b) => a.hora.localeCompare(b.hora));
      const cls = ['month-day', k === hoyK ? 'today' : '', k === diaSel ? 'selday' : ''].join(' ');
      let chips = evs.slice(0, 3).map(r => '<span class="evt ' + estadoCls(r.estado) + '">' + r.hora + ' ' + firstWord(r.paciente) + '</span>').join('');
      if (evs.length > 3) chips += '<span class="evt mas">+' + (evs.length - 3) + ' más</span>';
      html += '<div class="' + cls + '" onclick="CEA.selDia(\'' + k + '\')" role="button" tabindex="0"><div class="n">' + d + '</div>' + chips + '</div>';
    }
    $('calGrid').innerHTML = html;
    renderDetDia();
  }

  function selDia(k) { diaSel = (diaSel === k ? null : k); renderCal(); }

  function renderDetDia() {
    const box = $('calDet');
    if (!box) return;
    if (!diaSel) { box.classList.add('hidden'); box.innerHTML = ''; return; }
    const evs = RES.filter(r => r.fecha === diaSel).sort((a, b) => a.hora.localeCompare(b.hora));
    const [y, m, d] = diaSel.split('-');
    box.classList.remove('hidden');
    let inner = '<h3><i class="fa-regular fa-calendar-days"></i> ' + parseInt(d) + ' de ' + MESES[parseInt(m) - 1] + ' ' + y + '</h3>';
    if (evs.length) {
      evs.forEach(r => {
        inner += '<div class="det-item"><span class="hora-block"><i class="fa-regular fa-clock"></i>' + r.hora + '–' + r.hora_fin + '</span>'
          + '<span class="det-info"><strong>' + r.paciente + '</strong><span>Dr. ' + r.cirujano + ' · ' + r.tipo + ' · ' + r.quirofano + '</span></span>'
          + '<span class="badge ' + (r.estado === 'pagado' ? 'b-pago' : 'b-pend') + '"><i class="fa-solid ' + (r.estado === 'pagado' ? 'fa-circle-check' : 'fa-clock') + '"></i> ' + estadoLabel(r.estado) + '</span></div>';
      });
    } else {
      inner += '<p class="hint" style="margin-top:8px"><i class="fa-solid fa-circle-info"></i> Sin cirugías programadas este día.</p>';
    }
    box.innerHTML = inner;
  }

  /* ---- Modal recibo ---- */
  function verRecibo(url) {
    if (!url) return;
    $('ceaModalImg').src = url;
    $('ceaModal').classList.add('open');
  }
  function cerrarModal() { $('ceaModal').classList.remove('open'); }

  /* ---- Toast ---- */
  let toastT;
  function toast(msg) {
    const t = $('ceaToast'); if (!t) return;
    $('ceaToastMsg').textContent = msg;
    t.classList.add('show'); clearTimeout(toastT);
    toastT = setTimeout(() => t.classList.remove('show'), 3000);
  }

  /* ---- Guardado inline de precios ---- */
  function guardarTipo(id) {
    const tr = document.querySelector('tr[data-tipo="' + id + '"]');
    if (!tr) return;
    const fd = new FormData();
    fd.append('_method', 'PUT');
    fd.append('nombre', tr.dataset.nombre || '');
    fd.append('precio', tr.querySelector('[data-field=precio]').value);
    fd.append('duracion_minutos', tr.querySelector('[data-field=duracion]').value);
    fd.append('activo', tr.dataset.activo === '1' ? '1' : '0');
    fetch(C.tiposBase + '/' + id, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': C.csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      body: fd,
    })
      .then(async res => {
        if (res.ok) return {};
        const j = await res.json().catch(() => ({}));
        const first = (j.errors && Object.values(j.errors)[0] && Object.values(j.errors)[0][0]) || j.message || 'No se pudo guardar.';
        throw { msg: first };
      })
      .then(() => toast('Tipo actualizado.'))
      .catch(err => toast((err && err.msg) || 'No se pudo guardar el precio.'));
  }

  /* ---- Rechazar (pide motivo y envía el form de la fila) ---- */
  function rechazar(id) {
    const motivo = window.prompt('Motivo del rechazo de la reserva:');
    if (motivo === null || motivo.trim() === '') return;
    const form = document.getElementById('rechForm' + id);
    if (!form) return;
    form.querySelector('[name=motivo_rechazo]').value = motivo.trim();
    form.submit();
  }

  window.CEA = { navCal, selDia, verRecibo, cerrarModal, guardarTipo, rechazar };

  document.addEventListener('DOMContentLoaded', function () {
    renderCal();
    if (C.flash) toast(C.flash);
  });
})();
