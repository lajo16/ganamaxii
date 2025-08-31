<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/utils.php';
include __DIR__ . '/includes/header.php';

// 1) validar ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
  echo '<div class="container py-5"><div class="alert alert-warning">Propiedad no encontrada.</div></div>';
  include __DIR__ . '/includes/footer.php'; exit;
}

// 2) obtener propiedad
$stmt = $conn->prepare("SELECT * FROM propiedades WHERE id=?");
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$prop = $res->fetch_assoc();
$stmt->close();

if (!$prop) {
  echo '<div class="container py-5"><div class="alert alert-warning">Propiedad no encontrada.</div></div>';
  include __DIR__ . '/includes/footer.php'; exit;
}

// 3) obtener imágenes
$stmt2 = $conn->prepare("SELECT ruta_imagen FROM imagenes_propiedades WHERE propiedad_id=? ORDER BY id");
$stmt2->bind_param('i', $id);
$stmt2->execute();
$imgsRes = $stmt2->get_result();
$imgs = [];
while($r = $imgsRes->fetch_assoc()) { $imgs[] = $r['ruta_imagen']; }
$stmt2->close();

$symbol = currency_symbol($prop['moneda']);
$carouselId = 'prop_'.$id;

// === WhatsApp (agregado) ===
$WA_NUMBER = '51955736502';
$precioFmt = $symbol . number_format($prop['precio'], 2);
$waText = "Hola, me interesa esta propiedad:\n"
        . ($prop['titulo'] ?? ('ID ' . $prop['id'])) . "\n"
        . ($prop['distrito'] . ', ' . $prop['provincia'] . ', ' . $prop['departamento']) . "\n"
        . "Precio: $precioFmt\n"
        . ('property.php?id=' . $prop['id']);
$waUrl = 'https://wa.me/' . $WA_NUMBER . '?text=' . urlencode($waText);
?>

<section class="py-4">
  <div class="container">
    <nav class="small mb-3">
      <a href="index.php" class="text-decoration-none">Inicio</a> /
      <span class="text-muted">Propiedad #<?php echo $id; ?></span>
    </nav>

    <div class="row g-4">
      <div class="col-12 col-lg-7">
        <!-- Galería -->
        <div id="<?php echo $carouselId; ?>" class="carousel slide border rounded-4 overflow-hidden">
          <div class="carousel-inner">
            <?php foreach($imgs as $i => $src): ?>
              <div class="carousel-item <?php echo $i===0?'active':''; ?>">
                <img src="<?php echo e($src); ?>" class="d-block w-100" alt="Foto propiedad">
              </div>
            <?php endforeach; ?>
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#<?php echo $carouselId; ?>" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span><span class="visually-hidden">Anterior</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#<?php echo $carouselId; ?>" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span><span class="visually-hidden">Siguiente</span>
          </button>
        </div>

        <?php if(count($imgs) > 1): ?>
        <!-- Miniaturas -->
        <div class="thumbnail-strip d-flex mt-2">
          <?php foreach($imgs as $idx => $src): ?>
            <img src="<?php echo e($src); ?>" data-thumb-target="#<?php echo $carouselId; ?>" data-index="<?php echo $idx; ?>" alt="Miniatura">
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

      <div class="col-12 col-lg-5">
        <h1 class="h4 mb-2"><?php echo e($prop['titulo']); ?></h1>
        <div class="small text-muted mb-2">
          <?php echo e($prop['distrito'] . ', ' . $prop['provincia'] . ', ' . $prop['departamento']); ?>
        </div>
        <div class="d-flex align-items-center gap-2 mb-3">
          <span class="badge text-bg-primary text-capitalize"><?php echo str_replace('_',' ', $prop['tipo_operacion']); ?></span>
          <span class="badge text-bg-light"><?php echo e($prop['tipo_inmueble']); ?></span>
        </div>
        <div class="fs-3 fw-bold mb-3"><?php echo $symbol . number_format($prop['precio'], 2); ?></div>

        <div class="mb-4">
          <h2 class="h6">Descripción</h2>
          <p class="mb-0"><?php echo nl2br(e($prop['descripcion'] ?? '')); ?></p>
        </div>

        <div class="d-grid gap-2">
          <a class="btn btn-sm btn-ganamaxii px-4"
             href="<?= $waUrl ?>" target="_blank" rel="noopener">
            Preguntar por esta propiedad
          </a>

          <a class="btn btn-outline-secondary" href="javascript:history.back()">
            <i class="bi bi-arrow-left"></i> Volver
          </a>
        </div>
      </div>
    </div>

    <!-- Relacionadas (misma operación) -->
    <hr class="my-4">
    <h3 class="h6 mb-3">Propiedades relacionadas</h3>
    <div class="row g-3 related-grid">
      <?php
        $op = $prop['tipo_operacion'];
        $stmt3 = $conn->prepare("
          SELECT p.*, (SELECT ruta_imagen FROM imagenes_propiedades i WHERE i.propiedad_id=p.id ORDER BY id LIMIT 1) portada
          FROM propiedades p
          WHERE p.tipo_operacion=? AND p.id<>?
          ORDER BY p.fecha_creacion DESC LIMIT 4
        ");
        $stmt3->bind_param('si', $op, $id);
        $stmt3->execute();
        $rel = $stmt3->get_result();
        while($r = $rel->fetch_assoc()):
      ?>
      <!-- 2 por fila en móvil, 4 en desktop -->
      <div class="col-6 col-md-3">
        <a class="text-decoration-none" href="property.php?id=<?php echo $r['id']; ?>">
          <div class="card h-100">
            <!-- CAMBIO: related-img + lazy -->
            <img src="<?php echo e($r['portada'] ?: 'assets/img/hero.jpg'); ?>"
                 class="related-img"
                 alt="Portada de <?php echo e($r['titulo']); ?>"
                 loading="lazy" decoding="async">
            <div class="card-body">
              <div class="meta"><?php echo e($r['distrito'].', '.$r['provincia']); ?></div>
              <div class="title"><?php echo e($r['titulo']); ?></div>
              <div class="price"><?php echo currency_symbol($r['moneda']) . number_format($r['precio'], 2); ?></div>
            </div>
          </div>
        </a>
      </div>
      <?php endwhile; $stmt3->close(); ?>
    </div>

  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
