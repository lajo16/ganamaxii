<?php
require_once __DIR__ . '/db.php';

function build_where_from_get(&$params, &$types){
  // columnas => estrategia de comparación
  $map = [
    'departamento'   => ['col' => 'departamento',   'mode' => 'loose'], // LOWER(TRIM(col)) = LOWER(?)
    'provincia'      => ['col' => 'provincia',      'mode' => 'loose'],
    'distrito'       => ['col' => 'distrito',       'mode' => 'loose'],
    'tipo_inmueble'  => ['col' => 'tipo_inmueble',  'mode' => 'strict'], // = ?
    'moneda'         => ['col' => 'moneda',         'mode' => 'strict'],
    'tipo_operacion' => ['col' => 'tipo_operacion', 'mode' => 'strict']
  ];

  // valores que deben considerarse "vacío"
  $placeholders = ['','Todos','Provincia','Distrito',null];

  $wheres = [];
  foreach($map as $key => $cfg){
    if(!isset($_GET[$key])) continue;

    // limpia y normaliza entrada
    $val = trim((string)$_GET[$key]);

    // ignora placeholders
    if(in_array($val, $placeholders, true)) continue;

    if ($cfg['mode'] === 'loose') {
      // tolerante a espacios/case
      $wheres[] = "LOWER(TRIM({$cfg['col']})) = LOWER(?)";
      $params[] = $val;
      $types   .= "s";
    } else {
      // match estricto
      $wheres[] = "{$cfg['col']} = ?";
      $params[] = $val;
      $types   .= "s";
    }
  }

  return $wheres ? (" WHERE " . implode(" AND ", $wheres)) : "";
}

function fetch_props($conn, $limit=12, $offset=0){
  $params=[]; $types="";
  $where = build_where_from_get($params,$types);

  $sql = "SELECT p.*,
            (SELECT ruta_imagen
               FROM imagenes_propiedades i
              WHERE i.propiedad_id = p.id
              ORDER BY id
              LIMIT 1) AS portada
          FROM propiedades p
          $where
          ORDER BY p.fecha_creacion DESC, p.id DESC
          LIMIT ? OFFSET ?";

  $types .= "ii";
  $params[] = $limit;
  $params[] = $offset;

  $stmt = $conn->prepare($sql);
  $stmt->bind_param($types, ...$params);
  $stmt->execute();
  $res = $stmt->get_result();

  $rows = [];
  while($r = $res->fetch_assoc()){ $rows[] = $r; }

  $stmt->close();
  return $rows;
}
function count_props($conn){
  $params=[]; $types="";
  $where = build_where_from_get($params,$types);

  $sql = "SELECT COUNT(*) AS total
          FROM propiedades p
          $where";

  $stmt = $conn->prepare($sql);
  if($types){
    $stmt->bind_param($types, ...$params);
  }
  $stmt->execute();
  $res = $stmt->get_result();
  $row = $res->fetch_assoc();
  $stmt->close();

  return (int)($row['total'] ?? 0);
}


function fetch_images($conn, $pid){
  $stmt = $conn->prepare("SELECT ruta_imagen FROM imagenes_propiedades WHERE propiedad_id=? ORDER BY id");
  $stmt->bind_param("i", $pid);
  $stmt->execute();
  $res = $stmt->get_result();
  $imgs=[];
  while($r=$res->fetch_assoc()){ $imgs[]=$r['ruta_imagen']; }
  $stmt->close();
  return $imgs;
}
