<footer class="site-footer pt-5" style="background-color:#1f2a44; color:#f8f9fa;">
  <style>
    .site-footer{color:#f8f9fa}
    .site-footer .footer-heading{font-family:'Poppins',sans-serif;color:#fff}
    .site-footer .footer-slogan{font-family:'Poppins',sans-serif;color:#d1d5db;line-height:1.6;font-size:.875rem}
    .site-footer .footer-links a,
    .site-footer .footer-contact a{color:#d1d5db;text-decoration:none;transition:color .25s ease}
    .site-footer .footer-links a:hover,
    .site-footer .footer-contact a:hover{color:#fff}
    .site-footer .footer-social a{
      color:#d1d5db;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      width:36px;height:36px;
      border-radius:50%;
      transition:all .25s ease;
    }
    .site-footer .footer-social a:hover{transform:scale(1.15);background:rgba(255,255,255,.06)}
    .site-footer .footer-social a:hover .bi-instagram{color:#e1306c}
    .site-footer .footer-social a:hover .bi-facebook{color:#1877f2}
    .site-footer .footer-logo{max-height:40px;width:auto}
    .site-footer .footer-bottom{
      font-family:'Poppins',sans-serif;
      font-size:.85rem;
      color:#9ca3af;
      border-top:1px solid rgba(255,255,255,.12);
    }
    .site-footer .footer-bottom .brand-link{
      color:#22c55e; /* verde corporativo */
      font-weight:600;
      text-decoration:none;
      transition:color .3s ease;
    }
    .site-footer .footer-bottom .brand-link:hover{color:#ffffff}
    .site-footer .map-frame{border-radius:12px;overflow:hidden}
    @media(max-width:576px){
      .site-footer .footer-logo{max-height:32px}
      .site-footer .footer-slogan{font-size:.85rem}
    }
  </style>

  <div class="container">
    <div class="row text-center text-md-start">
      
      <!-- Columna 1 -->
      <div class="col-12 col-md-4 mb-4 d-flex flex-column align-items-center align-items-md-start">
        <a href="index.php" class="d-flex align-items-center mb-3 text-decoration-none">
          <img src="assets/logo.svg" alt="GanaMaxii" class="footer-logo me-2">
        </a>
        <p class="mb-3 small footer-slogan">
          Obtén la ganancia máxima en inversión inmobiliaria con la confianza, respaldo y experiencia que tu patrimonio merece.
        </p>

        <!-- Redes sociales -->
        <h5 class="fw-bold mb-3 footer-heading">Síguenos</h5>
        <ul class="list-unstyled small d-flex gap-3 footer-social">
          <li>
            <a href="https://www.instagram.com/ocginmobiliaria.ii/" target="_blank" rel="noopener" aria-label="Instagram" title="Instagram">
              <i class="bi bi-instagram fs-4"></i>
            </a>
          </li>
          <li>
            <a href="https://www.facebook.com/profile.php?id=61572491323207" target="_blank" rel="noopener" aria-label="Facebook" title="Facebook">
              <i class="bi bi-facebook fs-4"></i>
            </a>
          </li>
        </ul>
      </div>

      <!-- Columna 2 -->
      <div class="col-12 col-md-4 mb-4">
        <h5 class="fw-bold mb-3 footer-heading">Enlaces</h5>
        <ul class="list-unstyled small footer-links">
          <li><a href="index.php" class="d-block py-1">Inicio</a></li>
          <li><a href="comprar.php" class="d-block py-1">Compra directa</a></li>
          <li><a href="subasta.php" class="d-block py-1">Compra por subasta electrónica</a></li>
          <li><a href="alquilar.php" class="d-block py-1">Alquilar</a></li>
          <li><a href="propiedades.php" class="d-block py-1">Propiedades</a></li>
          <li><a href="contacto.php" class="d-block py-1">Contáctanos</a></li>
          <li><a href="admin/login.php" class="d-block py-1">Admin</a></li>
        </ul>
      </div>

      <!-- Columna 3 -->
      <div class="col-12 col-md-4 mb-4">
        <h5 class="fw-bold mb-3 footer-heading">Contáctanos</h5>
        <ul class="list-unstyled small mb-3 footer-contact">
          <li class="mb-2"><i class="bi bi-geo-alt-fill text-primary"></i> Av. Dos de Mayo N°1545 - 315 - San Isidro, Lima, Perú</li>
          <li class="mb-2"><i class="bi bi-telephone-fill text-warning"></i> Central: 01 - 3048039</li>
          <li class="mb-2"><i class="bi bi-whatsapp text-success"></i> <a href="https://wa.me/51955736502" target="_blank" rel="noopener">+51 955 736 502</a></li>
          <li class="mb-2"><i class="bi bi-envelope-fill text-primary"></i> <a href="mailto:informes@ganamaxii.pe">informes@ganamaxii.pe</a></li>
        </ul>

        <!-- Mapa responsive con esquinas redondeadas -->
        <div class="ratio ratio-16x9 border map-frame">
          <iframe src="https://www.google.com/maps?q=Av.+Dos+de+Mayo+1545+San+Isidro+Lima+Peru&output=embed" allowfullscreen loading="lazy"></iframe>
        </div>
      </div>
    </div>

    <!-- Texto inferior -->
    <div class="footer-bottom text-center pt-3 pb-3">
      © <?php echo date("Y"); ?> 
      <a href="index.php" class="brand-link">GanaMaxii</a> · Todos los derechos reservados
    </div>
  </div>
</footer>
