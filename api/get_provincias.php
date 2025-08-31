<?php
require_once __DIR__ . '/../includes/db.php';

$dep = $_GET['departamento']   ?? '';
$op  = $_GET['tipo_operacion'] ?? '';

if ($dep === '' || $op === '') { echo json_encode([]); exit; }

$sql = "SELECT DISTINCT provincia
        FROM propiedades
        WHERE provincia <> ''
          AND departamento = ?
          AND tipo_operacion = ?
        ORDER BY provincia";

$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $dep, $op);
$stmt->execute();
$res = $stmt->get_result();

$out = [];
while ($r = $res->fetch_assoc()) { $out[] = $r['provincia']; }

$stmt->close();
header('Content-Type: application/json; charset=utf-8');
echo json_encode($out);
