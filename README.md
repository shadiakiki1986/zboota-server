# General
* Author: Shadi Akiki ( shadiakiki1986 at gmail dot com )
* Licensed under WTFPL
* [![Build Status](https://secure.travis-ci.org/shadiakiki1986/zboota-server.png)](http://travis-ci.org/shadiakiki1986/zboota-server)

# Description
* Server-side of zboota app
* It serves an API to the following websites
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
