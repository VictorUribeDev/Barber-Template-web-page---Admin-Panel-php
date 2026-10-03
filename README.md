# Barbería Premium

A complete PHP-based barber shop management system with a public-facing booking website and an admin dashboard for managing appointments, services, barbers, and customer communications.

## Features

### Public Website (`public/`)
- **Responsive front-page** with image carousel
- **Service catalog** — displays all services with prices and associated style photos
- **Online booking** — customers can reserve appointments by selecting a barber, service, date, and time
- **Real-time availability check** — AJAX endpoint prevents double-booking
- **Team showcase** — presents barbers with bios and photos
- **Contact form** — visitors can send messages stored in the database

### Admin Dashboard (`admin/`)
- **Authentication** — secure login/register with password hashing (`password_hash`)
- **Reservation calendar** — FullCalendar integration with day/week/month views
- **Reservation management** — view, reschedule, delete, and update appointment status
- **Customer reports** — client listing with booking history and bulk status updates
- **Barber & service management** — add/remove barbers and services
- **Publication management** — upload and manage style photo galleries linked to services
- **Message inbox** — read and delete contact form submissions

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 7.4+ / 8.x |
| Database | MySQL 5.7+ / 8.0+ |
| Frontend | Bootstrap 5, AOS (animations), Font Awesome |
| Calendar | FullCalendar 5 |
| AJAX | Vanilla JS + jQuery |

## Project Structure

```
barberia3/
├── admin/                           # Admin dashboard
│   ├── login.php                    # Login page + auth logic
│   ├── registro.php                 # Admin registration
│   ├── logout.php                   # Session destruction
│   ├── dashboard.php                # Main admin panel
│   ├── reservas.php                 # Calendar view of reservations
│   ├── reporte_clientes.php         # Customer reports with history
│   ├── agregar_barbero.php          # Add barber handler
│   ├── agregar_servicio.php         # Add service handler
│   ├── actualizar_reserva.php       # AJAX: reschedule reservation
│   ├── actualizar_estado.php        # AJAX: update reservation status
│   ├── actualizar_estado_global.php # AJAX: bulk status update
│   ├── eliminar_reserva.php         # AJAX: delete reservation
│   ├── eliminar_cliente.php         # AJAX: delete client + reservations
│   ├── obtener_historial.php        # AJAX: fetch client appointment history
│   ├── enviar.php                   # AJAX: handle contact form submissions
│   └── assets/                      # Admin static assets (empty)
├── database/
│   └── init.sql                     # Database schema and seed data
├── public/                          # Public-facing web root
│   ├── index.php                    # Homepage with carousel
│   ├── catalogo.php                 # Services catalog
│   ├── reserva.php                  # Booking form page
│   ├── guardar_reserva.php          # Handle booking submission + validation
│   ├── check_disponibilidad.php     # AJAX: check slot availability
│   ├── contacto.php                 # Contact page with map + form
│   ├── equipo.php                   # Team showcase page
│   ├── includes/
│   │   ├── header.php               # Shared HTML header + navbar
│   │   ├── footer.php               # Shared HTML footer + scripts
│   │   ├── db.php                   # MySQLi connection wrapper
│   │   └── config.php               # Alternate MySQLi connection
│   └── assets/
│       ├── css/
│       │   ├── style.css            # Main dark-theme stylesheet
│       │   └── calendar.css         # FullCalendar custom styles
│       ├── js/                      # Reserved for future JS (empty)
│       └── images/
│           ├── barberia1-3.jpg      # Carousel hero images
│           ├── barbero1-3.jpg       # Team photos
│           ├── catalogo1-3.jpg      # Catalog images
│           └── publicaciones/       # Uploaded style photo gallery
├── .htaccess                        # URL rewriting / access rules
├── README.md
├── LICENSE                          # MIT License
├── CONTRIBUTING.md                  # Contribution guidelines
└── .gitignore
```

## Database Schema

The system uses a single MySQL database (default name: `barberia`). See [`database/init.sql`](database/init.sql) for the full schema with seed data.

| Table | Description |
|-------|-------------|
| `usuarios` | Admin user accounts (id, nombre, email, password, tipo) |
| `barberos` | Barber profiles (id, nombre) |
| `servicio` | Services offered (id, nombre, precio) |
| `reservas` | Appointment bookings (id, id_barbero, nombre_cliente, telefono_cliente, fecha, hora, estado) |
| `mensajes` | Contact form submissions (id, nombre, email, mensaje, fecha) |
| `publicaciones_servicios` | Style photo gallery items (id, servicio_id, titulo, imagen, fecha) |

Reservation statuses: `pendiente` (pending), `confirmada` (confirmed), `completada` (completed), `cancelada` (cancelled).

## Prerequisites

- **PHP** 7.4 or 8.x with `mysqli` extension
- **MySQL** 5.7+ or MariaDB 10.2+
- A web server (Apache, Nginx, or PHP built-in server)

## Installation

See the detailed [Installation Guide](docs/INSTALL.md).

```bash
# 1. Place project in your web root
# 2. Create MySQL database: barberia
# 3. Import database/init.sql
# 4. Configure DB credentials in public/includes/config.php
# 5. Point your web server at public/ (or admin/ for admin access)
```

## Getting Started

### Quick Start (XAMPP)

1. Copy the project to `xampp\htdocs\barberia3`
2. Start Apache and MySQL from XAMPP Control Panel
3. Open `http://localhost/phpmyadmin`, create a database named `barberia`
4. Import `database/init.sql` via phpMyAdmin's Import tab
5. Open `http://localhost/barberia3/public/` to view the site
6. Access the admin panel at `http://localhost/barberia3/admin/login.php`

Default admin credentials (seeded):
- Email: `admin@barberia.com`
- Password: `admin123`

## Documentation

- [Installation Guide](docs/INSTALL.md) — detailed setup instructions
- [Architecture Guide](docs/ARCHITECTURE.md) — code structure and flow documentation
- [Contributing](CONTRIBUTING.md) — how to contribute to this project

## Contributing

Contributions are welcome! Please read our [Contributing Guide](CONTRIBUTING.md) first.

## License

This project is licensed under the MIT License — see the [LICENSE](LICENSE) file for details.
