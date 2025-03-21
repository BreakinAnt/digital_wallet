# 🚀 Project Setup

## 📌 Prerequisites

This project uses:
- **Laravel** 11.31  
- **PHP** 8.2  

## ⚙️ Running the Project

1. Install Composer dependencies:  
```sh
   composer install
```
2. Copy the .env.example file to `.env`:
```
   cp .env.example .env
```
3. Configure the environment variables in the .env file, making sure to set up the database.
   
4. Run the database migrations and seeders:
```
    php artisan migrate
    php artisan db:seed
```
5. Start the local server:
```
   php artisan serve
```
6. Run automated tests:
```
    php artisan test
```

## 🐳 Running the Project via Laradock
1. In the terminal, navigate to the Laradock folder inside the project.
```
    cd Laradock
```
2. Run the following command:
```
    docker-compose up -d nginx mysql workspace redis
```
3. By default, the database connection should be configured as follows:
```ini
    DB_CONNECTION=mysql
    DB_HOST=mysql
    DB_PORT=3306
    DB_DATABASE=your_schema_name
    DB_USERNAME=root
    DB_PASSWORD=root
```
