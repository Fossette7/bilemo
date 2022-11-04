# BileMo - Creez un web service exposant une API
API Rest project 7 -
Bilemo Company supplies to their customers a catalogue of mobile phone via an Rest API
## Technologies
 - PHP 8.0.8
 - Symfony 6.1
 - MySQL 5.7.34

 [![Codacy Badge](https://app.codacy.com/project/badge/Grade/b5ded4c9754a4ef9b8b97232525ae5fe)](https://www.codacy.com/gh/Fossette7/bilemo/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Fossette7/bilemo&amp;utm_campaign=Badge_Grade)
## Installation
Copy the link on GitHub and clone it on your local repository
https://github.com/Fossette7/bilemo

Clone the repository to your local path. Use command `git clone`

Inside your directory:  `cd my-project`

Open your **terminal** and **run**: `composer install`

Run the server : `symfony server:start`

Create my new API project : `composer create-project symfony/skeleton my_rest_api`

Create database with: `php bin/console doctrine:database:create`

Open file `.env` and write **username** and **password** for

> DATABASE_URL: `DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7.34&charset=utf8"`

Load the fixture with :  `php bin/console doctrine:fixtures:load`

## Use API

> API Documentation : http://www.bilemo.fr/api/doc