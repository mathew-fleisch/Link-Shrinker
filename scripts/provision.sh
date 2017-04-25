#!/bin/bash
#
# This script is executed upon the first `vagrant up` or when running
# `vagrant up --provision`. You may use it to install any additional software
# that you require for your submission.
#
#
set -e

apt-get -y update
apt-get -y upgrade
apt-get -y install apache2-utils

# Workaround to enable htaccess file 
sed -i '164,168s/AllowOverride\ None/AllowOverride All/g' /etc/apache2/apache2.conf
service apache2 restart

# Initialize Database
#mysql -uroot < /var/www/application/config/init.sql


# Setup PhishTank service
/var/www/data/phishy.sh
if [[ $(grep -R "phishy" /var/spool/cron/crontabs) ]]; then
	echo "Crontab already saved..."
else 
	echo $(crontab -l ; echo '0 * * * * /var/www/data/phishy.sh') | crontab -
fi 
