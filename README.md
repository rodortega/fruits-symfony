# Fruits - Symfony

This is a sample coding task that will demonstrate the use of Symfony and jQuery.




## Installation

After cloning the repository, open a terminal and navigate to the projects root directory. Install the following dependencies:

```bash
  composer install
```
Open .env file in the project's root directory and modify the DATABASE_URL and MAILER_DSN.

If you haven't created a blank database yet, you can create one in the terminal:
```bash
php bin/console doctrine:database:create
```
This will create a new database based on the DATABASE_URL credentials on your .env file.


If you're using Gmail, you may follow these steps to generate your app password: 🔗 https://support.google.com/accounts/answer/185833

## Fetching Data

It fetches data from https://www.fruityvice.com and saves it in the database. To do so, run the following to create a database, fetch it and save it automatically.
```bash
  php bin/console doctrine:migrations:migrate
  php bin/console fruits:fetch
```
## Running the Application

You can run the symfony application by typing the following in the console:
```bash
  php -S localhost:8000 -t public
```
You can now use your application by navigating to https://localhost:8000 in your browser.