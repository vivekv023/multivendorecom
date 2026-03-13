# Multi-Vendor E-Commerce Platform

This project is a **multi-vendor e-commerce platform built with
Laravel** where multiple vendors can sell their products on the same
marketplace. Customers can browse products, add them to their cart (even
as a guest), and place orders that are automatically split between
vendors.

The goal of this project is to demonstrate a **clean Laravel
architecture with scalable design patterns** such as Repository Pattern
and Service Layer while keeping the code modular and maintainable.

------------------------------------------------------------------------

# Technology Stack

The application is built using the following technologies:

-   **Backend:** Laravel 11
-   **Frontend:** Bootstrap 5, Font Awesome 6
-   **Database:** MySQL / SQLite
-   **JavaScript:** JavaScript
-   **CSS:** CSS 

------------------------------------------------------------------------

# Project Structure

    multivendor-ecom/
    ├── app/
    │   ├── Http/
    │   │   ├── Controllers/
    │   │   ├── Requests/
    │   │   └── Middleware/
    │   ├── Models/
    │   ├── Repositories/
    │   │   ├── Interfaces/
    │   │   └── Eloquent/
    │   └── Services/
    ├── database/
    │   ├── migrations/
    │   └── seeders/
    ├── public/
    │   ├── css/
    │   └── js/
    └── resources/
        └── views/

------------------------------------------------------------------------

# Architecture

## Repository Pattern

The Repository Pattern separates **data access logic from business
logic**.

Examples:

-   ProductRepositoryInterface → ProductRepository
-   OrderRepositoryInterface → OrderRepository
-   VendorRepositoryInterface → VendorRepository

This makes the application easier to maintain and test.

------------------------------------------------------------------------

## Service Layer

The **Service Layer** contains the core business logic.

Services used in this project:

-   CartService -- Handles cart operations
-   CheckoutService -- Manages order processing
-   ProductService -- Product related operations
-   OrderService -- Order management
-   VendorService -- Vendor operations

Controllers remain clean while services handle complex logic.

------------------------------------------------------------------------

## Form Requests

Laravel **Form Request classes** are used for request validation.

Examples:

-   LoginRequest
-   VendorLoginRequest
-   UpdateOrderStatusRequest

------------------------------------------------------------------------

# Setup Instructions

## Prerequisites

Make sure your system has:

-   PHP 8.2+
-   Composer
-   MySQL or SQLite

------------------------------------------------------------------------

## Installation

### 1. Clone the repository

    git clone <repository-url>
    cd multivendor-ecom

### 2. Install dependencies

    composer install

### 3. Setup environment file

    cp .env.example .env

Update database credentials in `.env`:

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=multivendor_ecom
    DB_USERNAME=root
    DB_PASSWORD=your_password

### 4. Generate application key

    php artisan key:generate

### 5. Run migrations

    php artisan migrate

### 6. Seed database

    php artisan db:seed

Seeders included:

-   CustomerSeeder
-   ProductSeeder
-   VendorSeeder

### 7. Run development server

    php artisan serve

------------------------------------------------------------------------

# Access URLs

Store: http://127.0.0.1:8000

Admin Panel: http://127.0.0.1:8000/admin

Vendor Login: http://127.0.0.1:8000/vendor/login

------------------------------------------------------------------------

# Sample Credentials

## Admin

Email: admin@example.com\
Password: password

## Customers

customer@example.com / password\
jane@example.com / password

## Vendors

techzone@vendor.com / vendor123\
fashion@vendor.com / vendor123\
home@vendor.com / vendor123

------------------------------------------------------------------------

# Trade-offs and Assumptions

Guest Cart uses Laravel session. In production Redis can be used.

Payment system currently supports **Cash on Delivery** only. Payment
gateways like Razorpay or Stripe can be added.

Product images are stored using URLs or local paths. Cloud storage like
**AWS S3** can be used in production.

Email notifications are not implemented but can be added using Laravel
Notifications.

Orders are **split per vendor** when multiple vendor products are
purchased.

Vendor approval is automatic. In real marketplaces admin approval
workflow should be added.

Basic stock tracking is implemented. Inventory reservation during
checkout can prevent overselling in high traffic systems.

------------------------------------------------------------------------

# Security

The application includes basic security practices:

-   CSRF protection
-   Password hashing using bcrypt
-   Role based access control
-   SQL injection protection via Eloquent
-   XSS protection via Blade templating

