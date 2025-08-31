<?php require_once __DIR__ . '/utils.php'; 
header('Content-Type: text/html; charset=utf-8');

?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>GanaMaxii — Portal Inmobiliario</title>
  <meta name="description" content="Portal inmobiliario GanaMaxii — compra directa, subastas y alquiler de propiedades.">
  <link rel="icon" href="assets/logo.svg" type="image/svg+xml">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">


  <!-- Open Graph / Twitter -->
  <meta property="og:title" content="<?= htmlspecialchars($page_title ?? 'GanaMaxii', ENT_QUOTES) ?>">
  <meta property="og:description" content="<?= htmlspecialchars($page_desc ?? 'Compra, Alquiler y Subastas en Perú', ENT_QUOTES) ?>">
  <meta property="og:url" content="<?= htmlspecialchars($page_url ?? ((isset($_SERVER['HTTPS'])?'https':'http').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']), ENT_QUOTES) ?>">
  <meta property="og:image" content="<?= htmlspecialchars($page_image ?? ((isset($_SERVER['HTTPS'])?'https':'http').'://'.$_SERVER['HTTP_HOST'].'/assets/og-default.jpg'), ENT_QUOTES) ?>">
  <meta property="og:type" content="website">
  <meta name="twitter:card" content="summary_large_image">

  <!-- Favicons -->
  <link rel="icon" href="/assets/favicon/favicon.ico">
  <link rel="apple-touch-icon" href="/assets/favicon/apple-touch-icon.png">

  <!-- Prefetch / Preconnect -->
  <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
  <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>

  <!-- Bootstrap 5 CSS (una sola vez) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Tu CSS -->
  <link href="assets/css/style.css" rel="stylesheet">

  <!-- JSON-LD (corregido el slash de /assets) -->
  <script type="application/ld+json">
  {
    "@context":"https://schema.org",
    "@type":"RealEstateAgent",
    "name":"GanaMaxii Inmobiliaria",
    "url":"<?= htmlspecialchars((isset($_SERVER['HTTPS'])?'https':'http').'://'.$_SERVER['HTTP_HOST'].'/', ENT_QUOTES) ?>",
    "logo":"<?= htmlspecialchars((isset($_SERVER['HTTPS'])?'https':'http').'://'.$_SERVER['HTTP_HOST'].'/assets/logo.svg', ENT_QUOTES) ?>",
    "areaServed":"PE",
    "address":{"@type":"PostalAddress","addressCountry":"PE"},
    "telephone":"+51 955 736 502"
  }
  </script>

  <style>
  /* --- NAVBAR --- */
  .navbar.sticky-top { z-index: 1030; } /* que nada lo tape */
  .brand-name {
    font-weight: 700;
    font-size: 1.25rem;
    color: #16a34a; /* verde de marca */
  }
  @media (max-width: 991.98px){
    .brand-name { font-size: 1.05rem; }
  }

/* ----- HERO con imagen clara y velo celeste ----- */
.hero {
  position: relative;
  background: none;
  padding-top: 3rem;
  padding-bottom: 3rem;
  overflow: hidden;
}

.hero::before {
  content: "";
  position: absolute;
  inset: 0;
  background:
    /* celeste muy sutil */
    linear-gradient(180deg, rgba(14,165,233,.15), rgba(14,165,233,0) 60%),
    url('assets/img/hero.png') center/cover no-repeat;
  opacity: 1; /* imagen nítida */
  pointer-events: none;
}
.hero .overlay {
  background: rgba(255, 255, 255, .70);
  backdrop-filter: blur(2px);
  -webkit-backdrop-filter: blur(2px);
}



  /* ----- Cards de propiedad ----- */
  .card-property{
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    overflow: hidden;
    transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
  }
  .card-property:hover{
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(2,6,23,.08);
    border-color:#dbeafe;
  }
  .card-property img{ aspect-ratio: 16 / 9; object-fit: cover; }

  .badge-op{ text-transform: capitalize; font-weight: 600; }

  .fav-btn{
    position:absolute; top:.5rem; right:.5rem;
    border:none; border-radius:999px;
    background: rgba(255,255,255,.85);
    width:36px; height:36px;
    display:grid; place-items:center;
    box-shadow: 0 4px 14px rgba(2,6,23,.18);
  }
  .fav-btn:hover{ background:#fff; }
  .fav-btn.active i{ color:#ef4444; }

  /* Skip link accesible */
  .visually-hidden-focusable:not(:focus):not(:active){
    position:absolute!important; width:1px!important; height:1px!important; padding:0!important; margin:-1px!important; overflow:hidden!important; clip:rect(0,0,0,0)!important; white-space:nowrap!important; border:0!important;
  }
  </style>
</head>
<body>



<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top">
  <div class="container">
    <!-- Marca -->
    <a class="navbar-brand d-flex align-items-center" href="/">
      <img src="assets/logo.svg" alt="GanaMaxii" height="40" class="me-2">
      <span class="brand-name">GanaMaxii</span>
    </a>

    <!-- Botón hamburguesa (AÑADIDO) -->
    <button class="navbar-toggler" type="button"
            data-bs-toggle="collapse" data-bs-target="#mainNavbar"
            aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Menú colapsable (ID CAMBIADO a #mainNavbar) -->
    <div class="collapse navbar-collapse" id="mainNavbar">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?php echo is_active('/'); ?>" href="/" <?= is_active('/') ? 'aria-current="page"' : '' ?>>Inicio</a>
        </li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?php echo (is_active('comprar.php')||is_active('subasta.php'))?'active':''; ?>"
             href="#" id="dropdownComprar" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Comprar
          </a>
          <ul class="dropdown-menu" aria-labelledby="dropdownComprar">
            <li><a class="dropdown-item" href="comprar.php">Compra Directa</a></li>
            <li><a class="dropdown-item" href="subasta.php">Compra por Subasta Electrónica</a></li>
          </ul>
        </li>

        <li class="nav-item">
          <a class="nav-link <?php echo is_active('alquilar.php'); ?>" href="alquilar.php" <?= is_active('alquilar.php') ? 'aria-current="page"' : '' ?>>Alquilar</a>
        </li>

        <li class="nav-item">
          <a class="nav-link <?php echo is_active('contacto.php'); ?>" href="contacto.php" <?= is_active('contacto.php') ? 'aria-current="page"' : '' ?>>Contacto</a>
        </li>

        <li class="nav-item">
          <a class="nav-link <?php echo is_active('trabaja.php'); ?>" href="trabaja.php" <?= is_active('trabaja.php') ? 'aria-current="page"' : '' ?>>Trabaja con nosotros</a>
        </li>

        <li class="nav-item">
          <a class="nav-link <?php echo is_active('admin'); ?>" href="admin/login.php">
            <i class="bi bi-person-lock"></i> Admin
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- FAB WhatsApp (si te tapa algo en móvil, baja su z-index en tu CSS) -->
<a class="whatsapp-fab" 
   href="https://wa.me/51955736502?text=¡Hola%20GanaMaxii!%20Necesito%20más%20información%20sobre%20sus%20propiedades" 
   target="_blank" 
   rel="noopener" 
   aria-label="WhatsApp">
  <i class="bi bi-whatsapp"></i>
</a>


<!-- Main (para el skip link) -->
<main id="contenido" tabindex="-1">
  <!-- ...tu contenido... -->
</main>

<!-- Bootstrap JS bundle (requerido para el menú en móvil) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
