#!/bin/bash
grep "/zboota-server" /var/log/apache2/access.log*|awk '{print $4}'|tr -d "["|awk -F: '{print $1}'|sed 's/Jul/07/g'|sed 's/Jun/06/g'|awk -F/ '{print $3 "/" $2 "/" $1}'|sort -r|uniq -c
