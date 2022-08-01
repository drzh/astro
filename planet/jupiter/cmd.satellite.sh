wget --no-check-certificate https://www.projectpluto.com/jevent.htm
cat jevent.htm | grep '<br>' | grep -v '<a' | sed 's#<br>##' | cut -f1,2 -d':' | awk '{print "Jupiter\t"$0}' > jupiter.satellite.1
cat jevent.htm | grep '<br>' | grep -v '<a' | sed 's#<br>##' | cut -f3- -d':' | date -f /dev/stdin -u '+%Y%m%d %H%M' | sed 's# #\t#g' > jupiter.satellite.txt
paste jupiter.satellite.1 jupiter.satellite.txt > jupiter.satellite.2018.UTC.format
