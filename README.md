## 📚 Code Study Overview

This project is a personal code study developed using the **Laravel** framework, with the goal of practicing back-end architecture and applying modern coding principles. The focus has been on clean code, maintainability, and understanding how to structure scalable APIs.

### 🧠 Key Concepts Explored

- **SOLID Principles**
  - Single Responsibility: separated services, repositories, and controllers
  - Dependency Inversion: designed for abstraction using interfaces
  - Open/Closed: used enums and clean class extensions

- **Design Patterns**
  - Repository Pattern for clean data access
  - Service Layer for encapsulating business logic
  - Factory Pattern for model generation in testing
  - Transformer Pattern via Laravel Resources

- **Clean Architecture Practices**
  - Validation through custom Form Requests
  - Custom exceptions for clearer error handling
  - Middleware for securing internal dev routes

- **RESTful API Design**
  - Token-based authentication using Laravel Sanctum
  - Well-structured endpoints for user actions and transactions

---

# 🚀 Project Setup

## 📌 Prerequisites

This project uses:
- **Laravel** 12
- **PHP** 8.2  

## 📖 Documentation

The API request collection is available in the `documentations` folder.  

📌 [Postman Collection – Digital Wallet](https://github.com/BreakinAnt/digital_wallet/blob/main/documentations/Digital%20Wallet.postman_collection.json)  


## ⚙️ Running the Project

1. Install Composer dependencies:  
```sh
   composer install
```
2. Copy the .env.example file to `.env`:
```
   cp .env.example .env
```
4. Generate the application key:
```
php artisan key:generate
```

5. Configure the environment variables in the .env file, making sure to set up the database.
   
6. Run the database migrations and seeders:
<br>*Laradock will create a MySQL container. If you're not using Laradock, start the database using your preferred method.*
```
    php artisan migrate
    php artisan db:seed
```

7. __*__ Start the local server:
```
   php artisan serve
```
8. Run automated tests:
```
    php artisan test
```

*__It's recommended to run this project using Laradock.__*

## 🐳 Running the Project via Laradock (Recommended)
1. Inside the project root folder, run the following command:  
```
   git clone https://github.com/Laradock/laradock.git
```
2. Navigate to the Laradock folder inside the project.
```
    cd laradock
```
3. Copy the .env.example file to `.env`:
```
   cp .env.example .env
```
4. Open `.env` and search for `### PHP Version`.
5. Change the `PHP_VERSION` variable to `8.2`.
6. Run the following command:
```
    docker-compose up -d nginx mysql workspace redis
```
7. By default, the database connection should be configured as follows (Inside Laravel's .env):
```ini
    DB_CONNECTION=mysql
    DB_HOST=mysql
    DB_PORT=3306
    DB_DATABASE=your_schema_name
    DB_USERNAME=root
    DB_PASSWORD=root
```
