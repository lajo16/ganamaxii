<?php
require_once __DIR__ . '/../includes/db.php';

$dep  = $_GET['departamento']   ?? '';
$prov = $_GET['provincia']      ?? '';
$op   = $_GET['tipo_operacion'] ?? '';

if ($dep === '' || $prov === '' || $op === '') { echo json_encode([]); exit; }

$sql = "SELECT DISTINCT distrito
        FROM propiedades
        WHERE distrito <> ''
          AND departamento = ?
          AND provincia     = ?
          AND tipo_operacion = ?
        ORDER BY distrito";

$stmt = $conn->prepare($sql);
$stmt->bind_param('sss', $dep, $prov, $op);
$stmt->execute();
$res = $stmt->get_result();

$out = [];
while ($r = $res->fetch_assoc()) { $out[] = $r['distrito']; }

$stmt->close();
header('Content-Type: application/json; charset=utf-8');
echo json_encode($out);
