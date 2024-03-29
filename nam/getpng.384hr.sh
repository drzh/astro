#!/bin/bash

# Download the NAM 384-hour forecast png from https://weather.cod.edu/forecast/

dir='png.384'

cd $dir

urlmod='https://weather.cod.edu/forecast/assets/php/scripts/mkzip.php?parms=YYYYMMDDHH-GFS-US-prec-cloud-0-384'

# Get the current date and time in UTC
date=$(date -u +%Y%m%d)
hour=$(date -u +%H)

# Round (floor) the hour to 00, 06, 12 or 18
hour=$(echo $hour | awk '{printf("%02d", int($1/6)*6)}')

# Set max number of loops
nmax=8

n=0
# Loop until the directory is created or the max number of loops is reached
while [ ! -d png.$date$hour ] && [ $n -lt $nmax ]; do
    url=$(echo $urlmod | sed "s/YYYYMMDDHH/$date$hour/g")
    curl $url > tmp.zip
    # Check if the file is empty; if not, unzip the file
    if [ -s tmp.zip ]; then
        # Remove all other directories 'png.*'
        #rm -rf png.*
        unzip tmp.zip -d tmp
        rm tmp.zip
        # If the folder have at least 128 files, go ahead
        if [ $(ls tmp/ | wc -l) -ge 128 ]; then
            # If 'current' exists, remove it
            if [ -L current ]; then
                rm current
            fi
            # Recreat the symlink 'current' to the new directory
            rm -rf png.*
            mv tmp png.$date$hour
            ln -s png.$date$hour current
            break
        else
            rm -rf tmp
        fi
    fi
    rm tmp.zip
    # Minus 6 hour from the datehour
    date=$(date -u -d "$date $hour -6 hours" +%Y%m%d)
    hour=$(date -u -d "$date $hour -6 hours" +%H)
    url=$(echo $urlmod | sed "s/YYYYMMDDHH/$date$hour/g")
    n=$((n+1))
done

cd -