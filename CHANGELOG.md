# Ver 0.0.3
2015-06-13
* added some status indicators to the server home page (like travis-ci/status page)
* added some more CORS headers to get2.php

2015-06-11
* dropped all comment references to changing the root variable to development (was outdated since change to config.php in root folder)
* consolidated tests into phpunit test MiscTest.php
* also moved S3client tests to phpunit test

2015-06-02
* consolidated statistics in class and added weekly email
* sanity check on area and number in the get.php and get2.php api hooks
* refactored syncCore to use new function errorInLpns

2015-05-28
* added statistics on returning cars (i.e. anonymous users)

2015-05-22
* split out test user stuff into separate class
* added getpassword api hook for test user
* renamed deleteTestUser to testUserDelete

2015-05-21
* added deleteTestUser for the sake of automated testing of zboota-app

2015-05-19
* noticed that my travis-ci was very slow to re-build
 * read http://blog.travis-ci.com/2014-12-17-faster-builds-with-container-based-infrastructure/
 * learned that it may be because of my sudo: required
 * refactored include /etc/zboota...config.php to use a config file in the root folder
 * set sudo: false in travis yml file
* also, since I''m still getting the tls alert, I''m trying to add the ssh_known_hosts addon option to travis yml
 * http://docs.travis-ci.com/user/ssh-known-hosts/
* setting aws version to 2.7.* in composer.json didn''t help resolve the tls alert issue
 * setting to >=2.7.* since that''s the minimum version for which the passFail update works (check unit test)
 * actually setting to >=2.7.27 since the asterisk is not accepted as a version number in composer
* added composer self-update to travis yml file

2015-05-18
* added/improved some tests
* added travis.yml for CI with travis-ci.org
* incorporated composer for phpunit, aws (instead of AWS_PHAR)
* replaced several instances of putItem with updateItem
* after finally getting around the gnu tls error on travis-ci connecting to dynamodb (soln was to change from us-west-2 to us-east-1)
 * i ran the unit tests locally and corrected them by connecting to us-east-1 (after manually creating the tables)
 * i also added tests for newly created accounts
 * using aws >=8.4 so that the update of passfail works
 * travis only testing php 5.5 since that was irrelevant to the gnu tls issue

2015-05-17
* photos: moving from using local folder to S3 bucket + tested
* added backup photos script + tested
* added line output in log after each sync

2015-05-16
* added statistic scripts on live data (versus analyse.R on bkp files)

2015-05-15
* force update a car if it is available in the ddb but with old data
* split out uploadPhotoCore from uploadPhoto
 * also added uploadPhotoAsDataUrl
 * also simplified core
* added scipts listEmpty, etc
* removed trailing period from email sentence of forgot password and new email
* get2 is same as get but for angular get
 * I need to keep get because current apps use it
 * I should decomission it when no apps use it anymore
* loading photo as dataurl instaed of image because of javascript security exceptino in the latter case
* somehow I had accidentally removed the conversion of errors to json from login.php
 * I remember it was when I saw an error that the user was not supposed to see
 * so I judged that I should keep errors internal
 * but now I realize that some errors are supposed to bubble up to the user, such as wrong password, or unregistered email
* some CORS, angular http, jquery ajax, php crap

2015-05-13
* suppress html parsing warnings
* can upload photos and download them
* removed error returned as json and kept as internal server error
* removing empty fields before sending to dynamodb: made more general to any field, and not just the label, area, or number
* Changelog: removed double equal from dates
* new.php, update.php: adding isset(argc) to avoid the warning when used from html

2015-05-08
* not force refreshing data in case hp/y/t data missing
 * just remove dm before returning to user
* added test to see service availability
* api minor improvements of check variables at start to avoid notices
* furthered test_09_connectivity.sh

2015-05-07
* case of cached data modified with the mechanique info removed should not return mechanique results ... done
* case of timeout frmo isf, pml, dawlati servers => returmn 'Not available' to client instead of internal server error
* minor notice corrections
* added header message
* added thousands separator to mechanique value

2015-05-06
* adding mechanique information from dawlati.gov.lb
* added force to get.php
* ok to go live with dawlati mechanique schedule
* added check if car modified in getCore.php and forcing sync if so
* added case of no results found to syncCoreDawlati...

# Ver 0.0.2
2015-05-04
* include AWS_PHAR instead of require_once ...
* added backing up data weekly

2015-04-24
* dropUnconfirmed: changed to 30 days instead of 7 days
* added timestamp to log entries
* added addedTs to zboota-cars: this is the time at which a car is first added
 * this will allow me to track the evolution of number of added cars for unregistered users
* added default timestamp of current time for lastGetTs if missing

2015-04-23
* aws.phar is no longer soft-linked from a million places
 * because now I just use defined variable AWS_PHAR in the config file
* also stopped dropping orphan cars on a daily basis
 * and attached a last get date in order to drop "stale" cars instead
 * since all unregistered emails use "orphan" cars
* new accounts initial list of cars was [] but should be {}

# Ver 0.0.1

2015-04-22
* added statistics.php

2015-04-20
* some improvements to home page

2015-04-03
* some improvements to the home page

2015-02-14
* removed confirmation code
* generating my own password
* added lastloginDate
* using lastloginDate='' to capture 'unconfirmed' emails
* dropOrphan, dropStale, dropUnconfirmed all part of syncAll script

2015-02-11
* added deeplink in zabet notification email

2015-02-09
* added forgot password
* moved most code to ZbootaClient class (which also has interface)
* added increment pass/fail steps to confirmation and forgot password, so that if a user does so more than MAX times, the account is also locked (to avoid email flooding)

2015-02-05
* zboota-users: status -> status2 (reserved dynamodb keyword)
* added dropUnconfirmed accounts
* added dropOrphanCars to crontab

2015-01-28
* added notification preprocessing to avoid emailing users about same tickets every day
* split out notification sending php from syncAll php file
* added drop orphan cars in zboota-cars

2015-01-15
* First release
* client SPA
* dynamodb table for users
* can login + register new
