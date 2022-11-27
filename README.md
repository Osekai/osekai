# Setup
Clone the folder locally. Create a database using MySQL or MariaDB and import the template.sql. Put info into config.php.
Make oauth app on osu! and put in data to config.php, along with api v1 credentials.
Run `php -S localhost:8080` in the folder, then access at http://localhost:8080

## Snapshots & Admin Panel

For Snapshots and Admin Panel to work, you must install the PHP `gd` Extension:
- `sudo apt install php-gd` / `sudo pacman -S php-gd` and add to php.ini

Any issues contact me (hubz)

## Osekai Tools

Run `git submodule update --init --recursive` to pull all the tool source code, as they're in separate repositories.