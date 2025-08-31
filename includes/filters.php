<?php
// includes/filters.php
require_once __DIR__ . '/db.php';

/* ------------------ Config fija ------------------ */
$monedas = ['USD','PEN'];
$tipos   = ['Locales Comerciales','Departamentos','Casas','Lotes','Estacionamientos'];

/* ------------------ Valores GET ------------------ */
$f_departamento = $_GET['departamento']   ?? '';
$f_provincia    = $_GET['provincia']      ?? '';
$f_distrito     = $_GET['distrito']       ?? '';
$f_tipo         = $_GET['tipo_inmueble']  ?? '';
$f_moneda       = $_GET['moneda']         ?? '';
$f_operacion    = $_GET['tipo_operacion'] ?? ''; // compra_directa | alquiler | subasta
if ($f_operacion === '') $f_operacion = 'compra_directa';

/* Mapa para el dropdown de operación */
$opMap = [
  'compra_directa' => ['label'=>'Compra Directa',                 'page'=>'comprar.php'],
  'alquiler'       => ['label'=>'Alquilar',                       'page'=>'alquilar.php'],
  'subasta'        => ['label'=>'Compra por Subasta Electrónica','page'=>'subasta.php'],
];
$opLabel = $opMap[$f_operacion]['label'] ?? 'Seleccionar operación';
$opPage  = $opMap[$f_operacion]['page']  ?? 'comprar.php';

/* ------------------ Datos SSR dependientes de operación ------------------ */
/* Departamentos por operación */
$deps = [];
$stmt = $conn->prepare("
  SELECT DISTINCT departamento
    FROM propiedades
   WHERE departamento <> '' AND tipo_operacion = ?
   ORDER BY departamento
");
$stmt->bind_param('s', $f_operacion);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) $deps[] = $row['departamento'];
$stmt->close();

/* Provincias por operación + departamento seleccionado */
$provs = [];
if ($f_departamento !== '') {
  $stmt = $conn->prepare("
    SELECT DISTINCT provincia
      FROM propiedades
     WHERE provincia <> ''
       AND tipo_operacion = ?
       AND departamento = ?
     ORDER BY provincia
  ");
  $stmt->bind_param('ss', $f_operacion, $f_departamento);
  $stmt->execute();
  $res = $stmt->get_result();
  while ($row = $res->fetch_assoc()) $provs[] = $row['provincia'];
  $stmt->close();
}

/* Distritos por operación + dep + prov seleccionados */
$dists = [];
if ($f_departamento !== '' && $f_provincia !== '') {
  $stmt = $conn->prepare("
    SELECT DISTINCT distrito
      FROM propiedades
     WHERE distrito <> ''
       AND tipo_operacion = ?
       AND departamento = ?
       AND provincia = ?
     ORDER BY distrito
  ");
  $stmt->bind_param('sss', $f_operacion, $f_departamento, $f_provincia);
  $stmt->execute();
  $res = $stmt->get_result();
  while ($row = $res->fetch_assoc()) $dists[] = $row['distrito'];
  $stmt->close();
}
?>

<!-- Estilo mínimo del botón de Operación (celeste) -->
<style>
/* Ajusta tamaño de fuente y padding para que no se desborde */
.btn-op {
  font-size: 0.9rem;      /* más pequeño */
  padding: .45rem .65rem; /* menos alto y ancho */
  white-space: normal;    /* permite salto de línea */
  line-height: 1.2;       /* mejor lectura */
}

/* Forzar que el botón no se haga más alto que los selects */
#op-dropdown .btn-op {
  min-height: calc(2.35rem + 2px); /* misma altura aprox. que un select de Bootstrap */
}

</style>

<form class="row g-2 align-items-end" method="get" action="">
  <!-- Operación -->
  <div class="col-12 col-md-3">
    <label class="form-label d-block">Operación</label>

    <!-- hidden que viaja en el GET -->
    <input type="hidden" name="tipo_operacion" id="op-value" value="<?php echo htmlspecialchars($f_operacion,ENT_QUOTES); ?>">
    <!-- action dinámica que se ajusta al enviar -->
    <input type="hidden" id="op-action" value="<?php echo htmlspecialchars($opPage,ENT_QUOTES); ?>">

    <div class="dropdown w-100" id="op-dropdown">
      <button class="btn btn-op w-100 d-flex justify-content-between align-items-center"
              type="button" id="opBtn" data-bs-toggle="dropdown" aria-expanded="false">
        <span id="opLabel"><?php echo htmlspecialchars($opLabel,ENT_QUOTES); ?></span>
        <i class="bi bi-caret-down-fill ms-2"></i>
      </button>

      <ul class="dropdown-menu w-100 shadow-sm" aria-labelledby="opBtn">
        <li>
          <a class="dropdown-item op-item <?php echo $f_operacion==='compra_directa'?'active':''; ?>"
            href="#" data-value="compra_directa" data-page="comprar.php">🏠 Compra Directa</a>
        </li>
        <li>
          <a class="dropdown-item op-item <?php echo $f_operacion==='alquiler'?'active':''; ?>"
            href="#" data-value="alquiler" data-page="alquilar.php">🔑Alquilar</a>
        </li>
        <li>
          <a class="dropdown-item op-item <?php echo $f_operacion==='subasta'?'active':''; ?>"
            href="#" data-value="subasta" data-page="subasta.php">🔨Compra por Subasta Electrónica</a>
        </li>
      </ul>
    </div>
  </div>

  <!-- Departamento -->
  <div class="col-12 col-md-3">
    <label class="form-label">Departamento</label>
    <select id="f_departamento" name="departamento" class="form-select">
      <option value="">Todos</option>
      <?php foreach($deps as $d): ?>
        <option value="<?php echo htmlspecialchars($d,ENT_QUOTES); ?>"
          <?php echo $f_departamento===$d?'selected':''; ?>>
          <?php echo htmlspecialchars($d); ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <!-- Provincia -->
  <div class="col-12 col-md-3">
    <label class="form-label">Provincia</label>
    <select id="f_provincia" name="provincia" class="form-select" <?php echo empty($provs)?'disabled':''; ?>>
      <option value="">Provincia</option>
      <?php foreach($provs as $p): ?>
        <option value="<?php echo htmlspecialchars($p,ENT_QUOTES); ?>"
          <?php echo $f_provincia===$p?'selected':''; ?>>
          <?php echo htmlspecialchars($p); ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <!-- Distrito -->
  <div class="col-12 col-md-3">
    <label class="form-label">Distrito</label>
    <select id="f_distrito" name="distrito" class="form-select" <?php echo empty($dists)?'disabled':''; ?>>
      <option value="">Distrito</option>
      <?php foreach($dists as $d): ?>
        <option value="<?php echo htmlspecialchars($d,ENT_QUOTES); ?>"
          <?php echo $f_distrito===$d?'selected':''; ?>>
          <?php echo htmlspecialchars($d); ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <!-- Tipo de inmueble -->
  <div class="col-12 col-md-3">
    <label class="form-label">Tipo de inmueble</label>
    <select id="f_tipo" name="tipo_inmueble" class="form-select">
      <option value="">Todos</option>
      <?php foreach($tipos as $t): ?>
        <option value="<?php echo htmlspecialchars($t,ENT_QUOTES); ?>"
          <?php echo $f_tipo===$t?'selected':''; ?>>
          <?php echo htmlspecialchars($t); ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <!-- Moneda -->
  <div class="col-6 col-md-2">
    <label class="form-label">Moneda</label>
    <select name="moneda" class="form-select">
      <option value="">Todas</option>
      <?php foreach($monedas as $m): ?>
        <option value="<?php echo htmlspecialchars($m,ENT_QUOTES); ?>"
          <?php echo $f_moneda===$m?'selected':''; ?>>
          <?php echo htmlspecialchars($m); ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

<!-- Buscar -->
<div class="col-6 col-md-2">
  <label class="form-label d-block">&nbsp;</label>
  <button class="btn btn-success btn-lg w-100 shadow-sm" 
          style="background:#007A33; border-color:#006629; color:#fff;">
    <i class="bi bi-search me-1"></i> Buscar
  </button>
</div>


<!-- Borrar -->
<div class="col-6 col-md-2">
  <label class="form-label d-block">&nbsp;</label>
  <button class="btn btn-outline-secondary btn-lg w-100 shadow-sm" type="button" id="btn-clear">
    <i class="bi bi-x-circle me-1"></i> Borrar
  </button>
</div>

</form>

<script>
// Helpers
function setOptions(selectEl, items, placeholder){
  if(!selectEl) return;
  let html = `<option value="">${placeholder}</option>`;
  items.forEach(v => { html += `<option value="${v}">${v}</option>`; });
  selectEl.innerHTML = html;
  selectEl.disabled = items.length === 0; // habilita/deshabilita
}

document.addEventListener('DOMContentLoaded', function(){
  const ddWrap   = document.getElementById('op-dropdown');
  const form     = ddWrap ? ddWrap.closest('form') : null;
  const inputOp  = document.getElementById('op-value');   // hidden: compra_directa | alquiler | subasta
  const opLabel  = document.getElementById('opLabel');    // texto del botón
  const opAction = document.getElementById('op-action');  // hidden con página destino
  const items    = ddWrap ? ddWrap.querySelectorAll('.op-item') : [];

  const depSel  = document.getElementById('f_departamento');
  const provSel = document.getElementById('f_provincia');
  const distSel = document.getElementById('f_distrito');

  const labels = {
    'compra_directa':'Compra Directa',
    'alquiler':'Alquilar',
    'subasta':'Compra por Subasta Electrónica'
  };

  function setOperacion(val, page){
    if (val) inputOp.value = val;
    if (labels[val]) opLabel.textContent = labels[val];
    if (page) opAction.value = page;
    items.forEach(it => it.classList.toggle('active', it.dataset.value === val));
  }

  // Click en opciones del dropdown (NO navega; solo guarda selección)
  items.forEach(a => {
    a.addEventListener('click', function(e){
      e.preventDefault();
      setOperacion(this.dataset.value, this.dataset.page);
      if (window._afterOperacionChange) window._afterOperacionChange();
    });
  });

  // Al enviar: ajusta la action según la operación elegida
  if (form) {
    form.addEventListener('submit', function(){
      form.action = opAction.value || 'comprar.php';
    });
  }

  // ---- CARGA INICIAL (SSR hydration + operación) ----
  (function preload(){
    const dep  = depSel ? depSel.value : '';
    const prov = <?php echo json_encode($f_provincia); ?>;
    const dist = <?php echo json_encode($f_distrito); ?>;
    const op   = inputOp ? inputOp.value : 'compra_directa';

    if(dep){
      // Provincias
      fetch(`api/get_provincias.php?departamento=${encodeURIComponent(dep)}&tipo_operacion=${encodeURIComponent(op)}`)
        .then(r=>r.json()).then(arr=>{
          setOptions(provSel, arr, 'Provincia');
          if (prov) provSel.value = prov;

          // Distritos si ya hay provincia
          if (prov){
            fetch(`api/get_distritos.php?departamento=${encodeURIComponent(dep)}&provincia=${encodeURIComponent(prov)}&tipo_operacion=${encodeURIComponent(op)}`)
              .then(r=>r.json()).then(ds=>{
                setOptions(distSel, ds, 'Distrito');
                if (dist) distSel.value = dist;
              });
          }
        });
    }
  })();

  // ---- CAMBIO DE DEPARTAMENTO -> cargar PROVINCIAS (por operación) ----
  depSel && depSel.addEventListener('change', function(){
    const dep = this.value;
    const op  = inputOp.value;
    setOptions(provSel, [], 'Provincia');
    setOptions(distSel, [], 'Distrito');
    if(!dep) return;

    fetch(`api/get_provincias.php?departamento=${encodeURIComponent(dep)}&tipo_operacion=${encodeURIComponent(op)}`)
      .then(r=>r.json()).then(arr=>{
        setOptions(provSel, arr, 'Provincia');
      });
  });

  // ---- CAMBIO DE PROVINCIA -> cargar DISTRITOS (por operación) ----
  provSel && provSel.addEventListener('change', function(){
    const dep  = depSel.value;
    const prov = this.value;
    const op   = inputOp.value;
    setOptions(distSel, [], 'Distrito');
    if(!dep || !prov) return;

    fetch(`api/get_distritos.php?departamento=${encodeURIComponent(dep)}&provincia=${encodeURIComponent(prov)}&tipo_operacion=${encodeURIComponent(op)}`)
      .then(r=>r.json()).then(arr=>{
        setOptions(distSel, arr, 'Distrito');
      });
  });

  // ---- CUANDO CAMBIAS LA OPERACIÓN: refresca departamentos ----
  window._afterOperacionChange = function(){
    const op = inputOp.value;
    // Resetea selects
    setOptions(depSel,  [], 'Todos');
    setOptions(provSel, [], 'Provincia');
    setOptions(distSel, [], 'Distrito');
    // Carga departamentos para esa operación
    fetch(`api/get_departamentos.php?tipo_operacion=${encodeURIComponent(op)}`)
      .then(r=>r.json()).then(arr=>{
        setOptions(depSel, arr, 'Todos');
      });
  };

  // ---- Botón Borrar: limpia y redirige a la página de la operación ----
  const btnClear = document.getElementById('btn-clear');
  if (btnClear) {
    btnClear.addEventListener('click', function(){
      const page = opAction.value || 'comprar.php';
      const op   = inputOp.value || 'compra_directa';
      window.location.href = `${page}?tipo_operacion=${encodeURIComponent(op)}`;
    });
  }

  // Inicializa etiqueta/active del dropdown
  setOperacion(inputOp.value, opAction.value);
});
</script>
