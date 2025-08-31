<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/utils.php';

$msg = '';

// Lista fija de tipos de inmueble (normalizados)
$TIPOS_FIJOS = [
  'Locales Comerciales',
  'Departamentos',
  'Casas',
  'Lotes',
  'Estacionamientos'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $titulo        = $_POST['titulo']        ?? '';
  $descripcion   = $_POST['descripcion']   ?? '';
  $departamento  = $_POST['departamento']  ?? '';
  $provincia     = $_POST['provincia']     ?? '';
  $distrito      = $_POST['distrito']      ?? '';
  $tipo_inmueble = $_POST['tipo_inmueble'] ?? '';
  $tipo_operacion= $_POST['tipo_operacion']?? '';
  $moneda        = $_POST['moneda']        ?? 'USD';
  $precio        = floatval($_POST['precio'] ?? 0);

  // Validar tipo_inmueble contra la lista fija
  if (!in_array($tipo_inmueble, $TIPOS_FIJOS, true)) {
    $msg = 'Tipo de inmueble inválido.';
  }

  if ($msg === '') {
    $stmt = $conn->prepare("INSERT INTO propiedades
      (titulo, descripcion, departamento, provincia, distrito, tipo_inmueble, precio, moneda, tipo_operacion)
      VALUES (?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param('ssssssiss',
      $titulo, $descripcion, $departamento, $provincia, $distrito, $tipo_inmueble, $precio, $moneda, $tipo_operacion
    );
    $stmt->execute();
    $pid = $stmt->insert_id;
    $stmt->close();

    // Upload images (max 5)
    $dir = __DIR__ . '/../assets/img/properties/p' . $pid;
    if (!is_dir($dir)) mkdir($dir, 0775, true);
    $count = 0;

    if (isset($_FILES['imagenes']) && is_array($_FILES['imagenes']['name'])) {
      for ($i = 0; $i < count($_FILES['imagenes']['name']) && $count < 5; $i++) {
        if ($_FILES['imagenes']['error'][$i] === UPLOAD_ERR_OK) {
          $ext = strtolower(pathinfo($_FILES['imagenes']['name'][$i], PATHINFO_EXTENSION));
          if (!in_array($ext, ['jpg','jpeg','png','webp'], true)) continue;

          $destRel = 'assets/img/properties/p' . $pid . '/img' . ($count + 1) . '.' . $ext;
          $destAbs = __DIR__ . '/../' . $destRel;
          move_uploaded_file($_FILES['imagenes']['tmp_name'][$i], $destAbs);

          $stmt2 = $conn->prepare("INSERT INTO imagenes_propiedades(propiedad_id, ruta_imagen) VALUES (?,?)");
          $stmt2->bind_param('is', $pid, $destRel);
          $stmt2->execute();
          $stmt2->close();

          $count++;
        }
      }
    }

    header('Location: dashboard.php');
    exit;
  }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Nueva propiedad</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <a href="dashboard.php" class="btn btn-link">&larr; Volver</a>
  <h1 class="h5 mb-3">Agregar propiedad</h1>

  <?php if($msg): ?>
    <div class="alert alert-danger py-2"><?php echo e($msg); ?></div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data" class="row g-3">
    <div class="col-12">
      <label class="form-label">Título</label>
      <input name="titulo" class="form-control" required>
    </div>

    <div class="col-md-4">
      <label class="form-label">Departamento</label>
      <input name="departamento" class="form-control" required placeholder="Ej. Lima">
    </div>
    <div class="col-md-4">
      <label class="form-label">Provincia</label>
      <input name="provincia" class="form-control" required placeholder="Ej. Lima">
    </div>
    <div class="col-md-4">
      <label class="form-label">Distrito</label>
      <input name="distrito" class="form-control" required placeholder="Ej. San Isidro">
    </div>

    <!-- CAMBIO: input libre -> select con lista fija -->
    <div class="col-md-4">
      <label class="form-label">Tipo de inmueble</label>
      <select name="tipo_inmueble" class="form-select" required>
        <option value="">Seleccione…</option>
        <?php foreach ($TIPOS_FIJOS as $t): ?>
          <option value="<?php echo e($t); ?>"><?php echo e($t); ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-4">
      <label class="form-label">Operación</label>
      <select name="tipo_operacion" class="form-select" required>
        <option value="compra_directa">Compra Directa</option>
        <option value="subasta">Compra por Subasta Electronica</option>
        <option value="alquiler">Alquilar</option>
      </select>
    </div>

    <div class="col-md-2">
      <label class="form-label">Moneda</label>
      <select name="moneda" class="form-select">
        <option value="USD">USD</option>
        <option value="PEN">PEN</option>
      </select>
    </div>
    <div class="col-md-2">
      <label class="form-label">Precio</label>
      <input type="number" step="0.01" name="precio" class="form-control" required>
    </div>

    <div class="col-12">
      <label class="form-label">Descripción</label>
      <textarea name="descripcion" rows="4" class="form-control"></textarea>
    </div>

    <div class="col-12">
      <label class="form-label">Imágenes (hasta 5)</label>
      <input type="file" name="imagenes[]" class="form-control" multiple accept=".jpg,.jpeg,.png,.webp">
      <div class="form-text">Formatos: JPG, PNG o WEBP. Máx 5 imágenes.</div>
    </div>

    <div class="col-12">
      <button class="btn btn-primary">Guardar</button>
    </div>
  </form>
</div>
</body>
</html>
