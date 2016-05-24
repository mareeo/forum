# forum
A very basic forum I made for my friends to use.

## Code
I pieced this together years ago when I only had a slight clue what I was doing. I used [Slim 3.0](https://github.com/slimphp/Slim). My code isn't the greatest good. 

## Database
Database Schema isn't available yet.  Ask me if you want it.  I used [PHP ActiveRecord](https://github.com/jpfuentes2/php-activerecord) as my ORM.  There are obviously better choices.

## Setup
1. Clone the git repository in your Apache document folder.
2. Run `composer install` to pull down vendor libraries.
3. Run `schema.sql` on your MySQL server.
4. Update `config.php` to use the correct `WEB_BASE_DIR`, database credentials, and admin password.
