#!/bin/bash

# create metabase shema db - initialized by Metabase
mysql -uroot -p$MYSQL_ROOT_PASSWORD -hmysql -e"CREATE DATABASE IF NOT EXISTS $MB_DB_DBNAME;"

# create metabase for jira data
mysql -uroot -p$MYSQL_ROOT_PASSWORD -hmysql -e"CREATE DATABASE IF NOT EXISTS $MYSQL_DATABASE;"

executeFile() {
    data=`cat $1`
    data=${data//'$MYSQL_USER'/$MYSQL_USER}
    data=${data//'$MYSQL_PASSWORD'/$MYSQL_PASSWORD}
    data=${data//'$MB_DB_DBNAME'/$MB_DB_DBNAME}
    data=${data//'$MYSQL_DATABASE'/$MYSQL_DATABASE}

    mysql -uroot -p$MYSQL_ROOT_PASSWORD -hmysql -D$MYSQL_DATABASE -e"$data"
    mv $1 $1.ok
}

#ls -1 *.txt
for file in *.sql
do
    echo "Verifying [$file]"
    executeFile $file;
done

