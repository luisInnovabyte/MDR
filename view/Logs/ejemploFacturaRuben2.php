<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Factura INV_2025-00219</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root {
      --primary-color: #f58220;
      --primary-light: #ffe8d6;
      --secondary-color: #0033cc;
      --dark-color: #222;
      --light-bg: #f8f9fa;
      --border-radius: 12px;
      --border-color: #d1d9e6;
    }

    body {
      font-family: 'Segoe UI', system-ui, sans-serif;
      background-color: #f5f7fa;
      padding: 1rem 0;
      color: var(--dark-color);
    }

    .invoice-container {
      background: white;
      max-width: 900px;
      margin: 0 auto;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
      border-radius: var(--border-radius);
      overflow: hidden;
    }

    .invoice-header {
      padding: 1.5rem 2rem;
      border-bottom: 1px solid var(--border-color);
      background: linear-gradient(135deg, #ffffff 0%, #f9f9f9 100%);
    }

    .logo {
      font-size: 2rem;
      font-weight: 800;
      color: var(--dark-color);
    }

    .logo span {
      color: var(--primary-color);
    }

    .invoice-title {
      font-size: 1.4rem;
      font-weight: 700;
      color: var(--secondary-color);
      margin-bottom: 0.5rem;
    }

    .invoice-info {
      text-align: right;
      font-size: 0.9rem;
      line-height: 1.6;
    }

    .info-section {
      padding: 1rem 1.5rem;
    }

    .sender-box,
    .recipient-box {
      padding: 1.2rem;
      border-radius: var(--border-radius);
    }

    .sender-box {
      background-color: var(--light-bg);
      border-left: 4px solid var(--primary-color);
    }

    .recipient-box {
      border: 1px solid var(--border-color);
      background-color: white;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
    }

    .section-title {
      font-size: 1.05rem;
      font-weight: 700;
      margin-bottom: 1rem;
      position: relative;
      padding-bottom: 0.5rem;
    }

    .section-title:after {
      content: '';
      position: absolute;
      left: 0;
      bottom: 0;
      width: 40px;
      height: 3px;
      background: var(--primary-color);
    }

    .items-table-container {
      margin-top: 2rem;
      border-radius: var(--border-radius);
      overflow: hidden;
      box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
      border: 1px solid var(--border-color);
    }

    .items-table {
      width: 100%;
      border-collapse: collapse;
      min-height: 450px;
    }

    .items-table thead th {
      background-color: var(--primary-light);
      border: 1px solid var(--border-color);
      font-weight: 700;
      padding: 1rem;
      font-size: 0.85rem;
      text-transform: uppercase;
      color: var(--dark-color);
    }

    .items-table tbody td {
      border: 1px solid var(--border-color);
      padding: 1.5rem 1rem;
      vertical-align: top;
      background: white;
    }

    .text-right {
      text-align: right;
      white-space: nowrap;
    }

    .totals-container {
      width: 100%;
      max-width: 350px;
      margin: 2rem 0 0 auto;
      border-radius: var(--border-radius);
      overflow: hidden;
      box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
      border: 1px solid var(--border-color);
    }

    .totals-table {
      width: 100%;
      border-collapse: collapse;
    }

    .totals-table td {
      padding: 0.9rem 1.2rem;
      border: 1px solid var(--border-color);
      background: white;
    }

    .totals-table tr td:first-child {
      background-color: var(--primary-light);
      font-weight: 600;
    }

    .totals-table tr:last-child td {
      font-weight: 700;
    }

    .invoice-footer {
      padding: 1rem 2rem;
      border-top: 1px solid var(--border-color);
      font-size: 0.85rem;
      text-align: center;
      color: #666;
    }

    .description-cell {
      line-height: 1.6;
    }

    .badge-payment {
      background-color: var(--primary-light);
      color: var(--primary-color);
      font-weight: 600;
      padding: 0.3rem 0.6rem;
      border-radius: 6px;
      display: inline-block;
      margin-top: 1rem;
    }

    @media (max-width: 576px) {
      .invoice-header, .info-section, .invoice-footer {
        padding: 1rem;
      }

      .invoice-title {
        font-size: 1.2rem;
      }

      .logo {
        font-size: 1.5rem;
      }

      .invoice-info {
        text-align: left;
        margin-top: 1rem;
      }

      .totals-container {
        max-width: 100%;
      }
    }

    @media print {
      body {
        background: none;
        padding: 0;
      }

      .invoice-container {
        box-shadow: none;
        border-radius: 0;
      }
    }
  </style>
</head>
<body>
  <!--
  ///////////////////////
  CLASES DINÁMICAS:
  ///////////////////////
  CLASES:
  - emisorEmpresa
  - correoEmisor
  - webEmisor LA WEB DEL EMISOR DUDO QUE SEA DINÁMICA, PERO LA PONGO IGUAL
  - calleEmpresa
  - codigoPostalEmpresa
  - puebloCiudadEmpresa
  - provinciaEmpresa
  - numeroFactura
  - fechaFacturacionFactura
  - fechaVencimientoFactura
  - codigoClienteFactura

  - nombreReceptor
  - calleReceptor
  - codigoPostalReceptor
  - puebloCiudadReceptor
  - provinciaReceptor
  - CIF_NIF_Receptor

  - descripcionFactura
  - pagoNumeroFactura
  - ivaFactura
  - PUFactura
  - cantidadFactura
  - baseImpFactura

  - totalBaseImpFactura
  - totalIVAFactura
  - totalFactura

  -->
  <div class="container-fluid">
    <div class="invoice-container">
      <!-- Header -->
      <div class="invoice-header d-flex flex-column flex-sm-row justify-content-between">
        <div>
          <div class="logo mb-2">Innova<span>Byte</span></div>
          <div class="text-muted" style="font-size: 0.9rem;"><label class="calleEmpresa">C/Primer de Maig, 9</label> · <label class="codigoPostalEmpresa"> 46139 </label><label class="puebloCiudadEmpresa"> La Pobla de Farnals </label> · <label class="provinciaEmpresa">Valencia</label></div>
        </div>
        <div class="invoice-info mt-3 mt-sm-0">
          <div class="invoice-title">Factura <label class="numeroFactura">INV_2025-00219</label></div>
          <div>Fecha facturación: <span class="highlight"><label class="fechaFacturacionFactura">10/07/2025</label></span></div>
          <div>Fecha de vencimiento: <span class="highlight"><label class="fechaVencimientoFactura">20/07/2025</label></span></div>
          <div>Código cliente: <span class="highlight"><label class="codigoClienteFactura">CU2505-00002</label></span></div>
        </div>
      </div>

      <!-- Info Section -->
      <div class="info-section">
        <div class="row g-3 align-items-stretch">
          <div class="col-12 col-md-6 d-flex">
            <div class="sender-box w-100 d-flex flex-column">
              <h4 class="section-title">Emisor</h4>
              <p class="mb-1"><strong><label class="emisorEmpresa">Luis C. Pérez Mataix</label></strong></p>
              <p class="mb-1" style="font-size: 0.9rem;"><label class="calleEmpresa">C/Primer de Maig, 9</label><br><label class="codigoPostalEmpresa">46139</label> <label class="puebloCiudadEmpresa"> La Pobla de Farnals </label><br><label class="provinciaEmpresa"> Valencia </label></p>
              <p class="mb-1" style="font-size: 0.9rem;">Correo: <label class="correoEmisor">luiscarlos@innovabyte.es</label></p>
              <p class="mb-0" style="font-size: 0.9rem;">Web: <a href="https://innovabyte.es" target="_blank"><label class="webEmisor">https://innovabyte.es</label></a></p>
            </div>
          </div>
          <div class="col-12 col-md-6 d-flex">
            <div class="recipient-box w-100 d-flex flex-column">
              <h4 class="section-title">Enviar a</h4>
              <p class="mb-1"><strong><label class="nombreReceptor">Esther Lecumberri Militino</label></strong></p>
              <p class="mb-1" style="font-size: 0.9rem;"><label class="calleReceptor"> C/ Irunlarrea 32-2 izda </label><br><label class="codigoPostalReceptor"> 31008 </label> <label class="puebloCiudadReceptor"> Pamplona </label><br><label class="provinciaReceptor"> Navarra </label></p>
              <p class="mb-0" style="font-size: 0.9rem;">CIF/NIF: <span class="highlight"><label class="CIF_NIF_Receptor">44614967G</label></span></p>
            </div>
          </div>
        </div>
      </div>

      <!-- Items Table -->
      <div class="info-section">
        <div class="items-table-container">
          <table class="items-table">
            <thead>
              <tr>
                <th>Descripción</th>
                <th class="text-right">IVA</th>
                <th class="text-right">P.U.</th>
                <th class="text-right">Cant.</th>
                <th class="text-right">Base imp.</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="description-cell">
                  <p class="mt-2 mb-0"><label class="descripcionFactura">Diseño y desarrollo de una plataforma integral para tienda especializada en lanas y artículos textiles combinando la venta en línea de productos con la gestión y promoción de eventos relacionados con el sector textil, según presupuesto presentado el 01 de abril de 2025.</label></p>
                  <span class="badge-payment">Pago <label class="pagoNumeroFactura">3/6</label></span>
                </td>
                <td class="text-right"><label class="ivaFactura">21</label> %</td>
                <td class="text-right"><label class="PUFactura">700,00</label> €</td>
                <td class="text-right"><label class="cantidadFactura">1</label></td>
                <td class="text-right"><label class="baseImpFactura">700,00</label> €</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Totals -->
        <div class="totals-container">
          <table class="totals-table">
            <tr>
              <td>Total (Base imp.)</td>
              <td class="text-right"><label class="totalBaseImpFactura">700,00</label> €</td>
            </tr>
            <tr>
              <td>Total IVA 21%</td>
              <td class="text-right"><label class="totalIVAFactura">147,00</label> €</td>
            </tr>
            <tr>
              <td>Total</td>
              <td class="text-right"><label class="totalFactura">847,00</label> €</td>
            </tr>
          </table>
        </div>
      </div>


      <!-- Footer -->
      <div class="invoice-footer">
        <p class="mb-0">Gracias por confiar en nuestros servicios profesionales</p>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
