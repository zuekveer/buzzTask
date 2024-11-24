# Test Task - Symfony Project

This is a Symfony-based web application for managing orders, with support for ticket types (adult, kid, VIP). It uses Docker for local development, MySQL for database management, and Node.js with Yarn for frontend asset management. The application provides an API for creating and fetching orders.

## Features

- Symfony version: 6.4
- API for managing orders (create and retrieve).
- Ticket types: adult, kid, and VIP.
- Database: MySQL.
- API documentation: Swagger UI integrated via `NelmioApiDocBundle`.

---

## Local Deployment

1. **Clone the repository**:
    ```bash
    git clone git@github.com:zuekveer/buzzTask.git
    cd buzzTask
    ```

2. **Set up environment variables**:
   Edit the `.env.example` file to `.env` to configure the database and application settings.

3. **Build and start Docker containers**:
    ```bash
    make init
    ```

4. **Install PHP dependencies**:
    ```bash
    make composer-install
    ```
5. **Access the application**:
    - App: `http://localhost:8000`
    - phpMyAdmin: `http://localhost:8080`

6. **API Documentation**: `http://localhost:8000/api/doc`

---

## Contact

For more information, reach out via Telegram: [t.me/zuekveer](https://t.me/zuekveer)
