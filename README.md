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
3. Configure the environment variables in the .env file, making sure to set up the database.
   
4. Run the database migrations and seeders:
<br>*Laradock will create a MySQL container. If you're not using Laradock, start the database using your preferred method.*
```
    php artisan migrate
    php artisan db:seed
```

5. __*__ Start the local server:
```
   php artisan serve
```
6. Run automated tests:
```
    php artisan test
```

*__It's recommended to run this project using Laradock.__*

## 🐳 Running the Project via Laradock (Recommended)
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
