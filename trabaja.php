<?php
// ====== PROCESAR POSTULACIÓN ======
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capturar y limpiar valores
    $nombre   = strip_tags(trim($_POST["nombre"] ?? ""));
    $email    = filter_var(trim($_POST["email"] ?? ""), FILTER_SANITIZE_EMAIL);
    $celular  = strip_tags(trim($_POST["celular"] ?? ""));
    $puesto   = strip_tags(trim($_POST["puesto"] ?? ""));
    $mensaje  = strip_tags(trim($_POST["mensaje"] ?? ""));

    // Validación mínima
    if ($nombre && $email && $mensaje) {
        $to      = "contacto@ganamaxii.pe"; // tu correo en cPanel
        $subject = "Nueva postulación - GanaMaxii";

        // Cabeceras
        $headers  = "From: contacto@ganamaxii.pe\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        // Cuerpo del correo
        $body  = "Nueva postulación desde la web:\n\n";
        $body .= "Nombre: $nombre\n";
        $body .= "Correo: $email\n";
        $body .= "Celular: $celular\n";
        $body .= "Puesto de interés: $puesto\n";
        $body .= "Mensaje:\n$mensaje\n";

        // Enviar
        if (mail($to, $subject, $body, $headers)) {
            $alert = '<div class="alert alert-success">✅ Gracias, tu postulación fue enviada correctamente.</div>';
        } else {
            $alert = '<div class="alert alert-danger">❌ Ocurrió un error al enviar tu postulación. Intenta más tarde.</div>';
        }
    } else {
        $alert = '<div class="alert alert-warning">⚠️ Completa al menos Nombre, Correo y Mensaje.</div>';
    }
}
?>

<?php include __DIR__ . '/includes/header.php'; ?>
<section class="py-5">
  <div class="container">
    <h1 class="h4 mb-4 text-center">Trabaja con Nosotros</h1>

    <!-- Mensaje de éxito o error -->
    <?= $alert ?? '' ?>

    <div class="row g-4">
      <!-- Formulario -->
      <div class="col-12 col-lg-6">
        <form class="row g-3" method="post" action="">
          <div class="col-md-6">
            <label class="form-label">Nombres y Apellidos</label>
            <input type="text" name="nombre" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Correo electrónico</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">N° Celular</label>
            <input type="tel" name="celular" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Puesto de interés</label>
            <input type="text" name="puesto" class="form-control" placeholder="Ej. Asesor inmobiliario">
          </div>
          <div class="col-12">
            <label class="form-label">Mensaje</label>
            <textarea name="mensaje" class="form-control" rows="4" placeholder="Cuéntanos sobre ti y tu experiencia" required></textarea>
          </div>
          <div class="col-12 text-center">
            <button class="btn btn-primary">
              <i class="bi bi-send"></i> Enviar postulación
            </button>
          </div>
        </form>
      </div>

      <!-- Mapa -->
      <div class="col-12 col-lg-6">
        <div class="ratio ratio-4x3 rounded-4 overflow-hidden border">
          <iframe
            src="https://www.google.com/maps?q=Av.+Dos+de+Mayo+1545+San+Isidro+Lima+Peru&output=embed"
            loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
        <div class="mt-3 small text-muted">
          Av. Dos de Mayo N°1545 - 315 - San Isidro, Lima, Perú.
        </div>
      </div>
    </div>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
