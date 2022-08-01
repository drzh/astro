#!/bin/bash

# Usage: <STDIN> | prog

# i=0

# rm img/*png

for f in `cat - | perl -npe 's#[ \t]+#:#g; s#^:+##g'`; do
    num=`echo $f | cut -f1 -d':' | perl -npe 's#(\.\d\d).*#$1#'`
    numf=`echo $num | perl -npe 's#(\d{4})(\d{4})(\d{4})(\d{4})#$1 $2 $3 $4#'`
    pin=`echo $f | cut -f2 -d':'`
    amn=`echo $f | cut -f3 -d':'`
    # i=$((i + 1))
    convert -gravity center saks.png <(convert -bordercolor white -border 10 -fill black -pointsize 40 label:"\$$amn" png:-) <(convert -fill black -pointsize 20 label:"Card #: $numf" png:-) <(convert -bordercolor white -border 5 -fill black -pointsize 20 label:"PIN: $pin" png:-) <(barcode -b $num -e 128 -g 286x100 | convert -crop 310x80+5+680 - png:-) -append png:- | base64 -w 0 | cat - <(echo)
    # convert -gravity center saks.png <(convert -bordercolor white -border 10 -fill black -pointsize 40 label:"\$$amn" png:-) <(convert -fill black -pointsize 20 label:"Card #: $numf" png:-) <(convert -bordercolor white -border 5 -fill black -pointsize 20 label:"PIN: $pin" png:-) <(barcode -b $num -e 128 -g 275x100 -u pt | convert -crop 310x80+5+680 - png:-) -append png:- | base64 -w 0 | cat - <(echo)
done
