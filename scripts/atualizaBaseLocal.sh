#!/bin/bash

DIR="/srv/www/htdocs/mc/scripts"
HOST="localhost"
USER="megaUser"
PASS="megaPasswd"

if [ -f "$DIR/mc.sql.gz" ]; then
	gzip -d $DIR/mc.sql.gz
        mysqladmin -h $HOST -u $USER -f --password=$PASS drop megacondominio
        mysqladmin -h $HOST -u $USER -f --password=$PASS create megacondominio
        mysql -h $HOST -u $USER --password=$PASS megacondominio < $DIR/mc.sql
        gzip $DIR/mc.sql
fi
