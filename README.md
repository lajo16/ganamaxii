# GanaMaxii — Portal Inmobiliario (PHP + MySQL + Bootstrap)

**Características:**
- Home + Comprar (Directa, Subasta) + Alquilar + Contacto + Admin
- Filtros: Departamento → Provincia → Distrito (cascada vía AJAX), Tipo de inmueble, Moneda (USD/PEN)
- CRUD de propiedades (crear/editar/eliminar) con subida de **hasta 5 imágenes** por propiedad
- Login de administrador con contraseña **hasheada** (usuario inicial `admin`, clave `Gana2025!` — se crea automáticamente si no existen usuarios)
- Galería con **Bootstrap Carousel** y miniaturas
- Bootstrap 5 + Bootstrap Icons + diseño responsive

## Requisitos
- PHP 7.4+
- MySQL 5.7+/MariaDB (phpMyAdmin para importar el SQL)
- Servidor local (XAMPP, WAMP, MAMP) o hosting con PHP + MySQL

## Instalación
1. Crea una BD `ganamaxii` e importa `sql/ganamaxii.sql` en phpMyAdmin.
2. Configura tus credenciales en `includes/db.php`.
3. Copia todo el proyecto a tu servidor (`htdocs/ganamaxii` en XAMPP, por ejemplo).
4. Abre `http://localhost/ganamaxii/`.
5. Panel admin: `http://localhost/ganamaxii/admin/login.php`  
   Usuario: **admin** · Clave: **Gana2025!** (cámbiala luego).

## Estructura
- `index.php` · Home con filtros y destacados
- `comprar.php`, `subasta.php`, `alquilar.php` · Listados por operación
- `contacto.php` · Formulario + Google Maps
- `admin/` · Login + Dashboard + CRUD
- `api/` · Endpoints AJAX para provincias/distritos
- `includes/` · Conexión DB, header/footer, filtros, utilidades
- `assets/` · CSS, JS, imágenes

## Notas
- Las listas de provincias/distritos se construyen **dinámicamente** en función de lo que exista en la tabla `propiedades`, para que los filtros siempre estén sincronizados.
- El límite de 5 imágenes se valida al agregar/editar.
- Las imágenes se guardan en `assets/img/properties/p<ID>/` y se registran en la tabla `imagenes_propiedades`.


## 🌐 Demo en vivo
- Producción: [ganamaxii.pe](https://ganamaxii.pe)
- Pruebas: [g.ecla.pe](https://g.ecla.pe)


## ⚙️ Instalación local
1. Clona el repo: git clone https://github.com/tuusuario/ganamaxii.git
2. Copia includes/config.sample.php → config.php y pon tus credenciales locales.
3. Crea la BD `ganamaxii` en phpMyAdmin.
4. Importa sql/ganamaxii.example.sql.
5. Abre http://localhost/ganamaxii en tu navegador.

Autor: Luis A. Julca O. 
LinkIn: https://www.linkedin.com/in/l-alonso-julca-o/
