<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Factura INV_2025-00219</title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root {
      --primary-color: #f58220;
      --secondary-color: #0033cc;
      --dark-color: #000;
      --light-bg: #f8f9fa;
    }
    
    body {
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
      background-color: #e9ecef;
      padding: 2rem 0;
    }
    
    .invoice-container {
      background: white;
      max-width: 900px;
      margin: 0 auto;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
      overflow: hidden;
    }
    
    .invoice-header {
      padding: 2rem;
      border-bottom: 1px solid #dee2e6;
    }
    
    .logo {
      font-size: 2.2rem;
      font-weight: 700;
      color: var(--dark-color);
      transition: all 0.3s ease;
    }
    
    .logo:hover {
      transform: translateY(-2px);
    }
    
    .logo span {
      color: var(--primary-color);
    }
    
    .invoice-title {
      font-size: 1.5rem;
      font-weight: 600;
      color: var(--secondary-color);
    }
    
    .invoice-info {
      text-align: right;
      font-size: 0.9rem;
      line-height: 1.6;
      color: var(--secondary-color);
    }
    
    .info-section {
      padding: 2rem;
    }
    
    .sender-box {
      background-color: var(--light-bg);
      padding: 1.25rem;
      border-radius: 6px;
      height: 100%;
    }
    
    .recipient-box {
      border: 1px solid var(--dark-color);
      padding: 1.25rem;
      border-radius: 6px;
      height: 100%;
    }
    
    .section-title {
      font-size: 1rem;
      font-weight: 600;
      margin-bottom: 1rem;
      color: var(--dark-color);
    }
    
    .items-table {
      margin-top: 2rem;
      width: 100%;
      min-height: 300px; /* Altura aumentada significativamente */
    }
    
    .items-table thead th {
      background-color: var(--light-bg);
      border: 1px solid var(--dark-color);
      font-weight: 600;
      padding: 12px;
      height: 60px; /* Altura aumentada para el encabezado */
    }
    
    .items-table tbody td {
      border: 1px solid var(--dark-color);
      padding: 15px; /* Padding aumentado */
      vertical-align: top;
    }
    
    .text-right {
      text-align: right;
    }
    
    .totals-table {
      width: 300px;
      margin: 20px 0 0 auto; /* Margen superior aumentado */
    }
    
    .totals-table td {
      padding: 10px 12px;
      border: 1px solid #dee2e6;
    }
    
    .totals-table tr td:first-child {
      background-color: var(--light-bg);
      font-weight: 500;
    }
    
    .totals-table tr:last-child td {
      font-weight: 600;
    }
    
    .invoice-footer {
      padding: 1rem 2rem;
      border-top: 1px solid #dee2e6;
      font-size: 0.85rem;
    }
    
    a {
      color: var(--dark-color);
      text-decoration: none;
      transition: color 0.2s ease;
    }
    
    a:hover {
      color: var(--primary-color);
    }
    
    .highlight {
      color: var(--primary-color);
      font-weight: 600;
    }
    
    .description-cell {
      min-height: 250px; /* Altura mínima muy aumentada para la celda de descripción */
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

<div class="container">
  <div class="invoice-container">
    <!-- Header -->
    <div class="invoice-header">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="logo mb-2">Innova<span>Byte</span></div>
          <div class="text-muted small">C/Primer de Maig, 9 · 46139 La Pobla de Farnals · Valencia</div>
        </div>
        <div class="invoice-info">
          <div class="invoice-title mb-2">Factura INV_2025-00219</div>
          <div>Fecha facturación: <span class="highlight">10/07/2025</span></div>
          <div>Fecha de vencimiento: <span class="highlight">20/07/2025</span></div>
          <div>Código cliente: <span class="highlight">CU2505-00002</span></div>
        </div>
      </div>
    </div>
    
    <!-- Sender/Recipient Info -->
    <div class="info-section">
      <div class="row g-4">
        <div class="col-md-6">
          <div class="sender-box">
            <h4 class="section-title">Emisor</h4>
            <p class="mb-2"><strong>Luis C. Pérez Mataix</strong></p>
            <p class="mb-2">C/Primer de Maig, 9<br>46139 La Pobla de Farnals<br>Valencia</p>
            <p class="mb-1">Correo: <a href="mailto:luiscarlos@innovabyte.es">luiscarlos@innovabyte.es</a></p>
            <p class="mb-0">Web: <a href="https://innovabyte.es" target="_blank">https://innovabyte.es</a></p>
          </div>
        </div>
        <div class="col-md-6">
          <div class="recipient-box">
            <h4 class="section-title">Enviar a</h4>
            <p class="mb-2"><strong>Esther Lecumberri Militino</strong></p>
            <p class="mb-2">C/ Irunlarrea 32-2 izda<br>31008 Pamplona<br>Navarra</p>
            <p class="mb-0">CIF/NIF: <span class="highlight">44614967G</span></p>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Items Table -->
    <div class="info-section">
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
              <strong>Diseño y desarrollo de una plataforma integral para tienda especializada en lanas y artículos textiles, combinando la venta en línea de productos con la gestión y promoción de eventos relacionados con el sector, según presupuesto presentado el 01 de abril de 2025.</strong>
              <div class="mt-3"><span class="badge bg-light text-dark">Pago 3/6</span></div>
            </td>
            <td class="text-right">21%</td>
            <td class="text-right">700,00 €</td>
            <td class="text-right">1</td>
            <td class="text-right">700,00 €</td>
          </tr>
        </tbody>
      </table>
      
      <!-- Totals -->
      <table class="totals-table">
        <tr>
          <td>Total (Base imp.)</td>
          <td class="text-right">700,00 €</td>
        </tr>
        <tr>
          <td>Total IVA 21%</td>
          <td class="text-right">147,00 €</td>
        </tr>
        <tr>
          <td>Total</td>
          <td class="text-right">847,00 €</td>
        </tr>
      </table>
    </div>
    
    <!-- Footer simplificado -->
    <div class="invoice-footer">
      <p class="mb-0">Gracias por su confianza en nuestros servicios.</p>
    </div>
  </div>
</div>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>