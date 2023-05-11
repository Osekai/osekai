![](https://raw.githubusercontent.com/Osekai/osekai/main/global/img/cover/medals.jpg)
# *Osekai*
Hey there, welcome to the official Osekai repository! We're an open-source website specializing in apps and tools for the rhythm game "[osu!](https://osu.ppy.sh/)"

If you're interested in helping out with development, read on! Else, if you just want to use the website, [check it out here](https://osekai.net)!


# Setup
Clone the folder locally. Create a database using MySQL or MariaDB and import the template.sql. Put info into config.php.

Make oauth app on osu! and put in data to config.php, along with api v1 credentials.

## **Running**

### Local Php Server

Run `php -S localhost:8080` in the folder, then access at http://localhost:8080

### Apache Setup

An Apache/httpd setup is required to run Osekai completely. Without it, certain apps such as Teams, Tools, and more will not function, and pages such as the 404 page will not show up. If you are planning to work on osekai alot, using apache is recommended.

**The easiest way to do this is to install XAMPP, and then change the directory in the httpd/apache config to point to your Osekai clone.**

If you feel like installing it manually, though, start by setting up apache like you usually would, you can follow a plethora of online tutorials for this. After you're done, just make sure it's using php8.1 and is pointing to the Osekai folder.

You can use [this script](https://pastebin.com/3XGT5HwE) I've made to quickly switch between folders, keep in mind this'll only work on Arch Linux, so you'll need to edit the script for other distros. - `sudo switch /home/tanza/Documents/orgs/osekai/osekai` 

## **Database Setup**
Create a new user, call it whatever, whatever password, just remember it.

Make a new database called `osekai`, you can name it whatever, but osekai is recommended. It's recommended to make it using **utf8mb4_bin**

Run all the files in `db/` on this table, you can do this by just copying the content into a query tab and running it.

At that point you just need to fill in each field in config.php, and it should work.

## **Snapshots & Admin Panel**

For Snapshots and Admin Panel to work, you must install the PHP `gd` Extension:
- `sudo apt install php-gd` / `sudo pacman -S php-gd` and add to php.ini

On any issues - please contact me! (Hubz/Tanza)

## **Windows**

On Windows, SSL will not function with cURL, and Login will not work. To fix, please download [cacert.pem](https://curl.se/docs/caextract.html) and place it in a folder. Then in your `php.ini` under `[curl]` add `curl.cainfo = "C:\php\cacert.pem"` and under `[openssl]` add `openssl.cafile = "C:\php\cacert.pem"`. Should look like so:

![image](https://user-images.githubusercontent.com/33783503/204513078-8edb42f0-94db-4a8f-9a79-ade9996c7303.png)

You should now be able to login.
