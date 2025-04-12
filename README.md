# CLASS ATTENDANCE SYSTEM

This a refined class attendance system where checkin are made through facial recognition.

## Project Setup
Clone the repository
```sh
git clone https://github.com/Denis-Wanjohi/CAS.git
```
Split the terminal or have two terminals opened.
On the first terminal run 
```sh
npm install
```
After spining up the packages run
```sh
npm run dev
```
Now to the second terminal run, change directory first
```sh
cd api
```
Install Composer Dependencies
```sh
composer install
```
Copy the Environment File
```sh
cp .env.example .env
```
Generate the Application Key
```sh
php artisan key:generate
```
Configure the Database
```sh
DB_CONNECTION=mysql      # Or pgsql, sqlite, mysql,etc.
DB_HOST=127.0.0.1        # Or your database host
DB_PORT=3306             # Or your database port
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
```
Run the migrations
```sh
php artisan migrate:fresh --seed
```
Serve the project 
```sh
php artisan serve
```
Go to your browser and access the project through port 5173(http://localhost:5173/). use 'test@example.com' as the email to login as admin and password as '00000000'.

