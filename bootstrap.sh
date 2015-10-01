#!/usr/bin/env bash

# Variables path
php_config_file="/etc/php5/apache2/php.ini"
xdebug_config_file="/etc/php5/mods-available/xdebug.ini"
mysql_config_file="/etc/mysql/my.cnf"
apache_config_file="/etc/apache2/apache2.conf"
host_document_root="/home/vagrant"
apache_document_root="/var/www/html"
apache_ports_config_file="/etc/apache2/ports.conf"
project_folder_name='public/www'

# Variables env
DBNAME=ifedko_shorturl
DBUSER=shorturl
DBPASSWD=shorturl
DBDUMPSQL="public/app/migrations/create_db.sql"

echo "--- Start installation ---"

echo "--- Update package list ---"
apt-get -qq update

echo "--- Install base packages ---"
apt-get -y install vim curl build-essential python-software-properties git

echo "--- Update base packages ---"
apt-get upd ate

echo "--- Install Apache2 ---"
apt-get install -y apache2

echo "--- Install PHP ---"
apt-get install -y php5 libapache2-mod-php5 php5-curl php5-gd php5-mcrypt php5-mysql php-apc

echo "--- Install and configure xDebug ---"
apt-get install -y php5-xdebug

cat << EOF | sudo tee -a ${xdebug_config_file}
xdebug.scream=1
xdebug.cli_color=1
xdebug.show_local_vars=1
EOF

echo "--- Turn on mod-rewrite ---"
a2enmod rewrite

echo "--- Set root dir ---"
sudo rm -rf ${apache_document_root}
sudo ln -fs ${host_document_root}/${project_folder_name} ${apache_document_root}

echo "--- Configure php.ini and apache2.conf ---"
sed -i "s/error_reporting = .*/error_reporting = E_ALL/" ${php_config_file}
sed -i "s/display_errors = .*/display_errors = On/" ${php_config_file}
sed -i "s/short_open_tag = .*/short_open_tag = On/" ${php_config_file}

apt-get -qq update

echo "--- Install MySql ---"
echo "mysql-server mysql-server/root_password password ${DBPASSWD}" | debconf-set-selections
echo "mysql-server mysql-server/root_password_again password ${DBPASSWD}" | debconf-set-selections
apt-get -y install mysql-server-5.5 mysql-client

mysql -uroot -p${DBPASSWD} -e "CREATE DATABASE ${DBNAME}"
mysql -uroot -p${DBPASSWD} -e "grant all privileges on $DBNAME.* to '${DBUSER}'@'localhost' identified by '${DBPASSWD}'"
mysql -uroot -p${DBPASSWD} ${DBNAME} < ${DBDUMPSQL}

echo "--- Install repositories ---"
add-apt-repository ppa:ondrej/php5

sudo sed -i 's/AllowOverride None/AllowOverride All/g' ${apache_config_file}

#a2enconf phpmyadmin

echo "--- Restart Apache2 ---"
service apache2 restart

echo "--- Restart mysql ---"
service mysql restart
