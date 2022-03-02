## Joseph Nyahuye
### OnlineMarketPlace

A simple online market place to buy, list and sell your own goods or services. It is built on a stack using Laravel 8, Boostrap 4 and Jquery 3.6. It also uses the Stripe API to do online payments, and for email uses the ElasticEmail service to notify customers, all under a simulated test environment.


#### Git clone project

```
git clone https://github.com/hotslab/OnlineMarketPlace.git 

OR 

git clone git@github.com:hotslab/OnlineMarketPlace.git

cd OnlineMarketPlace
```

#### Install packages and copy .env file

```
cp .env.example .env
composer install
npm install
npm run dev
```

#### Install SQL Lite and run migrations

```
sudo apt update
sudo apt install sqlite3
sudo apt install php-sqlite3

touch database/database.sqlite
touch database/testdatabase.sqlite

php artisan migrate
php artisan db:seed --optional for testing and will delete all the data in the sqlite database if run
```

#### Create symbolic link for image uploads

```
php artisan storage:link
```

#### Running the app

- Open three separate terminals and run the following to test the app.

```
php artisan serve -vvv
npm run watch
php artisan queue:work --tries=3 -vvv
```

- Open the url show in the terminal for the `php artisan serve -vvv` command to view the app.

- The test user credentials to login and view the app is:

```
Email - testemail@example.com
Password -testpassword1234
```

#### Unit testing

```
php artisan test -vvv
```