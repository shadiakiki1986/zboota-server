#!/bin/bash
# script to set the header message displayed in the app

m=$1
if [ "$m" == "" ]; then
	echo "Error in usage. bash headerMessagePut.sh \"something to show\"";
	echo "Use a single space to store an empty message";
	exit;
fi

f=~/.zboota-server/headerMessage.txt
sudo chown ubuntu:ubuntu $f
echo $m > $f
sudo chown www-data:www-data $f
