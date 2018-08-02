---
extends: _layouts.master
section: content
title: Debian Installation Guide
---

@include('_partials.work_in_progress')

We know setting up servers can be hard and frustrating. Hopefully, this guide helps you step by step along the way.
We assume your server is running **Debian Stretch** and does not have any of the necessary tools installed.
If you have a working PHP 7.2 webserver setup, please jump to the [Installing MUM](#installing-mum) section.

## Prerequisites

Make sure your system is up-to-date and you have installed the following packages:

@component('_components.code')
@slot('lang', 'bash')
sudo apt-get update && sudo apt-get upgrade
sudo apt-get install unzip curl wget acl git
@endcomponent


## Installing PHP 7.2

Since Debian Stretch only ships with PHP 7.0 (as of 2018), we have to install a new package repository that allows
us to download newer versions of PHP. The following commands should be executed with root privileges
(use `sudo` or login with user `root`).

@component('_components.code')
@slot('lang', 'bash')
sudo apt-get -y install apt-transport-https lsb-release ca-certificates
sudo wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
sudo sh -c 'echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list'
sudo apt-get update
@endcomponent

The next step is to actually install PHP and all necessary extensions.
The following commands should be executed with root privileges (use `sudo` or login with user `root`).

@component('_components.code')
@slot('lang', 'bash')
sudo apt-get install php7.2 php7.2-mbstring php7.2-xml php7.2-zip \
php7.2-cli php7.2-common php7.2-curl php7.2-json php7.2-mysql php7.2-opcache
@endcomponent

Now, when you execute `php -v` you should see something like

@component('_components.code')
PHP 7.2.8-1+0~20180725124257.2+stretch~1.gbp571e56 (cli) (built: Jul 25 2018 12:43:00) ( NTS )
Copyright (c) 1997-2018 The PHP Group
Zend Engine v3.2.0, Copyright (c) 1998-2018 Zend Technologies
    with Zend OPcache v7.2.8-1+0~20180725124257.2+stretch~1.gbp571e56, Copyright (c) 1999-2018, by Zend Technologies
@endcomponent

## Installing MySQL / MariaDB

Firstly, we need to install a database server. We are going to use MariaDB here.

@component('_components.code')
@slot('lang', 'bash')
sudo apt-get install mariadb-server
sudo mysql -u root
@endcomponent

The last command opened a MySQL shell. Now, we need to create the database and several users.
Please choose **secure** passwords for your users. [Diceware](https://www.eff.org/dice) is one way of
generating secure passwords that are easily typeable and memorable.

@component('_components.code')
@slot('lang', 'sql')
CREATE USER mum@localhost IDENTIFIED BY 'my_secure_password_1';
CREATE USER mum_postfix@localhost IDENTIFIED BY 'my_secure_password_2';
CREATE USER mum_dovecot@localhost IDENTIFIED BY 'my_secure_password_3';
CREATE DATABASE mum;
GRANT ALL PRIVILEGES ON mum.* TO mum@localhost;
GRANT SELECT ON mum.* TO mum_postfix@localhost;
GRANT SELECT ON mum.* TO mum_dovecot@localhost;
@endcomponent

To exit the MySQL shell, simply type `exit;`.


## Configuring Apache2

When installing the `php7.2` package, the `apache2` should have automatically been installed as well.
To check if Apache2 is running, execute `sudo service apache2 status`.
If the service is running, you can continue with your Apache2 configuration. If the service could not be found,
install it with `sudo apt-get install apache2`.

### Enabling Modules

The first thing we need to do is to enable a few Apache2 modules that we need. The module `ssl` is needed for HTTPS,
`headers` is needed to configure [HSTS](https://en.wikipedia.org/wiki/HTTP_Strict_Transport_Security) on our
virtual host and `rewrite` is needed to redirect clients from HTTP to HTTPS, so that all traffic will be encrypted.

@component('_components.code')
@slot('lang', 'bash')
sudo a2enmod ssl headers rewrite php7.2
sudo service apache2 restart
@endcomponent

### SSL/TLS Configuration

We want MUM to (only) be available via HTTPS so that any traffic between your browser and MUM is encrypted.
If your server is accessible from the internet, the easiest (and a free) way to get an SSL certificate is
to use [Let's Encrypt](https://letsencrypt.org/). Follow the [instructions for EFF's Certbot](https://certbot.eff.org/)
on how to obtain a free SSL certificate through Let's Encrypt. From here on out we are going to assume that
you are using a Let's Encrypt SSL certificate. If you choose to work with a different certificate, please
change your file paths in the following configuration files accordingly.

### Virtual Host Configuration

The first step is to create a configuration file for the domain that should serve MUM's web interface.
We are going to use `mum.example.com` here, please change that name to your domain.

@component('_components.code')
@slot('lang', 'bash')
sudo nano /etc/apache2/sites-available/mum.example.com.conf
@endcomponent

Inside this configuration file we need to specify a virtual host that listens on port 443.
`ServerName` is the domain where MUM's web interface will be served. `DocumentRoot` is the path to MUM's
`public` folder. We are going to clone MUM into `/var/www/`, but you could use any path you want.
If you don't use Certbot with Let's Encrypt certificates, you will probably need to adjust the paths for
`SSLCertificateFile` and `SSLCertificateKeyFile`.

@component('_components.code_file')
@slot('filename', '/etc/apache2/sites-available/mum.example.com.conf')
@slot('lang', 'apacheconf')
<VirtualHost *:443>
    ServerName mum.example.com
    DocumentRoot /var/www/mum/public
    <Directory /var/www/mum/public>
        Options -Indexes +FollowSymLinks +MultiViews
        AllowOverride All
        Require all granted
    </Directory>
    SSLCertificateFile /etc/letsencrypt/live/mum.example.com/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/mum.example.com/privkey.pem

    SSLEngine on
    SSLProtocol All -SSLv2 -SSLv3
    SSLHonorCipherOrder On
    SSLCompression off
    Header always set Strict-Transport-Security "max-age=15768000"
    SSLCipherSuite 'EDH+CAMELLIA:EDH+aRSA:EECDH+aRSA+AESGCM:EECDH+aRSA+SHA256:EECDH:+CAMELLIA128:+AES128:+SSLv3:!aNULL:!eNULL:!LOW:!3DES:!MD5:!EXP:!PSK:!DSS:!RC4:!SEED:!IDEA:!ECDSA:kEDH:CAMELLIA128-SHA:AES128-SHA'
</VirtualHost>
@endcomponent

The last step is to redirect all clients that use HTTP on port 80 to HTTPS on port 443.
To do that, you could either add a second `VirtualHost` definition in your configuration file or create a second
file that only holds your HTTP host(s). If you want to redirect HTTP traffic for all your virtual hosts,
leave out the `ServerName` directive.

@component('_components.code_file')
@slot('filename', '/etc/apache2/sites-available/http-mum.example.com.conf')
@slot('lang', 'apacheconf')
<VirtualHost *:80>
    ServerName mum.example.com
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^ https://%{SERVER_NAME}%{REQUEST_URI} [END,QSA,R=permanent]
</VirtualHost>
@endcomponent

## Installing Composer

The next thing we need is the PHP dependency manager [Composer](https://getcomposer.org/).
To install Composer, please follow the [instructions on how to install the latest version](https://getcomposer.org/download/).
If you want to install Composer globally, execute the following line after you executed the installer script:

@component('_components.code')
@slot('lang', 'bash')
sudo mv composer.phar /usr/local/bin/composer
@endcomponent

## Installing MUM

Now on to the important part: actually installing MUM. The first thing you'll want to do is clone the repository
to your local machine, we are going to clone it into `/var/www/mum/`, but you could use any folder you want.
Just make sure you adjust your Apache2 configuration accordingly.

After cloning the repository, we will install all PHP dependencies. Then, we will create a configuration file
for your local settings called `.env`. Refer to our [configuration options]({{ $page->baseUrl }}/configuration-options)
for more information on this file. Next, we will generate an encryption key for MUM to use.

@component('_components.code')
@slot('lang', 'bash')
cd /var/www/
sudo mkdir mum && sudo chown user:user mum
git clone https://github.com/mum-project/mum.git
cd mum
composer install
cp .env.example .env
php artisan key:generate
@endcomponent

After these steps, your `mum` folder and it's permissions should look something like this:

@component('_components.code')
@slot('lang', 'bash')
user@debian:/var/www/mum$ ls -la
...
-rw-r--r--  1 user user   1965 Jul 31 12:38 .env
...
drwxr-xr-x  5 user user   4096 Jul 31 12:38 storage
...
@endcomponent

Finally, we need to give Apache2's `www-data` user permissions to read, write and execute for the `storage` folder
and all files within. Since we installed the `acl` package earlier, we can use `setfacl` to set default permissions
for the user `www-data` and our own user `user`. Be sure to use your actual user name here.

@component('_components.code')
@slot('lang', 'bash')
sudo setfacl -R -m u:www-data:rwx storage/
sudo setfacl -R -m u:user:rwx storage/
sudo setfacl -d -R -m u:www-data:rwx storage/
sudo setfacl -d -R -m u:user:rwx storage/
@endcomponent

The command `getfacl` now should output something like:

@component('_components.code')
@slot('lang', 'bash')
user@debian:/var/www/mum$ getfacl storage/
...
user:www-data:rwx
user:user:rwx
...
default:user::rwx
default:user:www-data:rwx
default:user:user:rwx
...
@endcomponent

## Configuring MUM

The last step is to configure MUM's `.env` file to suit your environment. Please see our
[page on configuration options]({{ $page->baseUrl }}/configuration-options) for details.
One thing you surely need to configure are the mysql credentials. Be sure to use the actual credentials
you used when creating the database users in the [MySQL section](#installing-mysql-mariadb).

@component('_components.code_file')
@slot('filename', '/var/www/mum/.env')
@slot('lang', 'text')
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mum
DB_USERNAME=mum
DB_PASSWORD=my_secret_password_1
@endcomponent

## Creating Your First User

Since MUM does not have a web installer yet, you will need to use console commands to create your first user in MUM.
Use the following commands to create a domain and a super admin mailbox:

@component('_components.code')
@lang('bash')
php artisan domains:create example.com
php artisan mailboxes:create admin example.com --super_admin --name='Super Admin'
@endcomponent

For a full list of command options, use the `--help` flag with the commands:

@component('_components.code')
@lang('bash')
$ php artisan domains:create --help
Usage:
  domains:create [options] [--] <domain>

Arguments:
  domain                               Domain to create, eg. example.com

Options:
      --description[=DESCRIPTION]
      --quota[=QUOTA]
      --max_quota[=MAX_QUOTA]
      --max_aliases[=MAX_ALIASES]
      --max_mailboxes[=MAX_MAILBOXES]
  -h, --help                           Display this help message
  -q, --quiet                          Do not output any message
  -V, --version                        Display this application version
      --ansi                           Force ANSI output
      --no-ansi                        Disable ANSI output
  -n, --no-interaction                 Do not ask any interactive question
      --env[=ENV]                      The environment the command should run under
  -v|vv|vvv, --verbose                 Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Help:
  Create a new domain. ATTENTION: This command currently does not perform ANY validation of supplied data.
@endcomponent

@component('_components.code')
@lang('bash')
$ php artisan mailboxes:create --help
Usage:
  mailboxes:create [options] [--] <local_part> <domain>

Arguments:
  local_part                                   The local part of the email address
  domain                                       An existing domain in MUM

Options:
      --password[=PASSWORD]                    You are asked for a password if you don't supply this option. Be aware that the value of the password option may be recorded in your shell history.
      --name[=NAME]                            The name of the person that uses the mailbox
      --alternative_email[=ALTERNATIVE_EMAIL]  An alternative address that may be used to reset the password
      --quota[=QUOTA]                          Maximum disk space for emails in GB
      --send_only                              Whether the mailbox should be able to receive emails, defaults to false
      --inactive                               Whether the mailbox should be inactive, defaults to false
      --super_admin                            Whether the mailbox should be a super admin, defaults to false
  -h, --help                                   Display this help message
  -q, --quiet                                  Do not output any message
  -V, --version                                Display this application version
      --ansi                                   Force ANSI output
      --no-ansi                                Disable ANSI output
  -n, --no-interaction                         Do not ask any interactive question
      --env[=ENV]                              The environment the command should run under
  -v|vv|vvv, --verbose                         Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Help:
  Create a new mailbox. ATTENTION: This command currently does not perform ANY validation of supplied data.
@endcomponent

## Troubleshooting

Whether you see an error message or nothing at all - it is always a good idea to have a look in your log files.
We hope the following tips will help you to solve your problem. If you have a new tip, we'd love to hear from you!

#### Invalid Cache Path

The Apache2 error logs show a PHP error along the lines of:

@component('_components.code_file')
@slot('filename', '/var/www/apache2/error.log')
@slot('lang', 'text')
... Uncaught InvalidArgumentException: Please provide a valid cache path. ...
@endcomponent

Sometimes it helps to clear all caches with `php artisan cache:clear && php artisan config:clear && php artisan view:clear`.
If the error keeps happening, you probably haven't set the `storage` folder permissions correctly.

---

If you think you have spotted an error in this guide, please check if other people spotted it too by looking at
the [GitHub issues in our docs repository](https://github.com/mum-project/docs/issues) first. If you are first,
we would highly appreciate if you told us by opening a new issue or (even better) a pull request.
