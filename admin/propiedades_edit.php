<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/utils.php';

$id = intval($_GET['id'] ?? 0);

// Lista fija normalizada
$TIPOS_FIJOS = [
  'Locales Comerciales',
  'Departamentos',
  'Casas',
  'Lotes',
  'Estacionamientos'
];

$msg = '';

// Cargar propiedad
$stmt = $conn->prepare("SELECT * FROM propiedades WHERE id=?");
$stmt->bind_param('i',$id);
$stmt->execute();
$p = $stmt->get_result()->fetch_assoc();
$stmt->close();

if(!$p){ die('No existe'); }

if($_SERVER['REQUEST_METHOD']==='POST'){
  $titulo        = $_POST['titulo']        ?? '';
  $descripcion   = $_POST['descripcion']   ?? '';
  $departamento  = $_POST['departamento']  ?? '';
  $provincia     = $_POST['provincia']     ?? '';
  $distrito      = $_POST['distrito']      ?? '';
  $tipo_inmueble = $_POST['tipo_inmueble'] ?? '';
  $tipo_operacion= $_POST['tipo_operacion']?? '';
  $moneda        = $_POST['moneda']        ?? 'USD';
  $precio        = floatval($_POST['precio']??0);

  // Validar tipo_inmueble contra lista fija
  if(!in_array($tipo_inmueble, $TIPOS_FIJOS, true)){
    $msg = 'Tipo de inmueble inválido.';
  }

  if($msg===''){
    $stmt = $conn->prepare("UPDATE propiedades SET
      titulo=?, descripcion=?, departamento=?, provincia=?, distrito=?,
      tipo_inmueble=?, precio=?, moneda=?, tipo_operacion=? WHERE id=?");
    $stmt->bind_param('ssssssissi',
      $titulo,$descripcion,$departamento,$provincia,$distrito,
      $tipo_inmueble,$precio,$moneda,$tipo_operacion,$id
    );
    $stmt->execute();
    $stmt->close();

    // New images upload (append up to 5 total)
    $res   = $conn->query("SELECT COUNT(*) c FROM imagenes_propiedades WHERE propiedad_id=$id")->fetch_assoc();
    $have  = intval($res['c'] ?? 0);
    $slots = max(0, 5 - $have);

    if($slots>0 && isset($_FILES['imagenes']) && is_array($_FILES['imagenes']['name'])){
      $dir = __DIR__ . '/../assets/img/properties/p' . $id;
      if(!is_dir($dir)) mkdir($dir, 0775, true);
      $added=0;
      for($i=0; $i<count($_FILES['imagenes']['name']) && $added<$slots; $i++){
        if($_FILES['imagenes']['error'][$i]===UPLOAD_ERR_OK){
          $ext = strtolower(pathinfo($_FILES['imagenes']['name'][$i], PATHINFO_EXTENSION));
          if(!in_array($ext,['jpg','jpeg','png','webp'], true)) continue;
          $destRel = 'assets/img/properties/p' . $id . '/extra' . time() . '_' . $i . '.' . $ext;
          $destAbs = __DIR__ . '/../' . $destRel;
          if(move_uploaded_file($_FILES['imagenes']['tmp_name'][$i], $destAbs)){
            $stmt2 = $conn->prepare("INSERT INTO imagenes_propiedades(propiedad_id, ruta_imagen) VALUES (?,?)");
            $stmt2->bind_param('is', $id, $destRel);
            $stmt2->execute(); $stmt2->close();
            $added++;
          }
        }
      }
    }

    header('Location: dashboard.php'); exit;
  }

  // refrescar $p si hubo error
  $p = array_merge($p, [
    'titulo'        => $titulo,
    'descripcion'   => $descripcion,
    'departamento'  => $departamento,
    'provincia'     => $provincia,
    'distrito'      => $distrito,
    'tipo_inmueble' => $tipo_inmueble,
    'tipo_operacion'=> $tipo_operacion,
    'moneda'        => $moneda,
    'precio'        => $precio
  ]);
}

// Imágenes actuales
$imgs = [];
$res = $conn->query("SELECT id, ruta_imagen FROM imagenes_propiedades WHERE propiedad_id=$id ORDER BY id");
while($r = $res->fetch_assoc()){ $imgs[]=$r; }
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Editar propiedad</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <a href="dashboard.php" class="btn btn-link">&larr; Volver</a>
  <h1 class="h5 mb-3">Editar propiedad #<?php echo $id; ?></h1>

  <?php if(!empty($msg)): ?>
    <div class="alert alert-danger py-2"><?php echo e($msg); ?></div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data" class="row g-3">
    <div class="col-12">
      <label class="form-label">Título</label>
      <input name="titulo" class="form-control" required value="<?php echo e($p['titulo']); ?>">
    </div>

    <div class="col-md-4">
      <label class="form-label">Departamento</label>
      <input name="departamento" class="form-control" required value="<?php echo e($p['departamento']); ?>">
    </div>
    <div class="col-md-4">
      <label class="form-label">Provincia</label>
      <input name="provincia" class="form-control" required value="<?php echo e($p['provincia']); ?>">
    </div>
    <div class="col-md-4">
      <label class="form-label">Distrito</label>
      <input name="distrito" class="form-control" required value="<?php echo e($p['distrito']); ?>">
    </div>

    <!-- CAMBIO: input libre -> select con lista fija -->
    <div class="col-md-4">
      <label class="form-label">Tipo de inmueble</label>
      <select name="tipo_inmueble" class="form-select" required>
        <option value="">Seleccione…</option>
        <?php foreach($TIPOS_FIJOS as $t): ?>
          <option value="<?php echo e($t); ?>" <?php echo ($p['tipo_inmueble'] === $t) ? 'selected' : ''; ?>>
            <?php echo e($t); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-4">
      <label class="form-label">Operación</label>
      <select name="tipo_operacion" class="form-select" required>
        <option value="compra_directa" <?php echo $p['tipo_operacion']==='compra_directa'?'selected':''; ?>>Compra directa</option>
        <option value="subasta"        <?php echo $p['tipo_operacion']==='subasta'?'selected':''; ?>>Compra por Subasta Electronica</option>
        <option value="alquiler"       <?php echo $p['tipo_operacion']==='alquiler'?'selected':''; ?>>Alquilar</option>
      </select>
    </div>

    <div class="col-md-2">
      <label class="form-label">Moneda</label>
      <select name="moneda" class="form-select">
        <option value="USD" <?php echo $p['moneda']==='USD'?'selected':''; ?>>USD</option>
        <option value="PEN" <?php echo $p['moneda']==='PEN'?'selected':''; ?>>PEN</option>
      </select>
    </div>
    <div class="col-md-2">
      <label class="form-label">Precio</label>
      <input type="number" step="0.01" name="precio" class="form-control" required value="<?php echo e($p['precio']); ?>">
    </div>

    <div class="col-12">
      <label class="form-label">Descripción</label>
      <textarea name="descripcion" rows="4" class="form-control"><?php echo e($p['descripcion']); ?></textarea>
    </div>

    <div class="col-12">
      <label class="form-label">Agregar imágenes (hasta completar 5)</label>
      <input type="file" name="imagenes[]" class="form-control" multiple accept=".jpg,.jpeg,.png,.webp">
    </div>

    <div class="col-12">
      <button class="btn btn-primary">Guardar cambios</button>
    </div>
  </form>

  <hr>
  <h2 class="h6">Imágenes actuales</h2>
  <div class="d-flex flex-wrap gap-2">
    <?php foreach($imgs as $im): ?>
      <div class="border rounded p-2 text-center">
        <img src="../<?php echo e($im['ruta_imagen']); ?>" style="width:160px;height:110px;object-fit:cover" alt="">
        <br>
        <a class="btn btn-sm btn-outline-danger mt-2"
           href="propiedad_img_delete.php?id=<?php echo $im['id']; ?>&pid=<?php echo $id; ?>"
           onclick="return confirm('¿Eliminar imagen?');">
           Eliminar
        </a>
      </div>
    <?php endforeach; ?>
  </div>
</div>
</body>
</html>
