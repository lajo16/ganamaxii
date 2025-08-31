<?php
require_once __DIR__ . '/../includes/db.php';

$op = $_GET['tipo_operacion'] ?? '';
$op = trim($op);

// Si no llega operacion, devolver vacío
if ($op === '') { echo json_encode([]); exit; }

$sql = "SELECT DISTINCT departamento
        FROM propiedades
        WHERE departamento <> '' AND tipo_operacion = ?
        ORDER BY departamento";

$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $op);
$stmt->execute();
$res = $stmt->get_result();

$out = [];
while ($r = $res->fetch_assoc()) { $out[] = $r['departamento']; }

$stmt->close();
header('Content-Type: application/json; charset=utf-8');
echo json_encode($out);
