#!/bin/bash
cd /var/www/data
rm -rf online-valid.json.bz2;
rm -rf online-valid.json;
wget http://data.phishtank.com/data/online-valid.json.bz2
bzip2 -d online-valid.json.bz2
/usr/bin/php /var/www/data/phishy.php