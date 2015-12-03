<a href="http://www.timegrid.io/">
    <img src="http://i.imgur.com/905Lv7L.png" alt="timegrid.io logo"
         title="timegrid.io" align="right" />
</a>

timegrid
a web application for online reservation of services for business.
============

[![Build Status](https://travis-ci.org/alariva/timegrid.svg?branch=development)](https://travis-ci.org/alariva/timegrid)
[![Code Climate](https://codeclimate.com/github/alariva/timegrid/badges/gpa.svg)](https://codeclimate.com/github/alariva/timegrid)
[![Test Coverage](https://codeclimate.com/github/alariva/timegrid/badges/coverage.svg)](https://codeclimate.com/github/alariva/timegrid/coverage)
[![Version Stage](https://img.shields.io/badge/dev--beta-3.5.0-orange.svg?style=flat-square)](http://demo.timegrid.io/)
[![ES User Manual](https://readthedocs.org/projects/manual-de-usuario-de-timegridio/badge/?version=latest&style=flat-square)](http://manual-de-usuario-de-timegridio.readthedocs.org/es/latest/?badge=latest)
[![License](https://img.shields.io/:license-AGPL--3.0-blue.svg?style=flat-square)](http://www.gnu.org/licenses/agpl-3.0.txt)

**The problem**

Small and medium scale businesses wish to provide online service booking for their customers, but the tools out there are either *too expensive* or *too complex* for their needs.

**The Solution**

Timegrid is a single platform holding all *businesses* and *customers* while letting them an *affordable* and *easy to use* way to interact. Useful, Simple, Fast.

It is made under the [**Laravel 5.1 (LTS)**](http://laravel.com/docs/5.1) framework for [**PHP**](http://php.net/).

## Screenshots

![Screenshot](http://i.imgur.com/aiG7jlx.png)

[Wanna see more?](https://github.com/alariva/timegrid/wiki/Screenshots)

## Live Demo

Want action? Try the *beta* [live demo](http://demo.timegrid.io/)

> Hold on tight! It's a bumpy ride for all of us!

## Features

### Implemented

  * User regular and oAuth2 SignUp
  * Business management
    * Contact addressbook
    * Services management
    * Availability management
  * Service Reservation
  * Local Search

### Future

  * Add SMS alerts
  * Add automatic vacancy handling
  * Support for Themes
  * Support for more industry sectors reservation strategies
  * Users reputation
  * ...and more

## Official Documentation

  * [![English Documentation Status](https://readthedocs.org/projects/timegrid-user-manual/badge/?version=latest)](http://manual-de-usuario-de-timegridio.readthedocs.org/en/latest/?badge=latest) English
  * [![Spanish Documentation Status](https://readthedocs.org/projects/manual-de-usuario-de-timegridio/badge/?version=latest)](http://manual-de-usuario-de-timegridio.readthedocs.org/es/latest/?badge=latest) Español

You are welcome to contribute.

-----
## How to install:

* [Step 1: Get the code](#step1)
* [Step 2: Use Composer to install dependencies](#step2)
* [Step 3: Create database](#step3)
* [Step 4: Install](#step4)
* [Step 5: Start Page](#step5)
* [Optional: Populate DB with a Demo Fixture](#demosandbox)

<a name="step1"></a>
### Step 1: Get the code - Clone the repository

    git clone https://github.com/alariva/timegrid.git
    
If you want to stand on the latest beta-stable version:

    cd timegrid

    git checkout tags/v3.4.2-beta

-----
<a name="step2"></a>
### Step 2: Use Composer to install dependencies

    composer install

-----
<a name="step3"></a>
### Step 3: Create database

If you finished first three steps, now you can create database on your database server(MySQL). You must create database
with utf-8 collation(uft8_general_ci), to install and application work perfectly.
After that, copy .env.example and rename it as .env and put connection and change default database connection name, only database connection, put name database, database username and password.

-----
<a name="step4"></a>
### Step 4: Configure environment

**Copy** the **.env.example** file to **.env**

    cp .env.example .env

Set the application key

    php artisan key:generate

**Edit** all the Primary section parameters (for *local/test/development environment*)

**Change** the storage path in **.env** file to a writeable location

    STORAGE_PATH=/home/username/timegrid/storage

For **local** environment you will need to comment out APP_DOMAIN, to keep it *null*

    #APP_DOMAIN=

Back to your console, migrate database schema

    php artisan migrate

Populate database:

    php artisan db:seed
    
Update [geoip](https://github.com/Torann/laravel-geoip) database:

    php artisan geoip:update

You should be ready to go, now run the server:

    php artisan serve

Type on web browser:

    http://localhost:8000/

-----
<a name="step5"></a>
### Step 5: Start Page

Congrats! You can now register as new user and log-in.

![timegrid Login Screen](http://i.imgur.com/jM8pbGq.png)

<a name="demosandbox"></a>
## Demo Sandbox Fixture

If you want to try the application with a *Lorem Ipsum* database fixture.

    php artisan db:seed --class=TestingDatabaseSeeder

Now you have two demo credentials to log in and play around.

    USER: demo@timegrid.io
    PASS: demomanager

    USER: guest@example.org
    PASS: demoguest

## Troubleshooting

I'd like to [hear your feedback](https://timegrid.slack.com/messages/general/).

Let me know if you are facing any inconvenients to install, as I'm working on bringing a more comprehensive installation guide assessing common problems.

## Contributing

Thank you for considering contributing to Timegrid.

Please see CONTRIBUTING doc for further details.
You are welcome to join the core development team and enhance the development process apart from just code :)

[Slack for technical discussion](https://timegrid.slack.com/home)

[Trello board for development roadmap](https://trello.com/b/VNFqnxhc/timegrid-io-dev)

[Dev Newsletter](http://eepurl.com/bF_ARX)

[![Join the chat at https://gitter.im/alariva/timegridDevelopment](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/alariva/timegridDevelopment?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

## Special Thanks

  * [PeGa!](http://ar.linkedin.com/in/pabloegonzalez) for infra support

### License

Timegrid is open-sourced software licensed under the [AGPL](http://www.gnu.org/licenses/agpl-3.0-standalone.html)
