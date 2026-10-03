# Architecture Guide

This document describes the code structure, request flow, and key patterns of the Barbería Premium codebase.

## Overview

Barbería Premium is a two-part PHP application:

1. **Public website** (`public/`) — A customer-facing site where users browse services, book appointments, view the team, and send contact messages. No authentication required.
2. **Admin panel** (`admin/`) — A protected backend for managing barbers, services, reservations, publications, and messages. Requires authentication via `usuario_id` session.

Both parts share the same MySQL database (`barberia`) and use raw MySQLi (procedural + object-oriented mix) for database access.

## Directory Structure

```
project-root/
├── admin/                          # Admin panel (requires authentication)
│   ├── login.php                   # Login page + inline auth processor
│   ├── registro.php                # Registration page + inline handler
│   ├── logout.php                  # Destroys session, redirects to login
│   ├── dashboard.php               # Central admin panel — all CRUD operations land here
│   ├── reservas.php                # FullCalendar view of all reservations
│   ├── reporte_clientes.php        # Customer listing with booking history (AJAX)
│   ├── agregar_barbero.php         # POST handler — inserts a new barber
│   ├── agregar_servicio.php        # POST handler — inserts a new service
│   ├── actualizar_reserva.php      # AJAX endpoint — reschedules a reservation
│   ├── actualizar_estado.php       # AJAX endpoint — updates single reservation status
│   ├── actualizar_estado_global.php # AJAX endpoint — bulk status update for a client
│   ├── eliminar_reserva.php        # AJAX endpoint — deletes a reservation by ID
│   ├── eliminar_cliente.php        # AJAX endpoint — deletes client + their reservations
│   ├── obtener_historial.php       # AJAX endpoint — returns client appointment history
│   ├── enviar.php                  # POST handler — stores contact form message
│   └── assets/                     # Reserved for admin static files (currently empty)
│
├── public/                          # Public-facing web root
│   ├── index.php                    # Homepage with Bootstrap carousel
│   ├── catalogo.php                 # Services catalog with price table and style photos
│   ├── reserva.php                  # Booking form (name, phone, barber, service, date, time)
│   ├── guardar_reserva.php          # Handles booking submission — validates availability + inserts
│   ├── check_disponibilidad.php     # AJAX endpoint — checks if a time slot is available
│   ├── contacto.php                 # Contact page with Google Map embed + message form
│   ├── equipo.php                   # Team showcase with barber profile cards
│   ├── includes/
│   │   ├── header.php               # Shared `<head>`, navbar, Bootstrap/AOS CDN links
│   │   ├── footer.php               # Closing tags, social icons, script includes
│   │   ├── db.php                   # MySQLi connection ($conn) — primary DB connector
│   │   └── config.php               # Alternate MySQLi connection (duplicate of db.php)
│   └── assets/
│       ├── css/
│       │   ├── style.css            # Main dark-theme stylesheet ("Nocturno Urbano")
│       │   └── calendar.css         # FullCalendar-specific styles
│       ├── js/                      # Reserved for JS files (currently empty)
│       └── images/                  # Hero carousel, team, catalog, and publication photos
│
├── database/
│   └── init.sql                     # Full schema + seed data
│
├── .htaccess                        # Root-level Apache rules (currently empty)
├── README.md
├── LICENSE                          # MIT License
├── CONTRIBUTING.md                  # Contribution guidelines
├── .gitignore
└── docs/
    ├── INSTALL.md                   # Detailed setup guide
    └── ARCHITECTURE.md              # This file
```

## Database Schema Summary

```
usuarios            — Admin accounts (auth)
barberos            — Barber profiles
servicio            — Services with prices
reservas            — Bookings (joins barberos + servicio)
mensajes            — Contact form submissions
publicaciones_servicios — Style photo gallery (joins servicio)
```

## Request Flow

### Public Booking Flow

```
1. User visits /index.php
   → header.php (navbar, Bootstrap)
   → Hero carousel
   → footer.php (AOS init, social links)

2. User clicks "Reserva" → /reserva.php
   → header.php
   → Booking form (fields: nombre, telefono, id_barbero, id_servicio, fecha, hora)
   → AJAX: check_disponibilidad.php validates slot in real-time
   → Submits to guardar_reserva.php

3. guardar_reserva.php
   → Validates availability (SELECT on reservas)
   → Inserts new reservation (INSERT into reservas, estado='pendiente')
   → Returns success/error message with auto-redirect
```

### Admin Authentication Flow

```
1. User visits /admin/login.php
   → Includes db.php for DB connection
   → POST: validates email + password via password_verify()
   → On success: sets $_SESSION['usuario_id'], $_SESSION['nombre']
   → Redirects to dashboard.php

2. All admin pages (dashboard.php, reservas.php, reporte_clientes.php, etc.)
   → Check `isset($_SESSION['usuario_id'])`
   → If not set → redirect to login.php

3. logout.php → session_destroy() → redirect to login.php
```

### Reservation Management Flow

```
1. Admin visits /admin/dashboard.php
   → Lists barberos, servicios, mensajes, publicaciones

2. Admin visits /admin/reservas.php
   → Loads FullCalendar with events from reservas table
   → Clicks an event → modal with edit/delete options
   → "Reagendar" → POST to actualizar_reserva.php (JSON response)
   → "Eliminar" → GET to eliminar_reserva.php (JSON response)

3. Admin visits /admin/reporte_clientes.php
   → Lists clients grouped by name+phone
   → "Ver Historial" → AJAX to obtener_historial.php
   → In modal: change per-reservation status (AJAX to actualizar_estado.php)
   → Or bulk-change all client statuses (AJAX to actualizar_estado_global.php)
```

## Key Patterns

### Authentication
- Session-based auth using `$_SESSION['usuario_id']`
- Passwords stored as bcrypt hashes via `password_hash($pwd, PASSWORD_DEFAULT)`
- No CSRF protection — this is a known security gap (see Notes)
- Auth is checked at the top of each admin script with a redirect to `login.php`

### Database Access
- Uses `mysqli` object-oriented style: `$conn = new mysqli($host, $user, $pass, $db)`
- Shared connection via `include "includes/db.php"` (or `config.php`)
- Prepared statements used in most AJAX endpoints (e.g., `actualizar_reserva.php`, `eliminar_reserva.php`)
- Some older scripts use direct string interpolation (e.g., `agregar_servicio.php`, `enviar.php`) — these are security risks

### AJAX Conventions
All AJAX endpoints return JSON with a consistent structure:
```json
{ "success": true }
// or
{ "success": false, "error": "Description of the error" }
```

HTTP status codes:
- `200` — Success
- `400` — Bad request (validation failure)
- `403` — Unauthorized (not logged in)
- `404` — Resource not found
- `500` — Server error

### Templates
Shared markup is split into:
- `public/includes/header.php` — `<!DOCTYPE>`, `<head>`, navbar, opening `<body>`
- `public/includes/footer.php` — Footer, closing `</body></html>`, script tags

These are `include`d at the top and bottom of each page.

## External Dependencies

| Library | Version | Loaded From |
|---------|---------|-------------|
| Bootstrap CSS | 5.3.0 | CDN (jsdelivr) |
| Bootstrap JS Bundle | 5.3.0 | CDN (jsdelivr) |
| Bootstrap CSS/JS | 4.5.2 | CDN (stackpath) — used in reservas.php and reporte_clientes.php |
| AOS (Animations) | 2.3.4 | CDN (jsdelivr) |
| Font Awesome | 6.4.2 / 6.0.0-beta3 / 5.15.4 | CDN (cdnjs) |
| jQuery | 3.5.1 / 3.6.0 | CDN (code.jquery.com) |
| FullCalendar | 5.11.3 | CDN (jsdelivr) |
| Popper.js | 2.11.6 | CDN (jsdelivr) — used in dashboard.php |

No package manager (npm/Composer) is used — all dependencies are loaded via CDN.

## Notes & Future Improvements

- **CSRF protection**: Not implemented. All state-changing forms (POST/DELETE) should use CSRF tokens.
- **SQL injection**: Most AJAX endpoints use prepared statements, but `agregar_barbero.php`, `agregar_servicio.php`, and `enviar.php` use string interpolation. These should be refactored.
- **Input validation**: Server-side validation is minimal. More robust validation (e.g., `filter_var` for emails, phone validation) is recommended.
- **Separation of concerns**: Logic and presentation are mixed. A future refactor could introduce a simple MVC pattern or template engine.
- **Error handling**: Most scripts use `die()` on errors. A centralized error handler would improve maintainability.
- **`procesar_login.php`**: References a non-existent `conexion.php` file. It appears to be a legacy alternative to `login.php`. Should be removed or fixed.
- **`config.php` vs `db.php`**: Two near-identical connection files exist. Consolidating to one would reduce confusion.
