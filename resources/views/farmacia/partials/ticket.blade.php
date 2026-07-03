{{-- ════════════════════════════════════════════════════════════
     Ticket de farmacia — fuente ÚNICA del comprobante impreso.
     Usado por el POS (punto-venta) y por la reimpresión (ventas).
     Recibe un modelo normalizado, ver imprimirTicketFarmacia(t).

     Contabilidad boliviana: el IVA (13%) es "por dentro" — el precio de
     venta YA lo incluye. Por eso el ticket muestra el IVA como "incluido"
     (no se suma por fuera): Subtotal − Descuento = Importe Total, y el IVA
     es informativo (débito fiscal = 13% del total).
════════════════════════════════════════════════════════════ --}}
<script>
    // Datos de la clínica (editar aquí cuando se tengan los definitivos).
    const TICKET_CLINICA = {
        nombre: 'Clínica de Especialidades Medicas Santa Cruz',
        direccion: 'Av. Monseñor Santistevan #591 Esquina Caller Bumberque',  // ej: 'Av. Principal #123'
        ciudad: 'Santa Cruz de la Sierra - Bolivia',
        telefono: '+59175662703',   // ej: '(3) 000-0000'
        nit: '497970026'         // ej: '0000000000'
    };

    // Logo de la clínica. Archivo: public/images/logocelular.png
    // Se usa URL absoluta porque el ticket se abre en una ventana en blanco (document.write).
    const TICKET_LOGO_URL = window.location.origin + '/images/logocelular.png';
    const TICKET_LOGO = `<img class="t-logo" src="${TICKET_LOGO_URL}" alt="">`;

    /**
     * Imprime el ticket. Modelo normalizado (t):
     *  { codigo, fecha, cliente, metodoPago, requiereReceta,
     *    conCreditoFiscal, razonSocial, docLabel, docNumero, docComplemento,
     *    items: [{ cantidad, nombre, precioUnitario, descuento, importe }],
     *    total, reimpresion,
     *    anulada, motivoAnulacion, fechaAnulacion }   // venta devuelta/anulada
     */
    function imprimirTicketFarmacia(t) {
        const num = (n) => parseFloat(n) || 0;
        const fmt = (n) => num(n).toFixed(2);
        const money = (n) => 'Bs ' + fmt(n);

        // Totales (IVA por dentro: el importe ya incluye el 13%).
        let subtotal = 0, descuentoTotal = 0;
        (t.items || []).forEach(it => {
            subtotal += num(it.importe) + num(it.descuento);
            descuentoTotal += num(it.descuento);
        });
        const total = num(t.total);
        const iva = num((total * 0.13).toFixed(2)); // débito fiscal informativo

        let filas = '';
        (t.items || []).forEach(it => {
            filas += `<tr>
                <td class="concepto">${it.nombre}</td>
                <td class="c">${num(it.cantidad)}</td>
                <td class="r">${fmt(it.precioUnitario)}</td>
                <td class="r">${fmt(it.descuento)}</td>
                <td class="r">${fmt(it.importe)}</td>
            </tr>`;
        });

        // Datos fiscales del receptor.
        const comp = t.docComplemento ? '-' + t.docComplemento : '';
        let docHTML = '';
        if (t.conCreditoFiscal) {
            if (t.razonSocial && t.razonSocial !== t.cliente) {
                docHTML += `<div class="t-row"><span>Razón social:</span><span class="t-strong">${t.razonSocial}</span></div>`;
            }
            docHTML += `<div class="t-row"><span>${t.docLabel}:</span><span class="t-strong">${t.docNumero}${comp}</span></div>`;
        } else {
            docHTML += `<div class="t-row"><span>NIT/CI:</span><span>0 - S/N</span></div>`;
        }

        const recetaHTML = t.requiereReceta
            ? '<div class="t-receta">★ REQUIERE RECETA MÉDICA ★</div>'
            : '';

        // Bloque de empresa: ubicación + teléfono + (NIT | fecha).
        const ubicacion = TICKET_CLINICA.direccion
            ? `${TICKET_CLINICA.direccion} · ${TICKET_CLINICA.ciudad}`
            : TICKET_CLINICA.ciudad;
        const telLinea = TICKET_CLINICA.telefono ? `<div>Tel: ${TICKET_CLINICA.telefono}</div>` : '';
        const nitEmpresa = TICKET_CLINICA.nit ? TICKET_CLINICA.nit : '—';

        const tituloTicket = t.reimpresion ? 'COMPROBANTE DE VENTA (REIMPRESIÓN)' : 'COMPROBANTE DE VENTA';

        // Venta anulada (devolución con reingreso de stock): el ticket reimpreso
        // DEBE decirlo — sin esto parecería un comprobante de venta válido.
        const anuladaHTML = t.anulada
            ? `<div class="t-anulada">*** VENTA ANULADA ***
                   <div class="t-anulada-sub">${t.fechaAnulacion ? 'Anulada el ' + t.fechaAnulacion : ''}${t.motivoAnulacion ? ' — ' + t.motivoAnulacion : ''}</div>
                   <div class="t-anulada-sub">Dinero devuelto · Stock reingresado · No representa un ingreso</div>
               </div>`
            : '';
        const descuentoFila = descuentoTotal > 0
            ? `<div class="t-trow"><span>Descuento</span><span>${money(descuentoTotal)}</span></div>`
            : '';

        const html = `
            <style>
                * { box-sizing: border-box; }
                body { font-family: 'Courier New', Courier, monospace; color: #111; margin: 0; padding: 12px 8px; width: 80mm; font-size: 11px; line-height: 1.35; }

                /* Cabecera: logo izquierda, nombre derecha */
                .t-head { display: flex; align-items: center; gap: 8px; padding-bottom: 8px; border-bottom: 2px solid #111; }
                .t-logo { width: 46px; height: auto; flex-shrink: 0; }
                .t-head-info { flex: 1; text-align: right; }
                .t-name { font-size: 15px; font-weight: 800; letter-spacing: 0.5px; text-transform: uppercase; }
                .t-tag { font-size: 8px; font-weight: 700; letter-spacing: 1px; color: #444; margin-top: 2px; }

                /* Bloque empresa */
                .t-empresa { text-align: center; font-size: 10px; margin: 6px 0; }
                .t-empresa .t-row { justify-content: center; gap: 14px; }

                .t-row { display: flex; justify-content: space-between; gap: 10px; margin: 1.5px 0; }
                .t-row span:last-child { text-align: right; }
                .t-strong { font-weight: 400; }
                .t-rule { border: 0; border-top: 1px dashed #555; margin: 7px 0; }

                /* Datos del cliente — etiqueta (subtítulo) en negrita, valor normal */
                .t-datos { font-size: 10.5px; }
                .t-datos .t-row span:first-child { font-weight: 700; }
                .t-datos .t-row span:last-child { font-weight: 400; }
                .t-datos .t-bignum { font-weight: 400; font-size: 12px; letter-spacing: 0.5px; }

                /* Tabla de ítems */
                table.t-items { width: 100%; border-collapse: collapse; table-layout: fixed; font-size: 9px; margin-top: 2px; }
                table.t-items th { text-align: right; font-weight: 700; text-transform: uppercase; font-size: 8px; letter-spacing: 0.3px; padding: 0 0 4px; border-bottom: 1px solid #111; }
                table.t-items th.l { text-align: left; }
                table.t-items th.c { text-align: center; }
                table.t-items td { padding: 3px 0; vertical-align: top; text-align: right; word-wrap: break-word; }
                table.t-items td.concepto { text-align: left; padding-right: 4px; }
                table.t-items td.c { text-align: center; }
                table.t-items tbody tr + tr td { border-top: 1px dotted #ccc; }

                /* Totales — etiqueta (subtítulo) en negrita, monto normal */
                .t-totales { margin-top: 8px; padding-top: 6px; border-top: 1px dashed #555; }
                .t-trow { display: flex; justify-content: space-between; font-size: 11px; margin: 2px 0; }
                .t-trow span:first-child { font-weight: 700; }
                .t-trow span:last-child { font-weight: 400; }
                .t-trow.iva { color: #444; font-size: 10px; }
                .t-total-final { display: flex; justify-content: space-between; align-items: baseline; margin-top: 5px; padding-top: 5px; border-top: 2px solid #111; font-weight: 800; font-size: 15px; letter-spacing: 0.5px; }

                .t-receta { text-align: center; font-weight: 700; font-size: 10px; margin: 6px 0; padding: 3px; border: 1px dashed #111; }
                .t-anulada { text-align: center; font-weight: 800; font-size: 12.5px; letter-spacing: 1px; margin: 7px 0; padding: 5px 3px; border: 2px solid #111; }
                .t-anulada-sub { font-weight: 400; font-size: 8.5px; letter-spacing: 0; margin-top: 2px; }
                .t-foot { font-size: 8.5px; text-align: center; margin-top: 10px; line-height: 1.5; color: #333; font-weight: 400; }
                .t-foot .t-gracias { font-weight: 400; font-size: 9.5px; color: #111; margin-top: 3px; }
            </style>

            <div class="t-head">
                ${TICKET_LOGO}
                <div class="t-head-info">
                    <div class="t-name">${TICKET_CLINICA.nombre}</div>
                    <div class="t-tag">${tituloTicket}</div>
                </div>
            </div>

            <div class="t-empresa">
                <div>${ubicacion}</div>
                ${telLinea}
                <div class="t-row"><span>NIT: ${nitEmpresa}</span><span>${t.fecha || ''}</span></div>
            </div>

            <hr class="t-rule">
            ${anuladaHTML}
            <div class="t-datos">
                <div class="t-row"><span>Comprobante N°:</span><span class="t-bignum">${t.codigo || ''}</span></div>
                <div class="t-row"><span>Cliente:</span><span class="t-strong">${t.cliente || 'Cliente General'}</span></div>
                ${docHTML}
                <div class="t-row"><span>Forma de pago:</span><span style="text-transform:capitalize">${t.metodoPago || ''}</span></div>
            </div>
            ${recetaHTML}

            <hr class="t-rule">

            <table class="t-items">
                <colgroup>
                    <col style="width:34%"><col style="width:12%"><col style="width:18%"><col style="width:14%"><col style="width:22%">
                </colgroup>
                <thead>
                    <tr>
                        <th class="l">Concepto</th>
                        <th class="c">Cant.</th>
                        <th>P.U.</th>
                        <th>Desc.</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>${filas}</tbody>
            </table>

            <div class="t-totales">
                <div class="t-trow"><span>Subtotal</span><span>${money(subtotal)}</span></div>
                ${descuentoFila}
                <div class="t-trow iva"><span>IVA (13%) incluido</span><span>${money(iva)}</span></div>
                <div class="t-total-final"><span>IMPORTE TOTAL</span><span>${money(total)}</span></div>
            </div>

            <div class="t-foot">
                Comprobante interno · No válido como factura.
                <div class="t-gracias">¡Gracias por su preferencia!</div>
            </div>
        `;

        const win = window.open('', '_blank', 'width=420,height=680,toolbar=0,menubar=0,location=0');
        win.document.write(html);
        win.document.close();
        win.focus();

        // Esperar a que cargue el logo antes de imprimir (con fallback si falta/404).
        const lanzar = () => { try { win.focus(); win.print(); } catch (e) {} };
        const img = win.document.querySelector('img.t-logo');
        if (img && !img.complete) {
            img.addEventListener('load', lanzar);
            img.addEventListener('error', lanzar);
            setTimeout(lanzar, 1500);
        } else {
            setTimeout(lanzar, 300);
        }
    }
</script>
