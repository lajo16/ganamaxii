<?php 
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/utils.php'; // <-- añade esta línea
 ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard · GanaMaxii</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar bg-light border-bottom">
  <div class="container">
    <span class="navbar-brand">Admin · GanaMaxii</span>
    <div class="d-flex gap-2">
      <a href="propiedades_add.php" class="btn btn-primary btn-sm"><i class="bi bi-plus"></i> Nueva propiedad</a>
      <a href="logout.php" class="btn btn-outline-secondary btn-sm">Salir</a>
    </div>
  </div>
</nav>
<main class="container py-4">
  <h1 class="h5 mb-3">Propiedades</h1>
  <div class="table-responsive">
    <table class="table table-sm align-middle">
      <thead><tr>
        <th>#</th><th>Título</th><th>Ubicación</th><th>Tipo</th><th>Operación</th><th>Moneda</th><th>Precio</th><th>Acciones</th>
      </tr></thead>
      <tbody>
        <?php
          $res = $conn->query("SELECT * FROM propiedades ORDER BY id DESC");
          while($p = $res->fetch_assoc()):
        ?>
        <tr>
          <td><?php echo $p['id']; ?></td>
          <td><?php echo e($p['titulo']); ?></td>
          <td class="small text-muted"><?php echo e($p['distrito'] . ', ' . $p['provincia'] . ', ' . $p['departamento']); ?></td>
          <td><?php echo e($p['tipo_inmueble']); ?></td>
          <td><?php echo e($p['tipo_operacion']); ?></td>
          <td><?php echo e($p['moneda']); ?></td>
          <td><?php echo number_format($p['precio'],2); ?></td>
          <td>
            <a class="btn btn-outline-primary btn-sm" href="propiedades_edit.php?id=<?php echo $p['id']; ?>">Editar</a>
            <a class="btn btn-outline-danger btn-sm" href="propiedades_delete.php?id=<?php echo $p['id']; ?>" onclick="return confirm('¿Eliminar propiedad?');">Eliminar</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</main>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</body>
</html>
