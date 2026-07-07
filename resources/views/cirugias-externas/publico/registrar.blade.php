@php
    $tiposJs = $tipos->map(fn ($t) => [
        'id' => $t->id,
        'clave' => $t->clave,
        'nombre' => $t->nombre,
        'precio' => (float) $t->precio,
        'duracionMin' => (int) $t->duracion_minutos,
        'desc' => $t->descripcion,
        'incluye' => $t->incluye ?? [],
        'noIncluye' => $t->no_incluye ?? [],
    ])->values();

    $quirofanosJs = $quirofanos->map(fn ($q) => [
        'id' => $q->id,
        'nombre' => 'Quirófano ' . $q->id,
        'desc' => ! empty($q->restriccion_externa)
            ? 'Solo: ' . collect($q->restriccion_externa)->join(', ')
            : 'Disponible para todo tipo de cirugía',
        'restriccion' => $q->restriccion_externa,
    ])->values();

    $ver = @filemtime(public_path('js/cirugias-externas.js')) ?: '1';
    $verCss = @filemtime(public_path('css/cirugias-externas.css')) ?: '1';
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Registro de Uso de Quirófano en CEMSA</title>
<link rel="icon" type="image/png" href="{{ asset('images/logocelular.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/cirugias-externas.css') }}?v={{ $verCss }}">
<style>
/* Modal flotante de confirmación de reserva */
.ce-ok-ov{position:fixed;inset:0;background:rgba(15,23,42,.6);backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center;padding:20px;z-index:80;animation:ceFade .2s ease}
@keyframes ceFade{from{opacity:0}to{opacity:1}}
.ce-ok-card{background:#fff;border-radius:16px;box-shadow:0 20px 40px rgba(0,0,0,.25);max-width:390px;width:100%;padding:32px 24px;text-align:center;animation:cePop .25s ease}
@keyframes cePop{from{transform:scale(.92);opacity:0}to{transform:scale(1);opacity:1}}
.ce-ok-ico{width:66px;height:66px;border-radius:50%;background:#ecfdf5;color:#059669;display:flex;align-items:center;justify-content:center;font-size:34px;margin:0 auto 16px}
.ce-ok-card h3{font-size:20px;font-weight:700;color:#0f172a;margin:0 0 8px}
.ce-ok-code{font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:14px;font-weight:700;color:#1a2e4a;background:#f1f5f9;border-radius:8px;padding:6px 12px;display:inline-block;margin-bottom:10px}
.ce-ok-sub{font-size:13px;color:#64748b;margin:0 0 20px;line-height:1.5}
.ce-ok-wa{display:flex;align-items:center;justify-content:center;gap:8px;background:#25d366;color:#fff;border:none;border-radius:10px;padding:13px;font-size:15px;font-weight:600;text-decoration:none;width:100%;margin-bottom:10px;cursor:pointer}
.ce-ok-wa:hover{background:#1da851;text-decoration:none}
.ce-ok-new{display:flex;align-items:center;justify-content:center;gap:8px;background:#fff;color:#334155;border:1.5px solid #e2e8f0;border-radius:10px;padding:12px;font-size:14px;font-weight:600;width:100%;cursor:pointer;transition:all .2s}
.ce-ok-new:hover{border-color:#1a2e4a;color:#1a2e4a}
.ce-ok-timer{font-size:11px;color:#94a3b8;margin-top:12px}
</style>
</head>
<body>

<div id="viewApp">
  <div class="topbar">
    <div class="brand">
      <div>Registro de Uso de Quirófano en CEMSA<span class="brand-sub">Sistema de Reserva de Quirófanos</span></div>
    </div>
    <div class="userchip">
      <div class="role-badge"><i class="fa-solid fa-circle-user"></i> <span>Cirujano</span></div>
    </div>
  </div>

  <main>
    <!-- NAV CIRUJANO -->
    <div class="tabs" id="tabsCirujano">
      <button id="tabCirNueva" class="active" onclick="CE.show('nueva')">
        <i class="fa-solid fa-calendar-plus"></i> Programar cirugía
      </button>
      <button id="tabCirCal" onclick="CE.show('cal')">
        <i class="fa-regular fa-calendar"></i> Calendario
      </button>
    </div>

    <!-- ================================================================
         SECCIÓN: NUEVA RESERVA (WIZARD)
    ================================================================ -->
    <section id="secNueva">
      <div class="wizard-wrap">

        <!-- Indicador móvil -->
        <div class="wiz-mob" id="wizMob">
          <div class="wiz-mob-icon" id="wizMobIcon"><i class="fa-solid fa-user-doctor"></i></div>
          <div class="wiz-mob-info">
            <div class="wiz-mob-label" id="wizMobLabel">Médico cirujano</div>
            <div class="wiz-mob-prog-bar"><div class="wiz-mob-fill" id="wizMobFill" style="width:16.6%"></div></div>
          </div>
          <div class="wiz-mob-txt" id="wizMobTxt">1 / 6</div>
        </div>

        <!-- Stepper desktop -->
        <div class="wiz-stepper" id="wizStepper">
          <div class="wiz-si">
            <div class="wiz-snum active" id="wsn1">1</div>
            <div class="wiz-slbl active" id="wsl1">Médico</div>
          </div>
          <div class="wiz-sline" id="wline1"></div>
          <div class="wiz-si">
            <div class="wiz-snum" id="wsn2">2</div>
            <div class="wiz-slbl" id="wsl2">Paciente</div>
          </div>
          <div class="wiz-sline" id="wline2"></div>
          <div class="wiz-si">
            <div class="wiz-snum" id="wsn3">3</div>
            <div class="wiz-slbl" id="wsl3">Cirugía</div>
          </div>
          <div class="wiz-sline" id="wline3"></div>
          <div class="wiz-si">
            <div class="wiz-snum" id="wsn4">4</div>
            <div class="wiz-slbl" id="wsl4">Quirófano</div>
          </div>
          <div class="wiz-sline" id="wline4"></div>
          <div class="wiz-si">
            <div class="wiz-snum" id="wsn5">5</div>
            <div class="wiz-slbl" id="wsl5">Fecha</div>
          </div>
          <div class="wiz-sline" id="wline5"></div>
          <div class="wiz-si">
            <div class="wiz-snum" id="wsn6">6</div>
            <div class="wiz-slbl" id="wsl6">Confirmar</div>
          </div>
        </div>

        <!-- Wizard card -->
        <div class="wiz-card">
          <div class="wiz-track" id="wizTrack">

            <!-- ===== PASO 1: MÉDICO ===== -->
            <div class="wstep active" id="wStep1">
              <div class="wiz-step-head">
                <div class="wiz-step-icon blue"><i class="fa-solid fa-user-doctor"></i></div>
                <div class="wiz-step-head-txt">
                  <h2>Médico cirujano</h2>
                  <p>Datos de contacto del responsable de la cirugía</p>
                </div>
              </div>
              <div style="padding:24px" id="wiz1Body">
                <div class="field">
                  <label for="fCirujano"><i class="fa-solid fa-id-badge"></i> Nombre completo del cirujano</label>
                  <input id="fCirujano" placeholder="Ej: Roberto Salinas">
                </div>
                <div class="grid2">
                  <div class="field">
                    <label for="fCirujanoTel"><i class="fa-solid fa-phone"></i> Celular</label>
                    <input id="fCirujanoTel" type="tel" inputmode="tel" placeholder="Ej: 70012345">
                  </div>
                  <div class="field">
                    <label for="fCirujanoMail"><i class="fa-solid fa-envelope"></i> Correo electrónico</label>
                    <input id="fCirujanoMail" type="email" placeholder="Ej: dr@correo.com">
                  </div>
                </div>
                <div id="wizErr1" class="wiz-err hidden"><i class="fa-solid fa-circle-exclamation"></i> <span id="wizErr1Txt"></span></div>
              </div>
            </div>

            <!-- ===== PASO 2: PACIENTE ===== -->
            <div class="wstep" id="wStep2">
              <div class="wiz-step-head">
                <div class="wiz-step-icon navy"><i class="fa-solid fa-person"></i></div>
                <div class="wiz-step-head-txt">
                  <h2>Datos del paciente</h2>
                  <p>Nombre completo del paciente a intervenir</p>
                </div>
              </div>
              <div style="padding:24px" id="wiz2Body">
                <div class="field" style="max-width:480px">
                  <label for="fNombre"><i class="fa-solid fa-id-card"></i> Nombre completo del paciente</label>
                  <input id="fNombre" placeholder="Ej: María Fernández Rojas">
                </div>
                <div id="wizErr2" class="wiz-err hidden"><i class="fa-solid fa-circle-exclamation"></i> <span id="wizErr2Txt"></span></div>
              </div>
            </div>

            <!-- ===== PASO 3: TIPO DE CIRUGÍA ===== -->
            <div class="wstep" id="wStep3">
              <div class="wiz-step-head">
                <div class="wiz-step-icon blue"><i class="fa-solid fa-scalpel-line-dashed"></i></div>
                <div class="wiz-step-head-txt">
                  <h2>Tipo de cirugía</h2>
                  <p>Seleccione el procedimiento a realizar</p>
                </div>
              </div>
              <div style="padding:24px" id="wiz3Body">
                <div class="tipos" id="tipos"></div>
                <div id="wizErr3" class="wiz-err hidden"><i class="fa-solid fa-circle-exclamation"></i> <span id="wizErr3Txt"></span></div>
              </div>
            </div>

            <!-- ===== PASO 4: QUIRÓFANO ===== -->
            <div class="wstep" id="wStep4">
              <div class="wiz-step-head">
                <div class="wiz-step-icon green"><i class="fa-solid fa-hospital"></i></div>
                <div class="wiz-step-head-txt">
                  <h2>Selección de quirófano</h2>
                  <p>Quirófano 3 solo para cirugías menores y partos</p>
                </div>
              </div>
              <div style="padding:24px" id="wiz4Body">
                <p class="hint" style="margin-bottom:14px"><i class="fa-solid fa-circle-info"></i> El Quirófano 3 está habilitado únicamente para cirugías menores y partos naturales.</p>
                <div class="quir-grid" id="quirGrid"></div>
                <div id="quirError" class="wiz-err hidden"><i class="fa-solid fa-circle-exclamation"></i> <span id="quirErrorTxt"></span></div>
                <div id="wizErr4" class="wiz-err hidden"><i class="fa-solid fa-circle-exclamation"></i> <span id="wizErr4Txt"></span></div>
              </div>
            </div>

            <!-- ===== PASO 5: FECHA Y HORA ===== -->
            <div class="wstep" id="wStep5">
              <div class="wiz-step-head">
                <div class="wiz-step-icon amber"><i class="fa-regular fa-calendar-days"></i></div>
                <div class="wiz-step-head-txt">
                  <h2>Fecha y hora</h2>
                  <p>Seleccione día y horario disponible en el quirófano elegido</p>
                </div>
              </div>
              <div style="padding:24px" id="wiz5Body">
                <p class="hint" style="margin-bottom:14px"><i class="fa-solid fa-moon"></i> Horarios <b>00:00–06:00</b> aplican <b>descuento nocturno del 10%</b></p>
                <div class="cal-head">
                  <span class="cal-month-label" id="pkTitulo"></span>
                  <div class="cal-nav-btns">
                    <button type="button" onclick="CE.navPk(-1)"><i class="fa-solid fa-chevron-left"></i></button>
                    <button type="button" class="hoy" onclick="CE.navPk(0)">Hoy</button>
                    <button type="button" onclick="CE.navPk(1)"><i class="fa-solid fa-chevron-right"></i></button>
                  </div>
                </div>
                <div class="cal-grid pk" id="pkGrid"></div>
                <div class="slots hidden" id="pkSlots"></div>
                <div class="pk-sel hidden" id="pkSel"></div>
                <div id="wizErr5" class="wiz-err hidden"><i class="fa-solid fa-circle-exclamation"></i> <span id="wizErr5Txt"></span></div>
              </div>
            </div>

            <!-- ===== PASO 6: CONFIRMACIÓN ===== -->
            <div class="wstep" id="wStep6">
              <div class="wiz-step-head">
                <div class="wiz-step-icon green"><i class="fa-solid fa-clipboard-check"></i></div>
                <div class="wiz-step-head-txt">
                  <h2>Confirmación</h2>
                  <p>Revise los datos y confirme la reserva</p>
                </div>
              </div>
              <div style="padding:24px" id="wiz6Body">
                <div class="step6-layout">
                  <div>
                    <dl class="resumen-card" id="resumen"></dl>
                    <div class="upload-zone" id="uploadBox">
                      <label id="reciboLabel" for="fRecibo">
                        <i class="fa-solid fa-paperclip"></i> Subir foto del recibo
                      </label>
                      <input id="fRecibo" type="file" accept="image/*" onchange="CE.cargarRecibo(this)">
                      <img id="reciboPrev" class="prev" alt="Vista previa del recibo">
                      <p id="uploadHint" class="upload-hint">Formatos: JPG, PNG · La reserva queda <b>pendiente</b> hasta verificar el pago.</p>
                    </div>
                  </div>
                  <div class="qr-card">
                    <div class="qrbox">
                      @if(!empty($qrUrl))
                      <img src="{{ $qrUrl }}" alt="QR de pago" style="cursor:zoom-in" title="Toca para ampliar"
                           onclick="document.getElementById('modalImg').src=this.src; document.getElementById('modal').classList.add('open')">
                      @else
                      <svg viewBox="0 0 100 100" role="img" aria-label="QR de pago">
                        <rect width="100" height="100" fill="#fff"/>
                        <g fill="#0f172a">
                          <rect x="8" y="8" width="24" height="24"/><rect x="12" y="12" width="16" height="16" fill="#fff"/><rect x="16" y="16" width="8" height="8"/>
                          <rect x="68" y="8" width="24" height="24"/><rect x="72" y="12" width="16" height="16" fill="#fff"/><rect x="76" y="16" width="8" height="8"/>
                          <rect x="8" y="68" width="24" height="24"/><rect x="12" y="72" width="16" height="16" fill="#fff"/><rect x="16" y="76" width="8" height="8"/>
                          <rect x="40" y="8" width="6" height="6"/><rect x="52" y="14" width="6" height="6"/><rect x="40" y="26" width="6" height="6"/>
                          <rect x="8" y="40" width="6" height="6"/><rect x="20" y="46" width="6" height="6"/><rect x="32" y="40" width="6" height="6"/>
                          <rect x="44" y="44" width="12" height="12"/><rect x="62" y="40" width="6" height="6"/><rect x="74" y="46" width="6" height="6"/>
                          <rect x="86" y="40" width="6" height="6"/><rect x="40" y="62" width="6" height="6"/><rect x="52" y="68" width="6" height="6"/>
                          <rect x="64" y="62" width="6" height="6"/><rect x="76" y="68" width="6" height="6"/><rect x="86" y="74" width="6" height="6"/>
                          <rect x="40" y="80" width="6" height="6"/><rect x="52" y="86" width="6" height="6"/><rect x="64" y="80" width="12" height="6"/>
                        </g>
                      </svg>
                      @endif
                    </div>
                    <p class="qr-label">Escanee para pagar</p>
                    @if(!empty($qrUrl))
                    <div style="display:flex;gap:8px;justify-content:center;flex-wrap:wrap;margin-top:8px">
                      <button type="button" style="display:inline-flex;align-items:center;gap:6px;background:#fff;color:#1a2e4a;border:1.5px solid var(--line);border-radius:8px;padding:8px 14px;font-size:13px;font-weight:600;cursor:pointer"
                              onclick="document.getElementById('modalImg').src='{{ $qrUrl }}'; document.getElementById('modal').classList.add('open')">
                        <i class="fa-solid fa-magnifying-glass-plus"></i> Ver grande
                      </button>
                      <a href="{{ $qrUrl }}" download="qr-pago.png" style="display:inline-flex;align-items:center;gap:6px;background:#1a2e4a;color:#fff;border-radius:8px;padding:8px 14px;font-size:13px;font-weight:600;text-decoration:none">
                        <i class="fa-solid fa-download"></i> Descargar QR
                      </a>
                    </div>
                    @endif
                    <div class="qr-contact">¿Problemas?<b><i class="fa-brands fa-whatsapp"></i> +591 609-75799</b></div>
                  </div>
                </div>
                <div id="wizErr6" class="wiz-err hidden"><i class="fa-solid fa-circle-exclamation"></i> <span id="wizErr6Txt"></span></div>
              </div>
            </div>

          </div><!-- /wiz-track -->

          <!-- Footer de navegación -->
          <div class="wiz-foot" id="wizFoot">
            <button class="btn-wiz-prev hidden" id="wizPrevBtn" onclick="CE.wizPrev()">
              <i class="fa-solid fa-arrow-left"></i> Anterior
            </button>
            <div></div>
            <button class="btn-wiz-next" id="wizNextBtn" onclick="CE.wizNext()">
              Siguiente <i class="fa-solid fa-arrow-right"></i>
            </button>
          </div>
        </div><!-- /wiz-card -->
      </div><!-- /wizard-wrap -->
    </section>

    <!-- ================================================================
         SECCIÓN: CALENDARIO MENSUAL
    ================================================================ -->
    <section id="secCal" class="card hidden">
      <div class="panel-head">
        <div class="ph-icon navy"><i class="fa-regular fa-calendar"></i></div>
        <div>
          <h1>Calendario de cirugías</h1>
          <p>Visualice la ocupación de los quirófanos por día</p>
        </div>
      </div>
      <div class="panel-body">
        <div class="month-cal-head">
          <h2 id="calTitulo"></h2>
          <div class="month-nav">
            <button onclick="CE.navCal(-1)"><i class="fa-solid fa-chevron-left"></i></button>
            <button class="hoy" onclick="CE.navCal(0)">Hoy</button>
            <button onclick="CE.navCal(1)"><i class="fa-solid fa-chevron-right"></i></button>
          </div>
        </div>
        <div class="month-grid" id="calGrid"></div>
        <div class="cal-legend" id="calLeyenda"></div>
        <div class="cal-det hidden" id="calDet"></div>
      </div>
    </section>

  </main>
</div>

<!-- Modal de imagen -->
<div class="modal" id="modal" onclick="this.classList.remove('open')">
  <img id="modalImg" alt="Recibo ampliado">
</div>
<div class="toast" id="toast"><i class="fa-solid fa-circle-check"></i> <span id="toastMsg"></span></div>

<script>
  window.CE_CONFIG = {
    storeUrl: @json(route('cirugias-externas.public.store')),
    agendaUrl: @json(route('cirugias-externas.public.agenda')),
    csrf: @json(csrf_token()),
    moneda: 'Bs',
    nocturnoFin: 6,
    descuentoNoct: 0.10,
    whatsapp: '59160975799',
    tipos: @json($tiposJs),
    quirofanos: @json($quirofanosJs),
  };
</script>
<script src="{{ asset('js/cirugias-externas.js') }}?v={{ $ver }}"></script>
</body>
</html>
