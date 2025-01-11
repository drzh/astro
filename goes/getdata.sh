#!/bin/bash

# ----- Lock file ----- #
# Lock file to ensure only one task is running
LOCKFILE=/home/celaeno/web/astro/goes/data/lock.txt
if [ -e ${LOCKFILE} ] && kill -0 `cat ${LOCKFILE}`; then
    echo "already running"
    exit
fi
# make sure the lockfile is removed when we exit and then claim it
trap "rm -f ${LOCKFILE}; exit" INT TERM EXIT
echo $$ > ${LOCKFILE}


# ----- Job start ----- #

url1='s3://noaa-goes16/ABI-L2-CMIPC/'
#url2='s3://noaa-goes16/ABI-L2-CMIPF/'

# chs=`echo 02 04 07 09 13 15`
chs=`echo 02 15`

urlall=`echo $url1 $url2`

# cmdls='/usr/bin/aws s3 --no-sign-request --endpoint-url https://osdc.rcc.uchicago.edu --no-verify-ssl ls'
# cmdcp='/usr/bin/aws s3 --no-sign-request --endpoint-url https://osdc.rcc.uchicago.edu --no-verify-ssl cp'
cmdls='/usr/local/bin/aws s3 --no-sign-request --no-verify-ssl ls'
cmdcp='/usr/local/bin/aws s3 --no-sign-request --no-verify-ssl cp'

for f in `ls data/ | grep nc`; do
    rm data/$f
done

for url in `echo $urlall`; do
    for i in `seq 1 3`; do
        d=`$cmdls $url | tail -n1 | awk '{print \$2}'`
        url=`echo ${url}${d}`
    done
    # fs=`$cmdls $url | awk '{print $4}' | perl -npe 's#\n#,#g'`;
    for c in `echo $chs | sed 's#,# #g'`; do
        fs=`$cmdls $url | awk '{print $4}' | perl -npe 's#\n#,#g'`;
        fnc=`echo $fs | sed 's#,#\n#g' | grep C$c | tail -n1`;
        t1=`echo $fnc | cut -f5 -d'_' | cut -c2-14`
        t2=`ls data/*.*.*.png | grep US.${c} | tail -n1 | cut -f3 -d'.'`
        if [ "$t1" != "$t2" ] ; then
            $cmdcp ${url}${fnc} data/
            if [ -e data/${fnc} ]; then
                #./plot_conus.py -106.0 25 -93.0 37.0 1 site.pos data/${fnc} data/TX.${c}.${t1}.png 2>&1
                #./plot_conus.py -121.0 22.0 -64.0 48.0 2 site.pos data/${fnc} data/US.${c}.${t1}.png 2>&1
                ./plot_conus.py -107.0 25.5 -93.0 37.0 1 site.pos data/${fnc} data/TX.${c}.${t1}.png 2>&1
                ./plot_conus.py -126.0 24.0 -66.0 50.0 2 site.pos data/${fnc} data/US.${c}.${t1}.png 2>&1
                if [ -e data/TX.${c}.${t1}.png ]; then
                    ln -f -s TX.${c}.${t1}.png data/TX.${c}.png
                fi
                if [ -e data/US.${c}.${t1}.png ]; then
                    ln -f -s US.${c}.${t1}.png data/US.${c}.png
                fi
                for f in `ls data/*.${c}.*.png | grep -v ${t1}`; do
                    rm $f
                done
                rm data/${fnc} data/${fnc}.aux.xml
            fi
        fi
    done
done

# ----- Job end ----- #

# ----- Remove lock file ----- #
rm -rf ${LOCKFILE}
