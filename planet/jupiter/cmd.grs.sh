wget wget --no-check-certificate https://www.projectpluto.com/jeve_grs.htm
cat jeve_grs.htm | grep '<br>' | sed '1d' | sed 's#<br>##' | date -f /dev/stdin -u '+%Y%m%d %H%M' | sed 's# #\t#g' | awk '{print "Jupiter\tGRS Tra\t"$0}' > jupiter.grs_transit.2018.UTC.format
