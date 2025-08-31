
// Simple helpers
function $(sel, root=document){ return root.querySelector(sel) }
function $all(sel, root=document){ return Array.from(root.querySelectorAll(sel)) }

// Cascading selects for filters
async function loadProvincias(departamento, targetSel){
  const res = await fetch(`api/get_provincias.php?departamento=${encodeURIComponent(departamento)}`);
  const data = await res.json();
  const target = $(targetSel);
  target.innerHTML = '<option value="">Provincia</option>';
  data.forEach(p => {
    const opt = document.createElement('option');
    opt.value = p; opt.textContent = p; target.appendChild(opt);
  });
  // Reset distritos
  const dist = $('#f_distrito'); if(dist){ dist.innerHTML = '<option value="">Distrito</option>'; }
}

async function loadDistritos(departamento, provincia, targetSel){
  const res = await fetch(`api/get_distritos.php?departamento=${encodeURIComponent(departamento)}&provincia=${encodeURIComponent(provincia)}`);
  const data = await res.json();
  const target = $(targetSel);
  target.innerHTML = '<option value="">Distrito</option>';
  data.forEach(d => {
    const opt = document.createElement('option');
    opt.value = d; opt.textContent = d; target.appendChild(opt);
  });
}

// Event wiring (if filters exist on page)
document.addEventListener('DOMContentLoaded', () => {
  const dep = $('#f_departamento');
  const prov = $('#f_provincia');
  const dist = $('#f_distrito');

  if(dep){
    dep.addEventListener('change', e => {
      const v = e.target.value;
      if(v) loadProvincias(v, '#f_provincia');
    });
  }
  if(prov){
    prov.addEventListener('change', e => {
      const v = e.target.value;
      const d = dep ? dep.value : '';
      if(v && d) loadDistritos(d, v, '#f_distrito');
    });
  }

  // Thumbnail click to move carousel
  $all('[data-thumb-target]').forEach(img => {
    img.addEventListener('click', () => {
      const target = img.getAttribute('data-thumb-target');
      const slideIdx = parseInt(img.getAttribute('data-index') || '0', 10);
      const carousel = document.querySelector(target);
      if(carousel){
        const bsCarousel = bootstrap.Carousel.getOrCreateInstance(carousel);
        bsCarousel.to(slideIdx);
      }
    });
  });
});
