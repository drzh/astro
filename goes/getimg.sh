#!/bin/bash

# ----- Lock file ----- #
# Lock file to ensure only one task is running
LOCKFILE=/home/celaeno/web/astro/goes/img/lock.txt
if [ -e ${LOCKFILE} ] && kill -0 `cat ${LOCKFILE}`; then
    echo "already running"
    exit
fi

# ----- Job start ----- #

url='https://cdn.star.nesdis.noaa.gov/GOES16/ABI/FD/'
chs='02 15'

for c in `echo $chs`; do
    u=`echo ${url}${c}/`
    wget $u -O img/${c}.html
    cimg=`cat img/${c}.html | grep jpg | grep GOES16 | grep 5424 | perl -npe 's#<a .+>(\S+)</a>.*#$1#' | tail -n1 | cut -f1,2 -d'.'`
    echo $cimg
done


# ----- Job end ----- #

# ----- Remove lock file ----- #
rm -rf ${LOCKFILE}
