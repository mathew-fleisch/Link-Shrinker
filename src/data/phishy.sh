#!/bin/bash
cd /var/www/data
rm -rf online-valid.json.bz2;
rm -rf online-valid.json;
wget http://data.phishtank.com/data/online-valid.json.bz2 >/dev/null 2>&1
bzip2 -d online-valid.json.bz2 >/dev/null 2>&1
/usr/bin/php /var/www/data/phishy.php
echo "This update process should take 3-5 minutes to complete... Please be patient."
wget http://localhost/api/phish/update