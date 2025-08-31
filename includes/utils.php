<?php
function e($s){ 
  return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); 
}

function currency_symbol($m){ 
  return $m === 'PEN' ? 'S/ ' : '$ '; 
}

function is_active($key){
  $uri = $_SERVER['REQUEST_URI'] ?? '';
  return strpos($uri, $key)!==false ? 'active' : '';
}

/**
 * Renderiza paginación Bootstrap
 */
function render_pagination($total, $page, $perPage){
  if ($total <= $perPage) return; // no mostrar si solo hay 1 página

  $totalPages = max(1, (int)ceil($total / $perPage));
  $qs = $_GET ?? [];
  unset($qs['page']);
  $self = basename($_SERVER['PHP_SELF']);
  $base = $self . (!empty($qs) ? ('?' . http_build_query($qs) . '&') : '?');

  $range = 2;
  $start = max(1, $page - $range);
  $end   = min($totalPages, $page + $range);

  echo '<nav aria-label="Paginación" class="mt-4">';
  echo '<ul class="pagination justify-content-center">';

  // Prev
  $disabled = $page <= 1 ? ' disabled' : '';
  echo '<li class="page-item'.$disabled.'"><a class="page-link" href="'.$base.'page='.($page-1).'">Anterior</a></li>';

  // First + ellipsis
  if ($start > 1) {
    echo '<li class="page-item"><a class="page-link" href="'.$base.'page=1">1</a></li>';
    if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
  }

  // Range
  for ($i = $start; $i <= $end; $i++) {
    $active = $i == $page ? ' active' : '';
    echo '<li class="page-item'.$active.'"><a class="page-link" href="'.$base.'page='.$i.'">'.$i.'</a></li>';
  }

  // Last + ellipsis
  if ($end < $totalPages) {
    if ($end < $totalPages - 1) echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
    echo '<li class="page-item"><a class="page-link" href="'.$base.'page='.$totalPages.'">'.$totalPages.'</a></li>';
  }

  // Next
  $disabled = $page >= $totalPages ? ' disabled' : '';
  echo '<li class="page-item'.$disabled.'"><a class="page-link" href="'.$base.'page='.($page+1).'">Siguiente</a></li>';

  echo '</ul>';
  echo '<p class="text-center text-muted small mb-0">';
  echo 'Mostrando '.min($perPage, $total - ($page-1)*$perPage).' de '.$total.' resultados · Página '.$page.' de '.$totalPages;
  echo '</p>';
  echo '</nav>';
}
?>
