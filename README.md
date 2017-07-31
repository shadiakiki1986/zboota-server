# General
* Author: Shadi Akiki ( shadiakiki1986 at gmail dot com )
* Licensed under WTFPL
* [![Build Status](https://secure.travis-ci.org/shadiakiki1986/zboota-server.png)](http://travis-ci.org/shadiakiki1986/zboota-server)

# Description
* Server-side of zboota app
* ~~It serves an API to the following websites~~
* complements [zboota-server-nodejs](https://github.com/shadiakiki1986/zboota-server-nodejs) which serves the API to the mobile app [zboota-app](https://github.com/shadiakiki1986/zboota-app)
  * http://www.isf.gov.lb/ar/speedtickets
 * http://www.parkmeterlebanon.com/default/STATMENT_OF_ACCOUNT.aspx
* It manages user information and updates their data daily from the sites above


# Architecture
* php server
* mobile app developed with cordova
* json messages
* dynamodb table for user data
* AWS EC2 instance
 * serves API
 * updates data daily

# Note 1: Dynamodb reserved keywords
* http://docs.aws.amazon.com/amazondynamodb/latest/developerguide/ReservedWords.html

# Note 2: Failed builds on travis-ci
* Build 42 (https://travis-ci.org/shadiakiki1986/zboota-server/builds/63521468) on travis-ci seems to fail with an error about TLS
* I had thought to have solved this issue by using the known hosts field in the travis yml file
* so I tried to rebuild the last successful commit, a08093d, by branching it out to a branch I called ''lastsuccess''
* Commit a08093d had passed in build 41 (https://travis-ci.org/shadiakiki1986/zboota-server/builds/63161583)
* however, building it again, via the whitelist entry in the travis yml file, seems to fail now in build 43 (https://travis-ci.org/shadiakiki1986/zboota-server/builds/63524924)
* I''ll need to understand what is going on .. later

# Devop note: Large amount of email notifications
I have set a breaker in the scripts/sendNotification.php script for the number of emails that can be sent in one day.
The reason I have this is that if something goes wrong and all of a sudden the server wants to email an obscene number of registrants, this would stop it and notify me to check.
The limit at the time of this writing (2015-10-15) is at 10, and I just moved it up to 20.
If in the future 20 is not enough, just edit the ''zboota-server/config.php'' file for the `NOTIF_BREAKER` variable.
Also, to check a simulation of what would be sent, edit the config.php for `NOTIF_SIMULATION` to be true, then run `php scripts/sendNotifications.php`.
Note that this is currently installed on an EC2 instance that runs for 1 hour daily in the morning (managed by OpsWorks)
