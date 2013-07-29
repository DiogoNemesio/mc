#!/bin/bash

USER="megaUser"
PASS="megaPasswd"
HOST="localhost"

mysqldump -c -h $HOST -u $USER --password=$PASS megacondominio > mc.sql
gzip -f mc.sql
