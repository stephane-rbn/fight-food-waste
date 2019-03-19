# Fight Food Waste

## Setup

- Create your MySQL database (with Adminer or phpMyAdmin)
- Run the SQL code located in `schema_build.sql`
- Create a file `.env` at the root of the project based on the `.env.example` file
- Install Composer and then run: `composer install` in your terminal
- Run `composer dump-autoload`
- Enable URL rewriting by uncommenting this line in your httpd.conf file. My setup:

```sh
# /Applications/MAMP/conf/Apache/httpd.conf
LoadModule rewrite_module modules/mod_rewrite.so
```

- Configure the web server by changing its root to the `/public` folder. My setup:

```sh
# /etc/hosts
127.0.0.1    fight-food-waste.lan
```

```sh
# /Applications/MAMP/conf/apache/extra/httpd-vhosts.conf
<VirtualHost *:80>
    DocumentRoot "/Applications/MAMP/htdocs/fight-food-waste/public"
    ServerName fight-food-waste.lan
</VirtualHost>
```
