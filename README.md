# Setup
Clone the folder locally. Create a database using MySQL or MariaDB and import the template.sql. Put info into config.php.

Make oauth app on osu! and put in data to config.php, along with api v1 credentials.

Run `php -S localhost:8080` in the folder, then access at http://localhost:8080


## Database Setup
Create a new user, call it whatever, whatever password, just remember it.

Make a new database called `osekai`, you can name it whatever, but osekai is recommended. It's recommended to make it using utf8mb4_polish_ci

Run the `template.sql` file on this table, you can do this by just copying the content into a query tab and running it.

At that point you just need to fill in each field in config.php, and it should work.

## Snapshots & Admin Panel

For Snapshots and Admin Panel to work, you must install the PHP `gd` Extension:
- `sudo apt install php-gd` / `sudo pacman -S php-gd` and add to php.ini

Any issues contact me (hubz)

## Osekai Tools

Run `git submodule update --init --recursive` to pull all the tool source code, as they're in separate repositories.
