<?php include __DIR__ . '/includes/header.php'; ?>
<?php include __DIR__ . '/includes/advantages.php'; ?>

<section class="hero py-5">
  <div class="container">
    <div class="p-4 p-md-5 rounded-4 overlay">
      <h1 class="display-6 fw-semibold mb-1">Encuentra tu propiedad ideal en todo el Perú</h1>
      <div class="small text-secondary">
        🛡️ Compra, subasta y alquiler con respaldo legal.
      </div>

      <div class="filter-card p-3 p-md-4">
        <?php include __DIR__ . '/includes/filters.php'; ?>
      </div>
    </div>
  </div>
</section>

<!-- BLOQUE: Respaldo Legal -->
<section class="py-4">
  <div class="container">
    <div class="p-3 p-md-4 rounded-3 border bg-white d-flex gap-3 align-items-start shadow-sm">
      <div class="fs-2 lh-1" aria-hidden="true">🛡️</div>
      <div>
        <h2 class="h5 mb-1">Compra y alquiler con respaldo legal</h2>
        <p class="mb-2 text-secondary">
          Seguridad garantizada gracias a nuestra alianza con asesores legales.
        </p>
        <!-- Mini lista opcional -->
        <ul class="list-unstyled mb-0 small text-secondary">
          <li>• Revisión de documentos y cargas registrales</li>
          <li>• Contratos personalizados y verificados</li>
          <li>• Acompañamiento durante todo el proceso</li>
        </ul>
      </div>
    </div>
  </div>
</section>

<section class="py-5">
  <div class="container">
    <?php
      // Construir URL hacia propiedades.php preservando filtros actuales
      $qs = $_GET ?? [];
      unset($qs['page']);
      $propsUrl = 'propiedades.php' . (!empty($qs) ? ('?' . http_build_query($qs)) : '');
    ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2 class="h4 mb-0">Propiedades destacadas</h2>
      <a href="<?php echo e($propsUrl); ?>" class="btn btn-outline-primary btn-sm">Ver propiedades</a>
    </div>

    <div class="row g-4">
      <?php
        require_once __DIR__ . '/includes/list_query.php';
        $props = fetch_props($conn, 6);

        // Número de WhatsApp (con código de país, sin +)
        $WA_NUMBER = '51955736502';

        foreach ($props as $p):
          $symbol     = currency_symbol($p['moneda']);
          $precioFmt  = $symbol . number_format($p['precio'], 2);
          $detalleUrl = 'property.php?id=' . $p['id'];

          // Texto de WhatsApp
          $waText = "Hola, quiero preguntar por esta propiedad:\n" .
                    ($p['titulo'] ?? ('ID ' . $p['id'])) . "\n" .
                    ($p['distrito'] . ', ' . $p['provincia'] . ', ' . $p['departamento']) . "\n" .
                    "Precio: $precioFmt\n" .
                    $detalleUrl;

          $waUrl = 'https://wa.me/' . $WA_NUMBER . '?text=' . urlencode($waText);

          $imgs = fetch_images($conn, $p['id']);
          $carouselId = 'c' . $p['id'];
      ?>
      <div class="col-12 col-md-6 col-lg-4">
        <div class="card card-property h-100">
          <!-- Carrusel: controles libres, no abre detalle -->
          <div id="<?php echo $carouselId; ?>" class="carousel slide" data-bs-ride="false">
            <div class="carousel-inner">
              <?php if ($imgs): ?>
                <?php foreach ($imgs as $idx => $src): ?>
                  <div class="carousel-item <?php echo $idx===0 ? 'active' : ''; ?>">
                    <!-- CAMBIO: card-cover + lazy -->
                    <img src="<?php echo e($src); ?>"
                         class="d-block w-100 card-cover"
                         alt="Foto de <?php echo e($p['titulo']); ?>"
                         loading="lazy" decoding="async">
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="carousel-item active">
                  <!-- CAMBIO: card-cover + lazy -->
                  <img src="assets/img/hero.jpg"
                       class="d-block w-100 card-cover"
                       alt="Foto no disponible"
                       loading="lazy" decoding="async">
                </div>
              <?php endif; ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#<?php echo $carouselId; ?>" data-bs-slide="prev">
              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#<?php echo $carouselId; ?>" data-bs-slide="next">
              <span class="carousel-control-next-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Siguiente</span>
            </button>
          </div>

          <!-- Contenido -->
          <div class="p-3">
            <div class="d-flex justify-content-between align-items-start">
              <h3 class="h6 mb-2"><?php echo e($p['titulo']); ?></h3>
              <span class="badge text-bg-primary">
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
    </div>
  </div>
</section>
<?php include __DIR__ . '/includes/ig-feed.php'; ?>

<?php include __DIR__ . '/includes/cta-final.php'; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>
