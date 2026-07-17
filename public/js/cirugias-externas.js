/* ============================================================
   Registro público de cirugías externas — CEMSA
   Portado 1:1 del prototipo (index.html/app.js) manteniendo el
   render y la UX idénticos; sólo cambia la capa de datos:
   catálogo inyectado desde la BD (CE_CONFIG), agenda real por
   endpoint y envío por fetch a Laravel.
============================================================ */
(function () {
  const C = window.CE_CONFIG;
  const TIPOS = C.tipos;              // {id, clave, nombre, precio, duracionMin, desc, incluye[], noIncluye[]}
  const QUIROFANOS = C.quirofanos;    // {id, nombre, desc, restriccion[]|null}
  const CIRUGIAS = C.cirugias || [];
  const MONEDA = C.moneda || 'Bs';
  const NOCTURNO_FIN = C.nocturnoFin || 6;
  const DESCUENTO_NOCT = C.descuentoNoct || 0.10;
  const HORARIO = { inicio: 0, fin: 24, pasoMin: 60 };
  const WHATSAPP_NUM = C.whatsapp || '';

  /* ========== ESTADO ========== */
  let tipoSel = null;   // id numérico
  let cirugiaSel = null; // string id
  let quirSel = null;   // id numérico
  let reservaTmp = null;
  let RES = [];         // ocupación (agenda compartida) [{fecha,q,ini,dur,label,tipo}]
  let submitting = false;

  const selectedDuracion = () => {
    if (cirugiaSel) {
      const c = CIRUGIAS.find(x => x.id === cirugiaSel);
      if (c) return c.duracionMin;
    }
    return tipoSel ? tipoDe(tipoSel).duracionMin : 60;
  };

  const $ = id => document.getElementById(id);
  const fmt = n => MONEDA + ' ' + Number(n).toLocaleString('es-BO');
  const tipoDe = id => TIPOS.find(t => t.id === id);
  const quirDe = id => QUIROFANOS.find(q => q.id === id);

  /* ========== UTILS ========== */
  function esNocturno(hora) { const [h] = hora.split(':').map(Number); return h >= 0 && h < NOCTURNO_FIN; }
  function calcPrecio(tipoId, hora) {
    const t = tipoDe(tipoId);
    if (esNocturno(hora)) { const d = Math.round(t.precio * DESCUENTO_NOCT); return { precio: t.precio - d, descuento: d, nocturno: true }; }
    return { precio: t.precio, descuento: 0, nocturno: false };
  }
  function textoDuracion(min) {
    const h = Math.floor(min / 60), m = min % 60;
    if (h && m) return `${h} h ${m} min`;
    if (h) return `${h} h`;
    return `${m} min`;
  }
  const pad = n => String(n).padStart(2, '0');
  const fkey = (y, m, d) => y + '-' + pad(m + 1) + '-' + pad(d);
  function horaMin(hhmm) { const [h, m] = hhmm.split(':').map(Number); return h * 60 + m; }
  function minHora(min) { return pad(Math.floor(min / 60)) + ':' + pad(min % 60); }
  function finDe(hora, durMin) { return minHora(horaMin(hora) + durMin); }

  /* ========== AGENDA (ocupación real) ========== */
  function loadAgenda() {
    return fetch(C.agendaUrl, { headers: { 'Accept': 'application/json' } })
      .then(r => r.ok ? r.json() : [])
      .then(data => { RES = Array.isArray(data) ? data : []; })
      .catch(() => { RES = []; });
  }
  function intervalosDe(fecha, quirofano) {
    const lista = quirofano !== null ? RES.filter(r => r.fecha === fecha && r.q === quirofano) : RES.filter(r => r.fecha === fecha);
    return lista.map(r => ({ ini: r.ini, fin: r.ini + r.dur }));
  }
  function slotValido(fecha, hora, durMin, quirofano) {
    if (quirofano === undefined) quirofano = null;
    const ini = horaMin(hora), fin = ini + durMin;
    if (fin > HORARIO.fin * 60) return false;
    return !intervalosDe(fecha, quirofano).some(o => ini < o.fin && fin > o.ini);
  }

  /* ========== NAVEGACIÓN PRINCIPAL ========== */
  function show(v) {
    ['Nueva', 'Cal'].forEach(s => $('sec' + s) && $('sec' + s).classList.add('hidden'));
    ['tabCirNueva', 'tabCirCal'].forEach(t => $(t) && $(t).classList.remove('active'));
    if (v === 'nueva') { $('secNueva').classList.remove('hidden'); $('tabCirNueva') && $('tabCirNueva').classList.add('active'); }
    if (v === 'cal') { $('secCal').classList.remove('hidden'); $('tabCirCal') && $('tabCirCal').classList.add('active'); renderCal(); }
  }

  /* ========== TIPOS DE CIRUGÍA ========== */
  function renderTipos() {
    const tiposHtml = TIPOS.map(t => `
      <button type="button" class="tipo${tipoSel === t.id ? ' sel' : ''}" onclick="CE.selTipo(${t.id})" aria-pressed="${tipoSel === t.id}">
        <div class="check-icon"><i class="fa-solid fa-check"></i></div>
        <div class="tn">${t.nombre}</div>
        <div class="tp">${fmt(t.precio)}</div>
        <div class="td"><i class="fa-regular fa-clock"></i> ${textoDuracion(t.duracionMin)}${t.desc ? ' · ' + t.desc : ''}</div>
        ${t.incluye && t.incluye.length ? `<div class="tinc"><b><i class="fa-solid fa-circle-check"></i> Incluye:</b> ${t.incluye.join(' · ')}</div>` : ''}
        ${t.noIncluye && t.noIncluye.length ? `<div class="texc"><b>No incluye:</b> ${t.noIncluye.join(', ')}</div>` : ''}
      </button>`).join('');

    let cirugiasHtml = '<p class="hint">Seleccione un tipo para ver las cirugías disponibles.</p>';
    if (tipoSel) {
      const tSel = tipoDe(tipoSel);
      const filtradas = CIRUGIAS.filter(c => c.tipo === tSel.clave);
      if (filtradas.length === 0) {
        cirugiasHtml = '<p class="hint"><i class="fa-solid fa-circle-info"></i> Este tipo de reserva no requiere seleccionar una cirugía específica.</p>';
      } else {
        const grupos = filtradas.reduce((acc, c) => {
          (acc[c.especialidad] ||= []).push(c);
          return acc;
        }, {});
        cirugiasHtml = Object.entries(grupos).map(([especialidad, items]) => `
          <div class="cirugia-grupo">
            <h3>${especialidad}</h3>
            <div class="cirugia-grid">${items.map(c => `
              <button type="button" class="cirugia-item${cirugiaSel === c.id ? ' sel' : ''}" onclick="CE.selCirugia('${c.id}')" aria-pressed="${cirugiaSel === c.id}">
                <div class="cirugia-name">${c.nombre}</div>
                <div class="cirugia-meta"><span>${c.tipo.charAt(0).toUpperCase() + c.tipo.slice(1)}</span> · <span>${textoDuracion(c.duracionMin)}</span></div>
              </button>`).join('')}</div>
          </div>`).join('');
      }
    }

    $('tipos').innerHTML = `<div class="tipos-col">${tiposHtml}</div><div class="cirugias-list">${cirugiasHtml}</div>`;
  }
  function selTipo(id) {
    tipoSel = id;
    cirugiaSel = null;
    pkHora = null;
    if (quirSel !== null) {
      const q = quirDe(quirSel), t = tipoDe(id);
      if (q.restriccion && !q.restriccion.includes(t.clave)) { quirSel = null; toast('El tipo de cirugía no es compatible con el Quirófano 3.'); }
    }
    renderTipos(); renderQuirofanos(); renderPk();
    showWizErr(3, ''); $('wizErr3').classList.add('hidden');
  }
  function selCirugia(id) {
    cirugiaSel = id;
    renderTipos();
    $('wizErr3').classList.add('hidden');
  }

  /* ========== QUIRÓFANOS ========== */
  function renderQuirofanos() {
    const t = tipoSel ? tipoDe(tipoSel) : null;
    $('quirGrid').innerHTML = QUIROFANOS.map(q => {
      const restringido = q.restriccion && t && !q.restriccion.includes(t.clave);
      const sel = quirSel === q.id;
      return `<button type="button"
        class="quir${sel ? ' sel' : ''}${restringido ? ' quir-dis' : ''}"
        onclick="CE.selQuirofano(${q.id})"
        aria-pressed="${sel}" aria-disabled="${restringido}"
        title="${restringido ? 'No disponible para el tipo seleccionado' : q.nombre}">
        <div class="quir-check"><i class="fa-solid fa-check"></i></div>
        <div class="quir-icon"><i class="fa-solid fa-door-open"></i></div>
        <div class="qn">${q.nombre}</div>
        <div class="qd">${q.desc || ''}</div>
        ${restringido ? '<div class="qr"><i class="fa-solid fa-triangle-exclamation"></i> No disponible</div>' : ''}
      </button>`;
    }).join('');
  }
  function selQuirofano(id) {
    const q = quirDe(id), t = tipoSel ? tipoDe(tipoSel) : null;
    if (q.restriccion && t && !q.restriccion.includes(t.clave)) {
      $('quirErrorTxt').textContent = 'El Quirófano 3 solo acepta cirugías menores.';
      $('quirError').classList.remove('hidden');
      return;
    }
    $('quirError').classList.add('hidden');
    quirSel = (quirSel === id ? null : id);
    renderQuirofanos();
    pkDia = null; pkHora = null; renderPk();
    if (quirSel) { $('wizErr4').classList.add('hidden'); }
  }

  /* ========== RECIBO ========== */
  function cargarRecibo(input) {
    const f = input.files[0]; if (!f) return;
    const r = new FileReader();
    r.onload = e => { const img = $('reciboPrev'); img.src = e.target.result; img.style.display = 'inline-block'; };
    r.readAsDataURL(f);
  }

  /* ========== CALENDARIO MENSUAL ========== */
  const MESES = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
  const DOW = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
  let calY = new Date().getFullYear(), calM = new Date().getMonth(), diaSel = null;

  function navCal(d) {
    if (d === 0) { const h = new Date(); calY = h.getFullYear(); calM = h.getMonth(); }
    else { calM += d; if (calM < 0) { calM = 11; calY--; } if (calM > 11) { calM = 0; calY++; } }
    diaSel = null; renderCal();
  }
  // Esquema de color unificado (mismo que /quirofano): interna=verde,
  // externa confirmada=azul, externa pendiente=naranja.
  function evtCls(cat) { return cat === 'interna' ? 'interna' : (cat === 'externa_pend' ? 'ext-pend' : 'ext'); }
  function dotCls(cat) { return cat === 'interna' ? 'green' : (cat === 'externa_pend' ? 'amber' : 'blue'); }

  function renderCal() {
    $('calLeyenda').innerHTML =
      '<span><span class="leg-dot blue"></span>Externa confirmada</span>' +
      '<span><span class="leg-dot amber"></span>Externa pendiente</span>' +
      '<span><span class="leg-dot green"></span>Interna</span>';
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
      const evs = (porDia[k] || []).sort((a, b) => a.ini - b.ini);
      const cls = ['month-day', k === hoyK ? 'today' : '', k === diaSel ? 'selday' : ''].join(' ');
      let chips = evs.slice(0, 3).map(r => '<span class="evt ' + evtCls(r.categoria) + '">' + r.label + ' · ' + r.tipo + '</span>').join('');
      if (evs.length > 3) chips += '<span class="evt mas">+' + (evs.length - 3) + ' más</span>';
      html += '<div class="' + cls + '" onclick="CE.selDia(\'' + k + '\')" role="button" tabindex="0" onkeydown="if(event.key===\'Enter\')CE.selDia(\'' + k + '\')" aria-label="Día ' + d + ', ' + evs.length + ' cirugías"><div class="n">' + d + '</div>' + chips + '</div>';
    }
    $('calGrid').innerHTML = html;
    renderDetDia();
  }
  function selDia(k) { diaSel = (diaSel === k ? null : k); renderCal(); }
  function renderDetDia() {
    const box = $('calDet');
    if (!diaSel) { box.classList.add('hidden'); box.innerHTML = ''; return; }
    const evs = RES.filter(r => r.fecha === diaSel).sort((a, b) => a.ini - b.ini);
    const [y, m, d] = diaSel.split('-');
    box.classList.remove('hidden');
    let inner = '<h3><i class="fa-regular fa-calendar-days"></i> ' + parseInt(d) + ' de ' + MESES[parseInt(m) - 1] + ' ' + y + '</h3>';
    if (evs.length) {
      evs.forEach(r => {
        const q = quirDe(r.q);
        inner += '<div class="det-item"><span class="hora-block"><i class="fa-regular fa-clock"></i>' + minHora(r.ini) + '–' + minHora(r.ini + r.dur) + '</span><span class="det-info"><strong><span class="leg-dot ' + dotCls(r.categoria) + '"></span> ' + r.label + '</strong><span>' + r.tipo + (q ? ' · ' + q.nombre : '') + '</span></span></div>';
      });
    } else {
      inner += '<p class="hint" style="margin-top:8px"><i class="fa-solid fa-circle-info"></i> Sin cirugías programadas este día.</p>';
    }
    box.innerHTML = inner;
  }

  /* ========== PICKER DE PROGRAMACIÓN ========== */
  let pkY = new Date().getFullYear(), pkM = new Date().getMonth(), pkDia = null, pkHora = null;

  function slotsDia() {
    const out = [];
    for (let min = HORARIO.inicio * 60; min < HORARIO.fin * 60; min += HORARIO.pasoMin)
      out.push(pad(Math.floor(min / 60)) + ':' + pad(min % 60));
    return out;
  }
  function navPk(d) {
    if (d === 0) { const h = new Date(); pkY = h.getFullYear(); pkM = h.getMonth(); }
    else { pkM += d; if (pkM < 0) { pkM = 11; pkY--; } if (pkM > 11) { pkM = 0; pkY++; } }
    pkDia = null; pkHora = null; renderPk();
  }
  function renderPk() {
    $('pkTitulo').textContent = MESES[pkM] + ' ' + pkY;
    const primero = new Date(pkY, pkM, 1);
    const diasMes = new Date(pkY, pkM + 1, 0).getDate();
    const offset = (primero.getDay() + 6) % 7;
    const ahora = new Date();
    const hoyK = fkey(ahora.getFullYear(), ahora.getMonth(), ahora.getDate());
    const horaActNow = pad(ahora.getHours()) + ':' + pad(ahora.getMinutes());
    const dur = selectedDuracion();
    let html = DOW.map(d => '<div class="cal-dow">' + d + '</div>').join('');
    for (let i = 0; i < offset; i++) html += '<div class="cal-day other"></div>';
    for (let d = 1; d <= diasMes; d++) {
      const k = fkey(pkY, pkM, d);
      const pasado = k < hoyK;
      const nOcup = (quirSel !== null ? RES.filter(r => r.fecha === k && r.q === quirSel) : RES.filter(r => r.fecha === k)).length;
      const disponibles = pasado ? [] : slotsDia().filter(h => (k !== hoyK || h > horaActNow) && slotValido(k, h, dur, quirSel));
      const lleno = !pasado && disponibles.length === 0;
      const cls = ['cal-day', pasado || lleno ? 'dis' : '', k === hoyK ? 'today' : '', k === pkDia ? 'selday' : ''].join(' ');
      const badge = pasado ? '' : nOcup === 0 ? '<span class="ocup libre">libre</span>' : lleno ? '<span class="ocup full">lleno</span>' : '<span class="ocup parcial">' + nOcup + ' ocup.</span>';
      if (pasado || lleno) {
        html += '<div class="' + cls + '" aria-disabled="true"><div class="n">' + d + '</div>' + badge + '</div>';
      } else {
        html += '<div class="' + cls + '" onclick="CE.selPkDia(\'' + k + '\')" role="button" tabindex="0" onkeydown="if(event.key===\'Enter\')CE.selPkDia(\'' + k + '\')"><div class="n">' + d + '</div>' + badge + '</div>';
      }
    }
    $('pkGrid').innerHTML = html;
    renderSlots();
  }
  function selPkDia(k) { pkDia = (pkDia === k ? null : k); pkHora = null; renderPk(); }
  function renderSlots() {
    const box = $('pkSlots'), sel = $('pkSel');
    if (!pkDia) { box.classList.add('hidden'); sel.classList.add('hidden'); return; }
    const dur = selectedDuracion();
    const ahora = new Date();
    const esHoy = pkDia === fkey(ahora.getFullYear(), ahora.getMonth(), ahora.getDate());
    const horaAct = pad(ahora.getHours()) + ':' + pad(ahora.getMinutes());
    const [y, m, d] = pkDia.split('-');
    const hayNocturno = slotsDia().some(h => esNocturno(h) && !((esHoy && h <= horaAct) || !slotValido(pkDia, h, dur, quirSel)));
    const bannerNoct = hayNocturno ? '<div class="noct-banner"><i class="fa-solid fa-moon"></i><span>Los horarios en <b>naranja (00:00–06:00)</b> tienen un <b>descuento nocturno del 10%</b> automático.</span></div>' : '';
    let slotsHtml = slotsDia().map(h => {
      const bloqueado = (esHoy && h <= horaAct) || !slotValido(pkDia, h, dur, quirSel);
      const noct = esNocturno(h);
      const calc = tipoSel ? calcPrecio(tipoSel, h) : null;
      const desc = calc && calc.nocturno ? '<small><i class="fa-solid fa-moon"></i> -10%</small>' : '';
      return '<button type="button" class="slot' + (pkHora === h ? ' sel' : '') + (noct && !bloqueado ? ' slot-noct' : '') + '" ' + (bloqueado ? 'disabled' : '') + ' onclick="CE.selSlot(\'' + h + '\')" aria-pressed="' + (pkHora === h) + '">' + h + desc + '</button>';
    }).join('');
    box.classList.remove('hidden');
    box.innerHTML = '<div class="slots-box"><div class="slots-title"><i class="fa-regular fa-clock"></i> Horarios disponibles · ' + parseInt(d) + ' de ' + MESES[parseInt(m) - 1] + ' ' + y + (tipoSel ? ' · ' + textoDuracion(dur) : '') + '</div><div class="slots-subtitle">Seleccione un horario de inicio</div>' + bannerNoct + '<div class="slots-grid">' + slotsHtml + '</div></div>';
    if (pkHora) {
      const noct = esNocturno(pkHora);
      const calcS = tipoSel ? calcPrecio(tipoSel, pkHora) : null;
      const descInfo = calcS && calcS.nocturno ? ' · <i class="fa-solid fa-moon"></i> Desc. nocturno: <b>' + fmt(calcS.precio) + '</b> (base: ' + fmt(tipoDe(tipoSel).precio) + ')' : (tipoSel ? ' · Total: <b>' + fmt(calcS.precio) + '</b>' : '');
      sel.className = 'pk-sel' + (noct ? ' noct-sel' : '');
      sel.classList.remove('hidden');
      sel.innerHTML = '<i class="fa-regular fa-calendar-check"></i><span>Seleccionado: <b>' + parseInt(d) + ' de ' + MESES[parseInt(m) - 1] + ' ' + y + '</b> de <b>' + pkHora + '</b> a <b>' + finDe(pkHora, dur) + '</b>' + descInfo + '</span>';
      $('wizErr5').classList.add('hidden');
    } else sel.classList.add('hidden');
  }
  function selSlot(h) { pkHora = (pkHora === h ? null : h); renderSlots(); }

  /* ========== TOAST ========== */
  let toastT;
  function toast(msg) { const t = $('toast'); $('toastMsg').textContent = msg; t.classList.add('show'); clearTimeout(toastT); toastT = setTimeout(() => t.classList.remove('show'), 3200); }

  /* ============================================================
     WIZARD LOGIC
  ============================================================ */
  const WIZ_TOTAL = 6;
  let wizStep = 1;
  let wizBusy = false;

  const WIZ_META = [
    { icon: 'fa-user-doctor', label: 'Médico cirujano', color: 'blue' },
    { icon: 'fa-person', label: 'Paciente', color: 'navy' },
    { icon: 'fa-scalpel-line-dashed', label: 'Tipo de cirugía', color: 'blue' },
    { icon: 'fa-hospital', label: 'Quirófano', color: 'green' },
    { icon: 'fa-calendar-days', label: 'Fecha y hora', color: 'amber' },
    { icon: 'fa-clipboard-check', label: 'Confirmación', color: 'green' },
  ];

  function updateWizStepper() {
    for (let i = 1; i <= WIZ_TOTAL; i++) {
      const num = $('wsn' + i), lbl = $('wsl' + i), line = $('wline' + (i - 1));
      num.className = 'wiz-snum ' + (i < wizStep ? 'done' : i === wizStep ? 'active' : '');
      lbl.className = 'wiz-slbl ' + (i < wizStep ? 'done' : i === wizStep ? 'active' : '');
      num.innerHTML = i < wizStep ? '<i class="fa-solid fa-check" style="font-size:12px"></i>' : i;
      if (line) { line.className = 'wiz-sline' + (i <= wizStep ? ' done' : ''); }
    }
    const m = WIZ_META[wizStep - 1];
    $('wizMobIcon').innerHTML = '<i class="fa-solid ' + m.icon + '"></i>';
    $('wizMobLabel').textContent = m.label;
    $('wizMobFill').style.width = (wizStep / WIZ_TOTAL * 100) + '%';
    $('wizMobTxt').textContent = wizStep + ' / ' + WIZ_TOTAL;
  }

  function updateWizNav() {
    const prev = $('wizPrevBtn'), next = $('wizNextBtn');
    const foot = $('wizFoot');
    if (wizStep === 1) { prev.classList.add('hidden'); } else { prev.classList.remove('hidden'); }
    next.className = 'btn-wiz-next';
    if (wizStep === WIZ_TOTAL) {
      next.style.display = 'none';
      let ra = foot.querySelector('.wiz-foot-actions');
      if (!ra) { ra = document.createElement('div'); ra.className = 'wiz-foot-actions'; ra.style.cssText = 'display:flex;gap:8px;flex-wrap:wrap'; foot.appendChild(ra); }
      ra.innerHTML = '<button class="btn-wiz-confirm" onclick="CE.confirmarReserva()"><i class="fa-solid fa-check"></i> Confirmar</button>';
    } else {
      next.style.display = 'flex';
      const ra = foot.querySelector('.wiz-foot-actions');
      if (ra) ra.remove();
    }
  }

  /* Slide animation */
  function wizGo(toStep, dir) {
    if (wizBusy || toStep === wizStep) return;
    wizBusy = true;
    const fromEl = $('wStep' + wizStep);
    const toEl = $('wStep' + toStep);
    const track = $('wizTrack');
    track.style.height = fromEl.offsetHeight + 'px';
    track.style.overflow = 'hidden';
    const enterX = dir === 'fwd' ? '100%' : '-100%';
    const exitX = dir === 'fwd' ? '-100%' : '100%';
    fromEl.style.cssText = 'display:block;position:absolute;top:0;left:0;width:100%;will-change:transform;';
    toEl.style.cssText = 'display:block;position:absolute;top:0;left:0;width:100%;will-change:transform;transform:translateX(' + enterX + ');';
    toEl.getBoundingClientRect();
    fromEl.style.transition = 'transform 350ms ease-in-out';
    toEl.style.transition = 'transform 350ms ease-in-out';
    fromEl.style.transform = 'translateX(' + exitX + ')';
    toEl.style.transform = 'translateX(0)';
    setTimeout(() => {
      fromEl.style.cssText = 'display:none;';
      toEl.style.cssText = 'display:block;';
      track.style.height = 'auto';
      track.style.overflow = '';
      wizStep = toStep;
      wizBusy = false;
      updateWizStepper();
      updateWizNav();
      if (toStep === 5) renderPk();
      if (toStep === 6) buildConfirmacion();
    }, 360);
  }

  /* Validation */
  function wizValidate(step) {
    clearWizErr(step);
    let ok = true;
    if (step === 1) {
      const c = $('fCirujano').value.trim();
      const t = $('fCirujanoTel').value.trim();
      const m = $('fCirujanoMail').value.trim();
      const faltan = [];
      if (!c) { markCampoErr($('fCirujano')); faltan.push('nombre del cirujano'); }
      if (!/^\d{7,10}$/.test(t)) { markCampoErr($('fCirujanoTel')); faltan.push('celular válido (7-10 dígitos)'); }
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(m)) { markCampoErr($('fCirujanoMail')); faltan.push('correo electrónico válido'); }
      if (faltan.length) { showWizErr(1, 'Complete: ' + faltan.join(', ') + '.'); ok = false; }
    }
    if (step === 2) {
      if (!$('fNombre').value.trim()) { markCampoErr($('fNombre')); showWizErr(2, 'Ingrese el nombre completo del paciente.'); ok = false; }
    }
    if (step === 3) {
      if (!tipoSel) { showWizErr(3, 'Seleccione un tipo de cirugía para continuar.'); ok = false; }
      else {
        const tSel = tipoDe(tipoSel);
        const tieneEspecificas = CIRUGIAS.some(c => c.tipo === tSel.clave);
        if (tieneEspecificas && !cirugiaSel) {
          showWizErr(3, 'Seleccione una cirugía específica para continuar.');
          ok = false;
        }
      }
    }
    if (step === 4) {
      if (!quirSel) { showWizErr(4, 'Seleccione un quirófano para continuar.'); ok = false; }
    }
    if (step === 5) {
      if (!pkDia || !pkHora) { showWizErr(5, 'Seleccione una fecha y un horario disponible.'); ok = false; }
      else {
        const dur = selectedDuracion();
        if (!slotValido(pkDia, pkHora, dur, quirSel)) { showWizErr(5, 'Ese horario ya no está disponible. Elija otro.'); renderPk(); ok = false; }
      }
    }
    if (!ok) shakeStep(step);
    return ok;
  }

  function markCampoErr(el) {
    el.classList.add('campo-err');
    el.addEventListener('input', () => el.classList.remove('campo-err'), { once: true });
  }
  function showWizErr(step, msg) {
    const el = $('wizErr' + step); if (!el) return;
    if (!msg) { el.classList.add('hidden'); return; }
    const txt = $('wizErr' + step + 'Txt'); if (txt) txt.textContent = msg; else el.innerHTML = '<i class="fa-solid fa-circle-exclamation"></i> ' + msg;
    el.classList.remove('hidden');
  }
  function clearWizErr(step) { const el = $('wizErr' + step); if (el) el.classList.add('hidden'); }
  function shakeStep(step) {
    const el = $('wiz' + step + 'Body'); if (!el) return;
    el.classList.remove('wiz-shake');
    void el.offsetWidth;
    el.classList.add('wiz-shake');
    setTimeout(() => el.classList.remove('wiz-shake'), 500);
  }

  /* Navigation handlers */
  function wizNext() {
    if (wizBusy) return;
    if (wizStep === WIZ_TOTAL) return;
    if (!wizValidate(wizStep)) return;
    wizGo(wizStep + 1, 'fwd');
  }
  function wizPrev() {
    if (wizBusy || wizStep <= 1) return;
    wizGo(wizStep - 1, 'back');
  }

  /* Build confirmation step */
  function buildConfirmacion() {
    const cirujano = $('fCirujano').value.trim();
    const cirujanoTel = $('fCirujanoTel').value.trim();
    const cirujanoMail = $('fCirujanoMail').value.trim();
    const nombre = $('fNombre').value.trim();
    const t = tipoDe(tipoSel);
    const q = quirDe(quirSel);
    const calc = calcPrecio(tipoSel, pkHora);
    const c = cirugiaSel ? CIRUGIAS.find(x => x.id === cirugiaSel) : null;

    reservaTmp = {
      cirujano, cirujanoTel, cirujanoMail, nombre, tipo: tipoSel, cirugia: cirugiaSel, quirofano: quirSel,
      fecha: pkDia, hora: pkHora, precioFinal: calc.precio, descuento: calc.descuento, nocturno: calc.nocturno
    };

    let html = `<dt>Cirujano</dt><dd>Dr. ${cirujano}</dd>
      <dt>Celular</dt><dd>${cirujanoTel}</dd>
      <dt>Correo</dt><dd>${cirujanoMail}</dd>
      <dt>Paciente</dt><dd>${nombre}</dd>
      <dt>Tipo</dt><dd>${t.nombre}</dd>
      <dt>Cirugía</dt><dd>${c ? c.nombre + ' · ' + c.especialidad : '—'}</dd>
      <dt>Duración</dt><dd>${textoDuracion(selectedDuracion())}</dd>
      <dt>Incluye</dt><dd>${t.incluye && t.incluye.length ? t.incluye.join(' · ') : '—'}</dd>
      <dt>No incluye</dt><dd>${t.noIncluye && t.noIncluye.length ? t.noIncluye.join(', ') : '—'}</dd>
      <dt>Quirófano</dt><dd>${q.nombre}</dd>
      <dt>Horario</dt><dd>${pkDia} · ${pkHora} a ${finDe(pkHora, selectedDuracion())}</dd>
      <div class="total-row">`;
    if (calc.nocturno) {
      html += `<div class="total-label">Precio base</div>
      <div class="precio-orig">${fmt(t.precio)}</div>
      <div class="total-label" style="margin-top:8px">Descuento nocturno (10%)</div>
      <div style="color:var(--green-dk);font-weight:700;font-size:14px">− ${fmt(calc.descuento)} <span class="descuento-tag"><i class="fa-solid fa-moon"></i> 00:00–06:00</span></div>
      <div class="total-label" style="margin-top:10px">Total a pagar</div>
      <div class="total-amount">${fmt(calc.precio)}</div>`;
    } else {
      html += `<div class="total-label">Total a pagar</div><div class="total-amount">${fmt(calc.precio)}</div>`;
    }
    html += '</div>';
    $('resumen').innerHTML = html;
    $('reciboPrev').style.display = 'none';
    $('fRecibo').value = '';
    $('wizErr6').classList.add('hidden');
  }

  /* Reset wizard */
  function wizReset() {
    document.querySelectorAll('.wstep').forEach(s => { s.style.cssText = 'display:none;'; });
    $('wStep1').style.cssText = 'display:block;';
    wizStep = 1;
    document.querySelector('.wiz-foot-actions')?.remove();
    $('wizNextBtn').style.display = 'flex';
    updateWizStepper();
    updateWizNav();
  }

  function limpiarForm() {
    ['fCirujano', 'fCirujanoTel', 'fCirujanoMail', 'fNombre'].forEach(i => $(i).value = '');
    $('fRecibo').value = '';
    tipoSel = null; cirugiaSel = null; quirSel = null; reservaTmp = null; pkDia = null; pkHora = null;
    renderTipos(); renderQuirofanos(); renderPk();
    for (let i = 1; i <= WIZ_TOTAL; i++) clearWizErr(i);
    $('quirError').classList.add('hidden');
    wizReset();
  }

  /* ========== ENVÍO AL BACKEND ========== */
  function validateStep6() {
    if (!$('fRecibo').files[0]) { showWizErr(6, 'Debe subir la foto del recibo para procesar el pago.'); return false; }
    if (!slotValido(reservaTmp.fecha, reservaTmp.hora, selectedDuracion(), reservaTmp.quirofano)) {
      showWizErr(6, 'Ese horario ya fue tomado. Vuelva atrás y elija otro.'); return false;
    }
    $('wizErr6').classList.add('hidden');
    return true;
  }

  function setSubmitting(on) {
    submitting = on;
    document.querySelectorAll('.wiz-foot-actions button').forEach(b => b.disabled = on);
  }

  function submitReserva() {
    if (submitting) return;
    setSubmitting(true);
    const fd = new FormData();
    fd.append('cirujano_nombre', reservaTmp.cirujano);
    fd.append('cirujano_telefono', reservaTmp.cirujanoTel);
    fd.append('cirujano_email', reservaTmp.cirujanoMail);
    fd.append('paciente_nombre', reservaTmp.nombre);
    fd.append('tipo_cirugia_externa_id', reservaTmp.tipo);
    fd.append('cirugia_id', reservaTmp.cirugia || '');
    fd.append('quirofano_id', reservaTmp.quirofano);
    fd.append('fecha', reservaTmp.fecha);
    fd.append('hora_inicio', reservaTmp.hora);
    fd.append('recibo', $('fRecibo').files[0]);

    fetch(C.storeUrl, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': C.csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      body: fd,
    })
      .then(async res => {
        if (res.ok) return res.json();
        if (res.status === 422) {
          const j = await res.json();
          const first = (j.errors && Object.values(j.errors)[0] && Object.values(j.errors)[0][0]) || j.message || 'Revise los datos ingresados.';
          throw { msg: first };
        }
        if (res.status === 429) throw { msg: 'Demasiados intentos. Espere un momento e intente de nuevo.' };
        throw { msg: 'No se pudo registrar la reserva. Intente de nuevo.' };
      })
      .then(data => {
        // Éxito confirmado por el servidor (imagen subida y reserva creada):
        // recién ahora se muestra la confirmación y se envía WhatsApp.
        finalizarConExito(data && data.codigo);
      })
      .catch(err => {
        showWizErr(6, (err && err.msg) || 'No se pudo registrar la reserva.');
        setSubmitting(false);
      });
  }

  /** Arma la URL de WhatsApp con el detalle de la reserva. */
  function urlWhatsapp() {
    const r = reservaTmp, t = tipoDe(r.tipo), q = quirDe(r.quirofano);
    const c = r.cirugia ? CIRUGIAS.find(x => x.id === r.cirugia) : null;
    const incluyeLn = t.incluye && t.incluye.length ? '\nIncluye: ' + t.incluye.join(', ') : '';
    const noInclLn = t.noIncluye && t.noIncluye.length ? '\nNo incluye: ' + t.noIncluye.join(', ') : '';
    const dur = selectedDuracion();
    const msg = 'Nueva reserva de quirófano:\nCirujano: Dr. ' + r.cirujano + ' · ' + r.cirujanoTel + '\nPaciente: ' + r.nombre + '\nCirugía: ' + (c ? c.nombre + ' (' + c.especialidad + ')' : t.nombre + ' (' + textoDuracion(dur) + ')') + incluyeLn + noInclLn + '\nQuirófano: ' + q.nombre + '\nFecha: ' + r.fecha + '  ' + r.hora + '–' + finDe(r.hora, dur) + '\nMonto: ' + fmt(r.precioFinal) + (r.nocturno ? ' (descuento nocturno 10%)' : '');
    return 'https://wa.me/' + WHATSAPP_NUM + '?text=' + encodeURIComponent(msg);
  }

  /**
   * Al completar la reserva: mensaje flotante de confirmación + envío a WhatsApp
   * + reinicio de la página. WhatsApp se intenta abrir en pestaña nueva y también
   * queda como botón (gesto del usuario = 100% confiable). La página se reinicia
   * automáticamente a los 12s, o al instante con "Hacer otra reserva".
   */
  function finalizarConExito(codigo) {
    const waUrl = urlWhatsapp();
    try { window.open(waUrl, '_blank', 'noopener'); } catch (e) { /* fallback: botón */ }

    const ov = document.createElement('div');
    ov.className = 'ce-ok-ov';
    ov.innerHTML =
      '<div class="ce-ok-card">' +
      '  <div class="ce-ok-ico"><i class="fa-solid fa-circle-check"></i></div>' +
      '  <h3>¡Reserva registrada!</h3>' +
      (codigo ? '  <div class="ce-ok-code">' + codigo + '</div>' : '') +
      '  <p class="ce-ok-sub">El pago será verificado por administración. Envíe el detalle por WhatsApp para agilizar la confirmación.</p>' +
      '  <a class="ce-ok-wa" href="' + waUrl + '" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i> Enviar detalle por WhatsApp</a>' +
      '  <button type="button" class="ce-ok-new" onclick="location.reload()"><i class="fa-solid fa-rotate-right"></i> Hacer otra reserva</button>' +
      '  <p class="ce-ok-timer">La página se reiniciará automáticamente…</p>' +
      '</div>';
    document.body.appendChild(ov);

    setTimeout(function () { location.reload(); }, 12000);
  }

  function confirmarReserva() {
    if (!reservaTmp || !validateStep6()) return;
    submitReserva();   // si el envío es exitoso, submitReserva redirige a WhatsApp
  }

  /* ========== API pública (para los onclick del markup) ========== */
  window.CE = {
    show, selTipo, selCirugia, selQuirofano, cargarRecibo, navCal, selDia,
    navPk, selPkDia, selSlot, wizNext, wizPrev, confirmarReserva,
  };

  /* ========== INIT ========== */
  renderTipos();
  renderQuirofanos();
  renderPk();
  updateWizStepper();
  updateWizNav();
  loadAgenda().then(() => { renderPk(); });
})();
