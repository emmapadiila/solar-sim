# SolarSim

Simulador de ahorro con paneles solares para hogares colombianos (PHP + MySQL/MariaDB, sin frameworks).

## Estructura

```
solar-sim/
├── public/                     ← ÚNICA carpeta accesible desde el navegador
│   ├── index.php               Login / registro
│   ├── dashboard.php           Inicio
│   ├── calculadora.php         Formulario de simulación
│   ├── historial.php           Simulaciones del usuario
│   ├── estadisticas.php        Panel de administrador
│   ├── educativo.php, about.php, contacto.php
│   ├── api/                    Endpoints JSON (los llama el JavaScript)
│   │   ├── auth/               login.php · registro.php · logout.php
│   │   ├── simulaciones/       calcular.php · guardar.php · detalle.php · exportar.php
│   │   └── contacto.php
│   └── assets/
│       ├── css/style.css
│       ├── js/                 app.js (común) + un archivo por página
│       └── images/
├── src/                        Lógica de la aplicación (no accesible por web)
│   ├── bootstrap.php           Arranque: config, sesión y carga de clases
│   ├── helpers.php             e(), config(), render(), json_response()...
│   ├── Database.php            Conexión PDO única
│   ├── Auth.php                Sesión y control de acceso
│   ├── Calculadora.php         Modelo de cálculo solar (fuente única de verdad)
│   └── repositories/           Todo el SQL: Usuario, Simulacion, Estadisticas
├── templates/
│   ├── layout/                 head.php · header.php (menú) · footer.php
│   └── export/simulacion.php   Reporte descargable
├── config/
│   ├── config.php              Valores por defecto (XAMPP)
│   └── config.local.example.php
├── database/paneles_solares.sql
├── .htaccess                   Redirige todo a public/
└── index.php                   Respaldo si no hay mod_rewrite
```

## Cómo se conecta todo

```
Navegador ──► public/<pagina>.php
                 │  require src/bootstrap.php   (config + sesión + clases)
                 │  Auth::requireLogin() / requireAdmin()
                 │  Repositorio::consulta()  ──► Database (PDO) ──► MySQL
                 └► render('layout/header') … HTML … render('layout/footer')
                                                        └► assets/js/app.js + <pagina>.js

assets/js/<pagina>.js ──fetch JSON──► public/api/.../*.php
                                        │  bootstrap + Auth::requireLoginApi()
                                        │  run_api(): valida, usa Calculadora / repositorios
                                        └► json_response([...])
```

**Flujo de una simulación**

1. `calculadora.js` envía los datos del formulario a `api/simulaciones/calcular.php`.
2. `Calculadora::validar()` + `Calculadora::calcular()` devuelven los resultados y se muestran con Chart.js.
3. Al guardar, se envían **solo los datos de entrada** a `api/simulaciones/guardar.php`, que recalcula en el servidor y
   `SimulacionRepository::crear()` lo guarda en una transacción (`tbl_simulacion`, `tbl_resultados`, `tbl_historial`, `tbl_detalle_historial`).

**Convenciones**

- Página nueva: crear `public/x.php`, añadir el enlace en `templates/layout/header.php` y, si necesita JS, `assets/js/x.js`.
- Endpoint nuevo: `public/api/...`, siempre con `require_method()`, `Auth::requireLoginApi()` y `run_api()`.
- SQL solo dentro de `src/repositories/`. Todo lo que se imprima en HTML pasa por `e()`.
- Las ciudades, estratos y tarifas viven solo en `src/Calculadora.php`; los `<select>` se generan desde ahí.

## Instalación (XAMPP)

1. Copiar la carpeta en `C:\xampp\htdocs\solar-sim`.
2. En phpMyAdmin, importar `database/paneles_solares.sql`.
3. Abrir <http://localhost/solar-sim/>.
4. Si la base de datos no usa `root` sin contraseña, copiar `config/config.local.example.php` como
   `config/config.local.php` y ajustar los datos (este archivo no se sube a git).

Usuarios de prueba: `Emma / emma123` (admin), `Breiner / breiner123`, `Juan / juan123`.
Sus contraseñas se guardan en texto plano en el `.sql`, pero se convierten a hash la primera vez que inician sesión.

Requisitos: PHP 8.0+ con `pdo_mysql`, MySQL 5.7+ o MariaDB 10.4+, Apache con `mod_rewrite` (recomendado).
