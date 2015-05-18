# General
* Author: Shadi Akiki ( shadiakiki1986 at gmail dot com )
* Licensed under WTFPL
* Semantic versioning: http://semver.org/
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

# Notes
* Avoid Dynamodb reserved keywords:
 * http://docs.aws.amazon.com/amazondynamodb/latest/developerguide/ReservedWords.html
