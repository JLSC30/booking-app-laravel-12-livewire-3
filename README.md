<div align="center">
  <h1>Booking App</h1>
  <p>A modern booking and appointment scheduling application built with the TALL stack, featuring a clean UI powered by <strong>Mary UI</strong>.</p>
</div>

![PHP](https://img.shields.io/badge/PHP-8.2%2B-blue)
![Laravel](https://img.shields.io/badge/Laravel-12.x-orange)
![Livewire](https://img.shields.io/badge/Livewire-3.x-f8b)
![License](https://img.shields.io/badge/License-MIT-green)

---

This application provides a simple yet powerful interface for managing branches, services, schedules, and appointments. It's designed with a focus on real-time interactions using Livewire and a clean, responsive layout from Mary UI.

## ✨ Key Features

-   **Authentication**: Secure login/logout powered by Laravel Fortify.
    -   **Force Password Change**: Prompts users to change their temporary password on their first login.
-   **Branch Management**: Create and manage multiple business locations.
-   **Service Management**: Define the different services offered.
-   **Dynamic Scheduling**:
    -   Create weekly schedules for each branch.
    -   Define available time slots for each day.
    -   Automatically generates available appointment slots.
-   **Appointment Booking**:
    -   Users can book available appointment slots.
    -   Real-time updates on available slots.
    -   Automatic email confirmations for new appointments.
-   **User Roles & Permissions**: Basic structure for Admin vs. User roles.
-   **Data Export**: Export users, branches, and services to XLSX files.
-   **Developer Experience**:
    -   Includes a concurrent development script (`npm run dev:concurrent`).
    -   Code style is enforced by Laravel Pint and Prettier.

## 💻 Tech Stack

-   **Backend**: PHP 8.2+, Laravel 12
-   **Frontend**: Blade, Tailwind CSS v4, Vite
-   **UI**: [Mary UI](https://mary-ui.com/) v2.4 on top of DaisyUI
-   **Full-stack Framework**: Livewire 3 + Volt
-   **Authentication**: Laravel Fortify
-   **Database**: SQLite by default, supports MySQL/PostgreSQL
-   **Testing**: PestPHP
-   **Queue Management**: Database driver
-   **File Exports**: Maatwebsite/excel

## 🚀 Getting Started

Follow these instructions to get the project up and running on your local machine.

### Prerequisites

-   PHP >= 8.2
-   Node.js & npm
-   Composer
-   A database server (SQLite is the default, no server needed)

### 1. Installation

You can use the convenient `setup` script included in `composer.json`.

```bash
# Clone the repository
git clone <repository-url>
cd booking-appv3

# Copy the environment file
cp .env.example .env

# Run the setup script (installs dependencies, generates key, runs migrations)
# This will use SQLite by default.
composer setup
```

### 2. Configure Environment (.env)

The `setup` script will create the `.env` file. The application is configured to work out-of-the-box with **SQLite**, which requires no further setup.

If you prefer to use **MySQL** or another database, update the `DB_*` variables in your `.env` file accordingly and make sure the database exists.

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=booking_app
DB_USERNAME=root
DB_PASSWORD=secret
```

Other important variables:

-   `CURRENCY`: Sets the currency symbol used in the app (e.g., `USD`, `EUR`).
-   `HOURLY_BOOKING_LIMIT`: The maximum number of bookings allowed in a single time slot.
-   `MAIL_*`: Configure these to enable sending confirmation emails. Mailtrap or a local alternative like Mailpit is recommended for development.

### 3. Running the Development Servers

The project includes a concurrent script that runs all necessary development processes.

```bash
# This single command starts the PHP server, Vite, queue listener, and log watcher.
npm run dev:concurrent
```
*(Note: `npm run dev:concurrent` is an alias for the `dev` script in `composer.json`)*

You can now access the application at **http://localhost:8000**.

## 🧪 Running Tests

The application uses Pest for testing. To run the test suite:

```bash
php artisan test
```

To run tests with code coverage:

```bash
composer test-coverage
```

## 💅 Code Style

This project enforces code style using Laravel Pint for PHP and Prettier for Blade files.

```bash
# Format all PHP files
composer format

# Check PHP files for formatting issues without modifying them
composer format-check

# Format all Blade files
npm run blade-format
```

## 📄 License

This project is open-source and licensed under the [MIT License](LICENSE).