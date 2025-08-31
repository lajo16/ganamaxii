<?php require_once __DIR__ . '/auth.php'; require_once __DIR__ . '/../includes/db.php';
$id = intval($_GET['id'] ?? 0);
$pid = intval($_GET['pid'] ?? 0);
$stmt = $conn->prepare("SELECT ruta_imagen FROM imagenes_propiedades WHERE id=?"); $stmt->bind_param('i',$id); $stmt->execute();
$r = $stmt->get_result()->fetch_assoc(); $stmt->close();
if($r){
  $path = __DIR__ . '/../' . $r['ruta_imagen'];
  if(is_file($path)) @unlink($path);
  $del = $conn->prepare("DELETE FROM imagenes_propiedades WHERE id=?"); $del->bind_param('i',$id); $del->execute(); $del->close();
}
header('Location: propiedades_edit.php?id=' . $pid);
exit;
