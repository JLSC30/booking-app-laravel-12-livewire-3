# Booking App

A simple **Laravel 12 + Livewire** booking application for managing schedules, appointments, and user bookings. This app includes features for **role-based access**, **force password change on first login**, and real-time UI interactions with Mary UI components.

---

## 📝 Features

- **User Authentication**
  - Login/logout
  - Force password change on first login
  - Role-based access control (optional: Admin/User)

- **Booking System**
  - Create schedules with multiple time slots
  - Limit number of bookings per slot
  - Block or modify slots dynamically
  - Real-time booking updates via Livewire

- **Admin Panel**
  - Manage schedules and bookings
  - View all bookings
  - Approve or reject bookings (optional)

- **Livewire + Mary UI Integration**
  - Dynamic forms with validation
  - Toast notifications for success/failure
  - Responsive and modern UI

- **Security Features**
  - Password hashing using Laravel `Hash::make()`
  - Session regeneration on password change
  - Force password update middleware

---

## 💻 Tech Stack

- **Backend:** Laravel 12, Livewire  
- **Frontend:** Blade + Mary UI, Tailwind CSS v4  
- **Database:** MySQL / MariaDB (configurable in `.env`)  
- **Authentication:** Laravel built-in auth  
- **Testing:** PestPHP + Livewire testing  

---

## 🚀 Installation

1. Clone the repository:

```bash
git clone <repository-url>
cd booking-appv3
```

2. Install PHP Dependencies:
```bash
composer install
```

3. Install NPM dependencies:
```bash
npm install
npm run dev
```

4. Copy .env and configure database:
```bash
cp .env.example .env
php artisan key:generate
```
Update .env
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=booking_app
DB_USERNAME=root
DB_PASSWORD=secret
```

5. Run migrations and seeders:
```bash
php artisan migrate --seed
```

6. Serve the application
```bash
php artisan serve
```

## 🧪 Running Tests
The application uses Pest for feature testing.
```
php artisan test
```

- Tests cover:
-- Force password change
-- Booking functionality
-- Middleware enforcement
-- Livewire component behavior
