
## **Tasks**

## **Prerequisites**
You should have **`composer`** installed. If you don't install composer from here.

## **Installation**

To install Job and freelancing portal project, follow these steps:

1. Clone the repository: **`git clone https://github.com/amalfathieh/Task`**
2. Navigate to the project directory: **`cd Task`**
3. Run this command to download composer packages:
    **composer install`**
4. Run this command to update composer packages:
    **`composer update`**
5. Create a copy of your .env file: **`cp .env.example .env`**
6. Generate an app encryption key: **`php artisan key:generate`**

7. Create an empty database for our application in your DBMS
8. In the .env file, add database information to allow Laravel to connect to the database
9. Migrate the database: **`php artisan migrate`**

10. Open up the server: **`php artisan serve`**
11. to run test :**`php artisan test --filter AuthTest`**
12. to run task test : **`php artisan test --filter TaskTest`** 
    
   
