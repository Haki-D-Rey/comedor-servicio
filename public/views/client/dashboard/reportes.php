<?php
ob_start();
?>
<style>
     .container {
      width: 80%;
      margin: 50px auto;
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      padding: 20px;
    }
    .header {
      text-align: center;
      margin-bottom: 20px;
    }
    .report-list {
      list-style: none;
      padding: 0;
    }
    .report-item {
      display: flex;
      justify-content: space-between;
      padding: 10px;
      margin-bottom: 10px;
      background-color: #fafafa;
      border-radius: 4px;
    }
    .report-item:hover {
      background-color: #f0f0f0;
    }
    .report-title {
      font-size: 18px;
      font-weight: bold;
    }
    .report-date {
      font-size: 14px;
      color: #777;
    }
    .actions {
      display: flex;
      gap: 10px;
    }
    .button {
      padding: 5px 10px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    .button:hover {
      background-color: #0056b3;
    }
</style>
<div class="container">
    <div class="header">
        <h1>Lista de Reportes</h1>
    </div>
    <ul class="report-list">
        <li class="report-item">
            <div>
                <div class="report-title">Reporte de Ventas - Octubre</div>
                <div class="report-date">Fecha: 10/10/2024</div>
            </div>
            <div class="actions">
                <button class="button">Ver</button>
                <button class="button">Descargar</button>
            </div>
        </li>
        <li class="report-item">
            <div>
                <div class="report-title">Reporte de Inventario</div>
                <div class="report-date">Fecha: 01/11/2024</div>
            </div>
            <div class="actions">
                <button class="button">Ver</button>
                <button class="button">Descargar</button>
            </div>
        </li>
        <!-- Más reportes aquí -->
    </ul>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layout/layout.php'; ?>