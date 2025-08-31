<?php require_once __DIR__ . '/auth.php'; require_once __DIR__ . '/../includes/db.php';
$id = intval($_GET['id'] ?? 0);
// Remove images from disk
$dir = __DIR__ . '/../assets/img/properties/p' . $id;
if(is_dir($dir)){
  foreach(glob($dir.'/*') as $f){ @unlink($f); }
  @rmdir($dir);
}
// Delete DB rows (images cascade via FK if enabled)
$stmt = $conn->prepare("DELETE FROM propiedades WHERE id=?"); $stmt->bind_param('i',$id); $stmt->execute(); $stmt->close();
header('Location: dashboard.php'); exit;
