#!/bin/bash

# ----- Lock file ----- #
# Lock file to ensure only one task is running
LOCKFILE=/home/celaeno/web/astro/radar/data/lock.txt
if [ -e ${LOCKFILE} ] && kill -0 `cat ${LOCKFILE}`; then
    echo "already running"
    exit
fi
# make sure the lockfile is removed when we exit and then claim it
trap "rm -f ${LOCKFILE}; exit" INT TERM EXIT
echo $$ > ${LOCKFILE}

# ----- Job start ----- #

url='s3://noaa-nexrad-level2/'
site='KFWS'

cmdls='/usr/bin/aws s3 --no-sign-request --no-verify-ssl ls'
cmdcp='/usr/bin/aws s3 --no-sign-request --no-verify-ssl cp'

for f in `ls data/${site}* 2>/dev/null`; do
    rm data/$f
done

for i in `seq 1 3`; do
    d=`$cmdls $url | grep PRE | tail -n1 | awk '{print \$2}'`
    url=`echo ${url}${d}`
done

url=`echo ${url}${site}/`

fnc=`$cmdls $url | tail -n1 | awk '{print \$4}'`

t1=`echo $fnc | cut -c5-12,14-19`
t2=`ls data/ | grep png | grep DFW.${site} | tail -n1 | cut -f3 -d'.'`
if [ "$t1" != "$t2" ] ; then
    $cmdcp ${url}${fnc} data/
    if [ -e data/${fnc} ]; then
        ./plot_radar.py -97.2 32.5 -96.5 33.1 5 site.pos data/$fnc data/All.${site}.${t1}.png
        convert -resize 25% data/All.${site}.${t1}.png data/DFW.${site}.${t1}.png
        convert -crop 600x600+1580+800 -bordercolor gray -border 1x1 data/All.${site}.${t1}.png data/Dallas.${site}.${t1}.png
        #if [ -e data/DFW.${site}.${t1}.png ]; then
        #    ln -f -s DFW.${site}.${t1}.png data/DFW.png
        #fi
        if [ -e data/Dallas.${site}.${t1}.png ]; then
            ln -f -s Dallas.${site}.${t1}.png data/All.${site}.${t1}.png
        fi
        for f in `ls data/*.${site}.*.png | grep -v ${t1}`; do
            rm $f
        done
        rm data/${fnc}
    fi
fi

# ----- Job end ----- #

# ----- Remove lock file ----- #
rm -rf ${LOCKFILE}
