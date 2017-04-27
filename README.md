# Link Shrinker

This application is similar to bit.ly where any user can submit a url, and it will give them back a shortened url that  automatically redirects, to the original url. 

This version was written in PHP using FlightPHP on the back-end and Javascript/jQuery on the front-end. The design comes from a free bootstrap theme, called '[cyborg](https://bootswatch.com/cyborg/)', to give it a standard look-and-feel. Each url first passes through a [phishtank](https://www.phishtank.com/) blacklist, to prevent known malicious urls from getting into the system. 

## System Requirements
 * [Vagrant](https://www.vagrantup.com/)
 * [VirtualBox](https://www.virtualbox.org/wiki/Downloads)
 * [Git](https://git-scm.com/)

## Setup
```
git clone git@github.com:mathew-fleisch/Link-Shrinker.git
cd ./Link-Shrinker
vagrant up
```
[http://localhost:8088](http://localhost:8088)

## Troubleshooting
Sometimes the provisioning file doesn't run and causes errors in initial setup. Generally this manifests by urls not resolving. For instance, if you can render http://localhost:8088/ but cannot render http://localhost:8088/admin the [provision.sh](https://github.com/mathew-fleisch/Link-Shrinker/blob/master/scripts/provision.sh#L14) script failed.
First try running this from the host computer:
```
vagrant provision
```
If that errors out, or it still doesn't work, try running the commands found in that script:
```
# Workaround to enable htaccess file 
sed -i '164,168s/AllowOverride\ None/AllowOverride All/g' /etc/apache2/apache2.conf
service apache2 restart

# Initialize Database
mysql -uroot < /var/www/data/init.sql


# Setup PhishTank service
/var/www/data/phishy.sh
if [[ $(grep -R "phishy" /var/spool/cron/crontabs) ]]; then
	echo "Crontab already saved..."
else 
	echo $(crontab -l ; echo '0 * * * * /var/www/data/phishy.sh') | crontab -
fi 
```


## API

``` 
/api/url - POST
	- This endpoint is passed a url, and an object containing the new alias is returned
	- @param string url
	- Sample Response
	{
		"success": true,
		"message": "Successful",
		"url": "http://google.com",
		"existing": "7",
		"alias": "wud8jb1"
	}



/api/url/@alias - GET
	- This endpoint will return the original url based on some alias/id
	- @param string alias/id
	- Sample Response
	{
		"url": "http://google.com"
	}



/api/phish/update - GET
	- This endpoint is used on the back-end only to update the phish-tank database hourly. Warning: this endpoint takes about 5 minutes to process.
	- @param N/A
	- Sample Response
	{
		"added": 15,
		"total": 26351
	}



/api/phish/url - POST
	- This endpoint will return a phish-tank id if the url passed was found in the blacklisted db
	- @param string url
	- Sample Response
	[
		"111532"
	]
```


### Benchmarks
```
$ ab -k -c 150 -n 1000 http://127.0.0.1/
This is ApacheBench, Version 2.3 <$Revision: 1528965 $>
Copyright 1996 Adam Twiss, Zeus Technology Ltd, http://www.zeustech.net/
Licensed to The Apache Software Foundation, http://www.apache.org/

Benchmarking 127.0.0.1 (be patient)
Completed 100 requests
Completed 200 requests
Completed 300 requests
Completed 400 requests
Completed 500 requests
Completed 600 requests
Completed 700 requests
Completed 800 requests
Completed 900 requests
Completed 1000 requests
Finished 1000 requests


Server Software:        Apache/2.4.7
Server Hostname:        127.0.0.1
Server Port:            80

Document Path:          /
Document Length:        1755 bytes

Concurrency Level:      150
Time taken for tests:   7.596 seconds
Complete requests:      1000
Failed requests:        0
Keep-Alive requests:    0
Total transferred:      1946000 bytes
HTML transferred:       1755000 bytes
Requests per second:    131.64 [#/sec] (mean)
Time per request:       1139.463 [ms] (mean)
Time per request:       7.596 [ms] (mean, across all concurrent requests)
Transfer rate:          250.17 [Kbytes/sec] received

Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:        0   12 104.1      0    1000
Processing:    78 1066 339.5   1097    4404
Waiting:       77 1062 338.3   1094    4401
Total:         85 1077 355.0   1100    4409

Percentage of the requests served within a certain time (ms)
  50%   1100
  66%   1159
  75%   1205
  80%   1228
  90%   1272
  95%   1389
  98%   1770
  99%   2094
 100%   4409 (longest request)
```
