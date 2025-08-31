<?php
if(session_status()===PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/db.php';

// Seed admin user if table empty
$has = $conn->query("SELECT COUNT(*) c FROM usuarios")->fetch_assoc()['c'] ?? 0;
if(!$has){
  $u = 'admin';
  $p = password_hash('Gana2025!', PASSWORD_DEFAULT);
  $stmt = $conn->prepare("INSERT INTO usuarios(usuario, password) VALUES (?,?)");
  $stmt->bind_param('ss', $u, $p);
  $stmt->execute(); $stmt->close();
}

$error = '';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $user = $_POST['usuario'] ?? '';
  $pass = $_POST['password'] ?? '';
  $stmt = $conn->prepare("SELECT id, password FROM usuarios WHERE usuario=?");
  $stmt->bind_param('s', $user);
  $stmt->execute(); $res = $stmt->get_result(); $row = $res->fetch_assoc();
  if($row && password_verify($pass, $row['password'])){
    $_SESSION['uid'] = $row['id'];
    header('Location: dashboard.php'); exit;
  } else { $error = 'Usuario o contraseña incorrectos'; }
  $stmt->close();
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login · Admin GanaMaxii</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height:100vh">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-md-5">
        <div class="card shadow-sm">
          <div class="card-body p-4">
            <h1 class="h5 mb-3 text-center">Panel Admin — GanaMaxii</h1>
            <?php if($error): ?><div class="alert alert-danger py-2"><?php echo $error; ?></div><?php endif; ?>
            <form method="post" autocomplete="off">
              <div class="mb-3">
                <label class="form-label">Usuario</label>
                <input type="text" name="usuario" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control" required>
              </div>
              <button class="btn btn-primary w-100">Ingresar</button>
            </form>
            <div class="text-center mt-3"><a href="/" class="small">Volver al sitio</a></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
