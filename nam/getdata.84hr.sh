#!/bin/bash

# ----- Lock file ----- #
# Lock file to ensure only one task is running
LOCKFILE=/home/celaeno/web/astro/nam/data/lock.txt
if [ -e ${LOCKFILE} ] && kill -0 `cat ${LOCKFILE}`; then
    echo "already running"
    exit
fi
# make sure the lockfile is removed when we exit and then claim it
trap "rm -f ${LOCKFILE}; exit" INT TERM EXIT
echo $$ > ${LOCKFILE}


# ----- Job start ----- #

url='s3://noaa-nam-pds/'

cmdls='/usr/bin/aws s3 --no-sign-request ls'
cmdcp='/usr/bin/aws s3 --no-sign-request cp'

wgrib2='/home/celaeno/usr/bin/wgrib2'

for f in `ls data/ | grep -P 'grib2$'`; do
    rm data/$f
done

$cmdls $url | grep 'nam.20' > nam.date.txt
date=`cat nam.date.txt | tail -n1 | awk '{print $2}'`
$cmdls ${url}${date} > nam.tz.txt
tz=`cat nam.tz.txt | awk '{print $4}' | grep 'nam' | grep 'grbgrd84' | grep -P 'grib2$' | cut -f2 -d'.' | sort | uniq | tail -n1`

# Check if $tz is empty
if [ -z "$tz" ]; then
    rm -rf ${LOCKFILE}
    exit
fi

# Check if the file 'nam.current.date.tz.txt' exists
if [ -e nam.current.date.tz.txt ]; then
    # If it exists, check if the current date and tz is the same as the date in the file
    current_date=`cat nam.current.date.tz.txt | cut -f1 -d' '`
    current_tz=`cat nam.current.date.tz.txt | cut -f2 -d' '`
    # If the current date or tz is the same as the date in the file, exit
    if [ "$current_date" == "$date" ] && [ "$current_tz" == "$tz" ]; then
        rm -rf ${LOCKFILE}
        exit
    fi
fi

outfile='all.skycover.84hr.UTC.format'
outfile_tmp='all.skycover.84hr.UTC.format.tmp'
if [ -e $outfile_tmp ]; then
    rm $outfile_tmp
fi

starthour=`echo $tz | cut -c2,3`
startdate=`echo $date | cut -c5-12`
starttime=`echo $startdate $starthour`
fs=`cat nam.tz.txt | awk '{print $4}' | grep nam | grep "${tz}" | grep 'grbgrd' | grep -P 'grib2$'`
for f in `echo $fs`; do 
    $cmdcp ${url}${date}${f} data/
    hour=`echo $f | cut -f3 -d'.' | cut -c7,8`
    newtime=`date "+%Y%m%d%H%M" -d "${starttime} + ${hour} hours"`
    
    # For each site in the site.pos (place, long, lat seperated by '\t') file, extract the 'TCDC' data from the grib2 file
    for s in `cat site.pos | perl -npe 's#\t#:#g; s# #_#g'`; do
        place=`echo $s | cut -f1 -d':' | perl -npe 's#_# #g'`
        lon=`echo $s | cut -f2 -d':'`
        lat=`echo $s | cut -f3 -d':'`
        echo -ne "$place\t$newtime\t" >> $outfile_tmp
        ${wgrib2} -s data/$f | grep ':TCDC:' | ${wgrib2} -i data/$f -lon ${lon} ${lat} | perl -npe 's#.*,val=(\d+).*#$1#' >> $outfile_tmp
    done

    rm data/$f
done

mv $outfile_tmp $outfile

# Update the file 'nam.current.date.tz.txt'
echo $date $tz > nam.current.date.tz.txt


# ----- Remove lock file ----- #
rm -rf ${LOCKFILE}
