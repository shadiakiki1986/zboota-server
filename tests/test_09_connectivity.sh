#!/bin/sh

# Ping
curl -i http://genesis.akikieng.com/zboota-server/api/get.php
echo ""

# Anonymous retrieve
curl -i -d 'lpns=[{"a":"B","n":"537005"}]' http://genesis.akikieng.com/zboota-server/api/get.php
echo ""

# Login
curl -i -d 'email=shadiakiki1986@gmail.com&pass=123456' http://genesis.akikieng.com/zboota-server/api/login.php
echo ""

#curl -i -X GET -d email=shadiakiki1986@gmail.com -d pass=123456 http://genesis.akikieng.com/zboota-server/api/login2.php
#wget http://genesis.akikieng.com/zboota-server/api/login2.php?email=shadiakiki1986@gmail.com&pass=123456
