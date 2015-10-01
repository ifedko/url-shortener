# Url Shortener

## Description
https://www.wikiwand.com/en/URL_shortening

## System requirements
  - Vagrant 1.7.4 (https://www.vagrantup.com/)
  - Apache 2
  - PHP 5.5.9
  - MySQL 5.5
  
## Installation

### Use vagrant
 - Install vagrant. Download from official site https://www.vagrantup.com/ and follow instructions.
 - Create directory for project
```sh
$ mkdir url-shortener
$ cd ~/url-shortener/
```
 - Clone project from: 
```sh
$ git clone https://github.com/ifedko/url-shortener.git .
```
 - Run vagrant
```sh
$ vagrant up
```
 - Open hosts file:
```sh
$ sudo nano /etc/hosts
```
 - and add next rule:
```
127.0.0.1 url-shortener.local
```
 - Open http://url-shortener.local:8081 in your browser (Vagrant uses port 8081 for host machine)

### Manual

 - Create directory for project
 ```sh
 $ mkdir url-shortener
 $ cd ~/url-shortener/
 ```
  - Clone project from:
 ```sh
 $ git clone https://github.com/ifedko/url-shortener.git .
 ```
 - Set AllowOverride to All and DocumentRoot in Apache config
 - Create database and set access data and base url (for example, http://localhost) in application configuration (see below)
 - Change javascript parameter "requestUri" to appropriate uri in
 ```sh
 /path/to/project/public/app/src/View/UrlShortener/index.php
 ```

 For example:
 ```
 requestUri: "http://localhost/short_url"
 ```
 - Run project in browser: http://localhost

#### Project configuration

Path to application configuration (database and application parameters):
```
/path/to/project/app/config/config.ini
```

Sql for creating database:
```
/path/to/project/app/migrations/create_db.sql
```

### Todos

 - Write Tests
 - Add support of cyrillic domain names
 - And other small tasks

