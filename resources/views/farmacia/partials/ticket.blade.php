{{-- ════════════════════════════════════════════════════════════
     Ticket de farmacia — fuente ÚNICA del comprobante impreso.
     Usado por el POS (punto-venta) y por la reimpresión (ventas).
     Recibe un modelo normalizado, ver imprimirTicketFarmacia(t).
════════════════════════════════════════════════════════════ --}}
<script>
    // Datos de la clínica (editar aquí cuando se tengan los definitivos).
    const TICKET_CLINICA = {
        nombre: 'Clínica Santa Cruz',
        ciudad: 'Santa Cruz de la Sierra - Bolivia',
        telefono: '',   // ej: 'Tel. (3) 000-0000'
        nit: ''         // ej: 'NIT 0000000000'
    };

    // Logo de la clínica. Archivo: public/images/logocelular.png
    // Se usa URL absoluta porque el ticket se abre en una ventana en blanco (document.write).
    const TICKET_LOGO_URL = window.location.origin + '/images/logocelular.png';
    const TICKET_LOGO = `<img class="t-logo" src="${TICKET_LOGO_URL}" width="90" alt="">`;

    /**
     * Imprime el ticket. Modelo normalizado (t):
     *  { codigo, fecha, cliente, metodoPago, requiereReceta,
     *    conCreditoFiscal, razonSocial, docLabel, docNumero, docComplemento,
     *    items: [{ cantidad, nombre, importe }], total, reimpresion }
     */
    function imprimirTicketFarmacia(t) {
        const money = (n) => 'Bs ' + (parseFloat(n) || 0).toFixed(2);

        let filas = '';
        (t.items || []).forEach(it => {
            filas += `<tr>
                <td class="cant">${it.cantidad} x</td>
                <td>${it.nombre}</td>
                <td class="r">${money(it.importe)}</td>
            </tr>`;
        });

        const comp = t.docComplemento ? '-' + t.docComplemento : '';
        let docHTML = '';
        if (t.conCreditoFiscal) {
            if (t.razonSocial && t.razonSocial !== t.cliente) {
                docHTML += `<div class="t-row"><span>Razon social:</span><span>${t.razonSocial}</span></div>`;
            }
            docHTML += `<div class="t-row"><span>${t.docLabel}:</span><span>${t.docNumero}${comp}</span></div>`;
        } else {
            docHTML += `<div class="t-row"><span>NIT/CI:</span><span>0 - S/N</span></div>`;
        }

        const recetaHTML = t.requiereReceta
            ? '<div class="t-receta">REQUIERE RECETA MEDICA</div>'
            : '';

        const lineasClinica = [
            `<div class="t-sub">${TICKET_CLINICA.ciudad}</div>`,
            TICKET_CLINICA.telefono ? `<div class="t-sub">${TICKET_CLINICA.telefono}</div>` : '',
            TICKET_CLINICA.nit ? `<div class="t-sub">${TICKET_CLINICA.nit}</div>` : ''
        ].join('');

        const tituloTicket = t.reimpresion ? 'Comprobante de venta (reimpresion)' : 'Comprobante de venta';

        const html = `
            <style>
                * { box-sizing: border-box; }
                body { font-family: 'Courier New', Courier, monospace; color: #000; margin: 0; padding: 14px 12px; width: 80mm; }
                .t-center { text-align: center; }
                .t-logo { display: block; margin: 0 auto 6px; height: auto; max-width: 60%; }
                .t-title { font-size: 22px; font-weight: 800; letter-spacing: 2px; text-transform: uppercase; margin: 2px 0; }
                .t-tag { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 4px; }
                .t-sub { font-size: 11px; margin: 1px 0; }
                .t-rule { border: 0; border-top: 1px dashed #000; margin: 8px 0; }
                .t-row { display: flex; justify-content: space-between; gap: 10px; font-size: 12px; margin: 2px 0; }
                .t-bold { font-weight: 700; }
                table.t-items { width: 100%; border-collapse: collapse; font-size: 12px; }
                table.t-items th { text-align: left; font-weight: 700; text-transform: uppercase; font-size: 11px; letter-spacing: 1px; padding-bottom: 4px; border-bottom: 1px dashed #000; }
                table.t-items th.r, table.t-items td.r { text-align: right; white-space: nowrap; }
                table.t-items td { padding: 2px 0; vertical-align: top; }
                table.t-items td.cant { white-space: nowrap; padding-right: 8px; }
                .t-total { display: flex; justify-content: space-between; align-items: baseline; font-weight: 800; font-size: 18px; letter-spacing: 1px; }
                .t-receta { text-align: center; font-weight: 700; font-size: 11px; margin: 4px 0; }
                .t-foot { font-size: 9px; text-align: center; margin-top: 8px; line-height: 1.4; }
                .t-mark { text-align: center; font-weight: 800; font-size: 11px; letter-spacing: 3px; margin-top: 6px; }
            </style>
            <div class="t-center">
                ${TICKET_LOGO}
                <div class="t-title">${TICKET_CLINICA.nombre}</div>
                <div class="t-tag">${tituloTicket}</div>
                ${lineasClinica}
            </div>
            <hr class="t-rule">
            <div class="t-row"><span class="t-bold">ORDEN</span><span class="t-bold">${t.codigo}</span></div>
            <div class="t-row"><span>${t.fecha || ''}</span></div>
            <div class="t-row"><span>Cliente:</span><span>${t.cliente || 'Cliente General'}</span></div>
            ${docHTML}
            <div class="t-row"><span>Pago:</span><span>${t.metodoPago || ''}</span></div>
            ${recetaHTML}
            <hr class="t-rule">
            <table class="t-items">
                <thead><tr><th>Cant</th><th>Descr</th><th class="r">Imp</th></tr></thead>
                <tbody>${filas}</tbody>
            </table>
            <hr class="t-rule">
            <div class="t-total"><span>TOTAL :</span><span>${money(t.total)}</span></div>
            <hr class="t-rule">
            <div class="t-foot">Comprobante interno - no valido como factura.<br>Gracias por su preferencia.</div>
            <div class="t-mark">SC</div>
        `;

        const win = window.open('', '_blank', 'width=420,height=640,toolbar=0,menubar=0,location=0');
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
