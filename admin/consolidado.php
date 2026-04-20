<?php
require_once 'conection.php';

$con = connect();

$selectedIdConfig = null;
$results = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedIdConfig = isset($_POST['idconfigfact']) ? (int)$_POST['idconfigfact'] : 0;

    $sql = "
SELECT *
FROM (
SELECT
YEAR(s.fecha_creada) AS anio,
MONTH(s.fecha_creada) AS mes,
s.idconfigfact AS idconfigfact,
SUM(
CASE
WHEN s.tipo_moneda = 'CRC' THEN
CASE WHEN s.tipo_documento = '03' THEN -s.total_neto ELSE s.total_neto END
ELSE
CASE WHEN s.tipo_documento = '03' THEN -(s.total_neto * s.tipo_cambio)
ELSE (s.total_neto * s.tipo_cambio)
END
END
) AS total_mes
FROM sales s
JOIN facelectron f ON f.idsales = s.idsale
WHERE s.idconfigfact = ?
AND s.fecha_creada > '2025-01-01'
AND f.estatushacienda = 'aceptado'
AND s.tipo_documento != '08'
GROUP BY YEAR(s.fecha_creada), MONTH(s.fecha_creada)
) AS detalle
UNION ALL
SELECT
s.idconfigfact AS idconfigfact,
NULL AS anio,
NULL AS mes,
SUM(
CASE
WHEN s.tipo_moneda = 'CRC' THEN
CASE WHEN s.tipo_documento = '03' THEN -s.total_neto ELSE s.total_neto END
ELSE
CASE WHEN s.tipo_documento = '03' THEN -(s.total_neto * s.tipo_cambio)
ELSE (s.total_neto * s.tipo_cambio)
END
END
) AS total_mes
FROM sales s
JOIN facelectron f ON f.idsales = s.idsale
WHERE s.idconfigfact = ?
AND s.fecha_creada > '2025-01-01'
AND s.tipo_documento != '08'
AND f.estatushacienda = 'aceptado';
";

    $stmt = $con->prepare($sql);
    if (!$stmt) {
        die('Error en la preparación de la consulta: ' . $con->error);
    }
    $stmt->bind_param('ii', $selectedIdConfig, $selectedIdConfig);
    $stmt->execute();
    $res = $stmt->get_result();
    $results = $res->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Consolidado de Ventas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f2f2f2; }
        .container { max-width: 900px; margin: 0 auto; }
        .form-group { margin-bottom: 12px; }
    </style>
</head>
<body>
<div class="container">
    <h1>Consulta por idconfigfact</h1>

    <form method="post" action="">
           <div class="col-md-3 form-group">
		  <select class="select2 form-control" data-rel="chosen"  name="idconfigfact" id="idconfigfact">
                            <?php 
                              $categories = mysqli_query($con,"select * from configuracion");
                                while ($row = $categories->fetch_assoc()) {
                                    echo "<option value=".$row["idconfigfact"].">".$row["idconfigfact"]."->".$row["nombre_emisor"]."</option>";
                                }
                            ?>
                        </select>
		  </div> 
        <button type="submit">Ejecutar</button>
    </form>

<?php if ($_SERVER['REQUEST_METHOD'] === 'POST') : ?>
    <?php if (!empty($results)) : ?>
        <h2>Resultados</h2>
        <table>
            <thead>
                <tr>
                <th>Confi</th>
                    <th>anio</th>
                    <th>mes</th>
                    <th>total_mes</th>
                </tr>
            </thead>
            <tbody>
               
            <?php foreach ($results as $row) : 
    $id = $row['idconfigfact'] ?? null;
    $cliente = '';

    if ($id !== null) {
        // Consulta preparada para obtener el nombre_emisor asociado a idconfigfact
        if ($stmt2 = $con->prepare("SELECT nombre_emisor FROM configuracion WHERE idconfigfact = ? LIMIT 1")) {
            $stmt2->bind_param('i', $id);
            $stmt2->execute();
            $res2 = $stmt2->get_result();
            if ($rowss = $res2->fetch_assoc()) {
                $cliente = $rowss['nombre_emisor'] ?? '';
            }
            $stmt2->close();
        } else {
            // En caso de fallo, dejar $cliente vacío
            $cliente = '';
        }
    }

    ?>
    <tr>
        <td><?= htmlspecialchars($cliente ?? '') ?></td>
        <td><?= htmlspecialchars($row['anio'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['mes'] ?? '') ?></td>
        <td><?= number_format($row['total_mes'] ?? 0, 2, '.', ',') ?></td>
    </tr>
<?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No se obtuvieron resultados.</p>
    <?php endif; ?>
<?php endif; ?>

</div>
</body>
</html>