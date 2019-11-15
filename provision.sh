#!/usr/bin/env bash
apt-get update

#install any binaries for compiling from source
apt-get install -y build-essential

#install apache utilities
apt-get install -y apache2-utils

#install git-scm
apt-get install -y git

#install utilities
apt-get install -y unzip

#install nginx and php-fpm
apt-get install -y nginx php-fpm

#install php
apt-get install -y php php-mysql php-mcrypt php-curl php-cli php-gd php7.0-mbstring php7.0-dom php7.0-bcmath php-imagick php-zip

apt-get install -y memcached

apt-get install -y php-memcached

sudo timedatectl set-timezone America/New_York

#configuration for silently creating the root password
echo mysql-server mysql-server/root_password select "root" | debconf-set-selections
echo mysql-server mysql-server/root_password_again select "root" | debconf-set-selections

#install mysql
apt-get install -y mysql-server

#restart mysql
service mysql restart

#install node
curl -sL https://deb.nodesource.com/setup_8.x | sudo -E bash -
apt-get install -y nodejs



#install composer
curl -sS https://getcomposer.org/installer | php --
mv composer.phar /usr/bin/composer

cd /var/www

composer install

cp .env.example .env

php artisan key:generate

mysql -uroot -proot -e "create database momentum"

# migrate for the platform site
php artisan migrate

php artisan db:seed

php artisan passport:install

mkdir /etc/nginx/ssl

sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/nginx/ssl/nginx.key -out /etc/nginx/ssl/nginx.crt -subj "/C=US/ST=ME/L=Portsmouth/O=ATOM/CN=mrg.dev/emailAddress=admin@theatomgroup.com/"


cat << EOF > /etc/nginx/sites-available/000-default
server {
        listen 80 default_server;
        listen [::]:80 default_server;



        root /var/www/public;

        index index.php;

        sendfile off;

        server_name _;

        if (\$http_x_forwarded_proto = "http") {
                rewrite        ^ https://\$server_name\$request_uri? permanent;
        }

        location /robots.txt {
                add_header Content-Type text/plain;
                return 200 "User-agent: *\nDisallow: /\n";
        }

        location / {
                try_files \$uri \$uri/ /index.php?\$query_string;
        }

        location ~ \.php\$ {
                include snippets/fastcgi-php.conf;
                fastcgi_pass unix:/run/php/php7.0-fpm.sock;
        }

}
EOF

ln -s /etc/nginx/sites-available/000-default /etc/nginx/sites-enabled/000-default

cat << EOF > /etc/nginx/sites-available/mrg.dev.conf
server {
        listen 80;
        listen 443;

        server_name mrg.dev;

        ssl    on;
        ssl_certificate    /etc/nginx/ssl/nginx.crt;
        ssl_certificate_key    /etc/nginx/ssl/nginx.key;

        error_log /var/log/nginx/error.log;
        access_log /var/log/nginx/access.log;

        root /var/www/public;

        index index.php;

        sendfile off;


        if (\$http_x_forwarded_proto = "http") {
                rewrite        ^ https://\$server_name\$request_uri? permanent;
        }

        location /robots.txt {
                add_header Content-Type text/plain;
                return 200 "User-agent: *\nDisallow: /\n";
        }

        location / {
                try_files \$uri \$uri/ /index.php?\$query_string;
        }

        location ~ \.php\$ {
                include snippets/fastcgi-php.conf;
                fastcgi_pass unix:/run/php/php7.0-fpm.sock;
        }

}
EOF

ln -s /etc/nginx/sites-available/mrg.dev.conf /etc/nginx/sites-enabled/mrg.dev.conf


if [ -f /etc/nginx/sites-enabled/default ] ; then
    rm /etc/nginx/sites-enabled/default
fi

#update the max filesize upload to 100M
sed -ie 's/upload_max_filesize = 2M/upload_max_filesize = 100M/g' /etc/php/7.0/fpm/php.ini
sed -ie 's/post_max_size = 8M/post_max_size = 100M/g' /etc/php/7.0/fpm/php.ini
sed -i '/http {/a client_max_body_size 100m;' /etc/nginx/nginx.conf

#set the max execution time for database backups
sed -ie 's/max_execution_time .*/max_execution_time = 600/g' /etc/php/7.0/fpm/php.ini
sed -i '/http {/a fastcgi_read_timeout 600s;' /etc/nginx/nginx.conf

service nginx restart
service php7.0-fpm restart

grep "cd /var/www" /home/vagrant/.bashrc || printf "cd /var/www\n" >> /home/vagrant/.bashrc

#headless chrome Dependencies and puppeteer
apt install  gconf-service libasound2 libatk1.0-0 libc6 libcairo2 libcups2 libdbus-1-3 libexpat1 libfontconfig1 libgcc1 libgconf-2-4 libgdk-pixbuf2.0-0 libglib2.0-0 libgtk-3-0 libnspr4 libpango-1.0-0 libpangocairo-1.0-0 libstdc++6 libx11-6 libx11-xcb1 libxcb1 libxcomposite1 libxcursor1 libxdamage1 libxext6 libxfixes3 libxi6 libxrandr2 libxrender1 libxss1 libxtst6 ca-certificates fonts-liberation libappindicator1 libnss3 lsb-release xdg-utils wget -y
cp -r /var/www/url-2-pdf ~/url-2-pdf
pushd ~/url-2-pdf
npm install
chown -R www-data:www-data ~/url-2-pdf
popd


# Locales
sudo locale-gen en_US.UTF-8
sudo locale-gen da_DK.UTF-8
sudo locale-gen de_DE.UTF-8
sudo locale-gen es_ES.UTF-8
sudo locale-gen fr_FR.UTF-8
sudo locale-gen it_IT.UTF-8
sudo locale-gen nl_NL.UTF-8
sudo locale-gen pt_BR.UTF-8
sudo locale-gen sv_SE.UTF-8
sudo locale-gen zh_CN.UTF-8
sudo locale-gen en_GB.UTF-8
sudo locale-gen pl_PL.UTF-8
sudo locale-gen ja_JP.UTF-8
sudo locale-gen fi_FI.UTF-8
sudo locale-gen cs_CZ.UTF-8
sudo locale-gen nb_NO.UTF-8
sudo locale-gen ko_KR.UTF-8
sudo locale-gen ar_SA.UTF-8
sudo locale-gen zh_TW.UTF-8
sudo update-locale
