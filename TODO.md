* do not drop orphan cars
 * instead, add a last get date flag and drop "stale" cars
* move more tests to phpUnit
* travis-ci causes a gnu tls error
 * I suspect this comes from aws phar being recent (2.8.4)
 * but going back to 2.4.12 causes updateItem not to work (for incrementing passFail)
 * I should try versions in between (if indeed this is the case)
