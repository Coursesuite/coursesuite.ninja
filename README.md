# CourseSuite

CourseSuite was retired at the beginning of 2020. The server and back end was turned off. The code for the site is now open source.

## Setup

To make this project run:

* make sure you have php 5.6 or later
* point your webroot at the ./public/ folder.
* make ./public/avatars writable by the webserver user
* make ./public/img/apps writable by the webserver user too
* make ./precompiled writable by the webserver user
* edit ./application/config/config.development.php. This uses the apache global environment variable so you may need to rename the file
    1. install the database (a mysqldump is in the db/ folder)
    2. create a secure user for the db basic permissions
    3. put db params into the config file
    4. create very random keys for the HMAC and encryption key
    5. check the google captcha secret & other config settings
* hit the site in your browser

If this fails, it will either be your database or config values
