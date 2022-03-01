## Joseph Nyahuye
### OnlineMarketPlace

``A simple online market place to buy, list and sell your own goods or services. It is built on a stack using Laravel 8, Boostrap 4, Jquery 3.6. It also uses the Stripe intergration API to do online payments, and for email uses Elastic email service to notify customers, all in a test environment.``


#### Git clone project

```
git clone https://github.com/hotslab/ShoppingMarketPlace.git 

OR 

git clone git@github.com:hotslab/ShoppingMarketPlace.git

cd ShoppingMarketPlace

```

#### Install packages

```
composer install
npm install

```

#### Install SQL Lite and run migrations

```
sudo apt update
sudo apt install sqlite3
sudo apt-get install php-sqlite3

touch database/database.sqlite
touch database/testdatabase.sqlite

php artisan migrate
php artisan db:seed --optional for testing

```