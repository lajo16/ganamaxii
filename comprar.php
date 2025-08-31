<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/utils.php';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/advantages.php';


// Forzar operación "compra_directa" si no viene en la URL
if (!isset($_GET['tipo_operacion']) || $_GET['tipo_operacion'] === '') {
  $_GET['tipo_operacion'] = 'compra_directa';
}
?>
<section class="hero py-5">
  <div class="container">
    <div class="p-4 p-md-5 rounded-4 overlay">
      <h1 class="display-6 fw-semibold mb-1">Compra tu propiedad ideal en todo el Perú</h1>
      <div class="small text-secondary">
        🛡️ Compra con respaldo legal.
      </div>
      <div class="filter-card p-3 p-md-4">
        <?php include __DIR__ . '/includes/filters.php'; ?>
      </div>
    </div>
  </div>
</section>

<section class="py-4">
  <div class="container">

    <div class="row g-4">
      <?php
        require_once __DIR__ . '/includes/list_query.php';

        // --- Paginación --- //
        $perPage = 12; // cantidad por página
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

        // Total según los filtros actuales (incluye tipo_operacion = compra_directa)
        $total = count_props($conn);
        $totalPages = max(1, (int)ceil($total / $perPage));
        if ($page > $totalPages) $page = $totalPages;

        $offset = ($page - 1) * $perPage;

        // Trae usando limit + offset
        $props = fetch_props($conn, $perPage, $offset);

        if(!$props){
          echo '<div class="text-muted">No se encontraron propiedades con los filtros seleccionados.</div>';
        }

        // Número de WhatsApp (con código de país, sin +)
        $WA_NUMBER = '51955736502';

        foreach($props as $p):
          $symbol     = currency_symbol($p['moneda']);
          $imgs       = fetch_images($conn, $p['id']);
          $carouselId = 'c' . $p['id'];

          // Precio formateado y URL a la ficha
          $precioFmt  = $symbol . number_format($p['precio'], 2);
          $detalleUrl = 'property.php?id=' . $p['id'];

          // Texto y url para WhatsApp
          $waText = "Hola, quiero preguntar por esta propiedad:\n" .
                    ($p['titulo'] ?? ('ID ' . $p['id'])) . "\n" .
                    ($p['distrito'] . ', ' . $p['provincia'] . ', ' . $p['departamento']) . "\n" .
                    "Precio: $precioFmt\n" .
                    $detalleUrl;

          $waUrl  = 'https://wa.me/' . $WA_NUMBER . '?text=' . urlencode($waText);
      ?>
      <div class="col-12 col-md-6 col-lg-4">
        <div class="card card-property h-100">
          <div id="<?php echo $carouselId; ?>" class="carousel slide" data-bs-ride="false">
            <div class="carousel-inner">
              <?php if ($imgs): foreach($imgs as $idx=>$src): ?>
                <div class="carousel-item <?php echo $idx===0?'active':''; ?>">
                  <img src="<?php echo e($src); ?>" class="d-block w-100" alt="Foto propiedad">
                </div>
              <?php endforeach; else: ?>
                <div class="carousel-item active">
                  <img src="assets/img/hero.jpg" class="d-block w-100" alt="Foto no disponible">
                </div>
              <?php endif; ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#<?php echo $carouselId; ?>" data-bs-slide="prev">
              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#<?php echo $carouselId; ?>" data-bs-slide="next">
              <span class="carousel-control-next-icon" aria-hidden="true"></span>
              <span class="visualmente-hidden">Siguiente</span>
            </button>
          </div>

          <div class="p-3">
            <div class="d-flex justify-content-between align-items-start">
              <h3 class="h6 mb-2"><?php echo e($p['titulo']); ?></h3>
              <span class="badge text-bg-primary badge-op">
                <?php echo e(str_replace('_',' ', $p['tipo_operacion'])); ?>
              </span>
            </div>

            <div class="small text-muted mb-2">
              <?php echo e($p['distrito'] . ', ' . $p['provincia'] . ', ' . $p['departamento']); ?>
            </div>

            <div class="fw-bold fs-5 mb-2">
              <?php echo $precioFmt; ?>
            </div>

            <p class="small text-secondary mb-3">
              <?php echo e(mb_strimwidth($p['descripcion'], 0, 120, '…', 'UTF-8')); ?>
            </p>

            <!-- Botones centrados -->
            <div class="d-flex flex-column gap-2 align-items-center">
              <a class="btn btn-sm btn-outline-primary px-4"
                 href="property.php?id=<?php echo $p['id']; ?>">
                Ver detalle
              </a>
<a class="btn btn-sm btn-ganamaxii px-4"
   href="<?= $waUrl ?>" target="_blank" rel="noopener">
  Preguntar por esta propiedad
</a>

            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>

      <?php
        // --- NAV de paginación ---
        // Construir base URL preservando filtros actuales (sin 'page')
        $qs = $_GET ?? [];
        unset($qs['page']);
        $self = basename($_SERVER['PHP_SELF']); // comprar.php
        $base = $self . (!empty($qs) ? ('?' . http_build_query($qs) . '&') : '?');

        // Rango de páginas alrededor de la actual
        $range = 2;
        $start = max(1, $page - $range);
        $end   = min($totalPages, $page + $range);
      ?>
      <nav aria-label="Paginación" class="mt-4">
        <ul class="pagination justify-content-center">
          <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
            <a class="page-link" href="<?php echo $base . 'page=' . ($page - 1); ?>" tabindex="-1">Anterior</a>
          </li>

          <?php
            if ($start > 1) {
              echo '<li class="page-item"><a class="page-link" href="'.$base.'page=1">1</a></li>';
              if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
            }

            for ($i = $start; $i <= $end; $i++) {
              $active = $i == $page ? ' active' : '';
              echo '<li class="page-item'.$active.'"><a class="page-link" href="'.$base.'page='.$i.'">'.$i.'</a></li>';
            }

            if ($end < $totalPages) {
              if ($end < $totalPages - 1) echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
              echo '<li class="page-item"><a class="page-link" href="'.$base.'page='.$totalPages.'">'.$totalPages.'</a></li>';
            }
          ?>

          <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
            <a class="page-link" href="<?php echo $base . 'page=' . ($page + 1); ?>">Siguiente</a>
          </li>
        </ul>
        <p class="text-center text-muted small mb-0">
          Mostrando <?php echo count($props); ?> de <?php echo $total; ?> resultados · Página <?php echo $page; ?> de <?php echo $totalPages; ?>
        </p>
      </nav>

    </div><!-- /.row -->
  </div><!-- /.container -->
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
