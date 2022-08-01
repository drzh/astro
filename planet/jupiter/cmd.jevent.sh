# must be executed in 
./jevent 1 6 2018 -d3650 > ~/web/astro/planet/jupiter/jupiter.satellite.2018_2028

cat jupiter.satellite.2018_2028 | perl -npe '$/=""; s#.*Final results:##gs' | sed '1,2d' | cut -f1,2 -d':' | perl -npe 's# +$##; s#(\S):#$1 :#; s#sat 1#I#; s#sat 2#II#; s#sat 3#III#; s#sat 4#IV#' > jupiter.satellite.2018_2028.1

cat jupiter.satellite.2018_2028 | perl -npe '$/=""; s#.*Final results:##gs' | sed '1,2d' | cut -f3- -d':' | date -f /dev/stdin -u '+%Y%m%d %H%M' | sed 's# #\t#g' > jupiter.satellite.2018_2028.2

paste jupiter.satellite.2018_2028.1 jupiter.satellite.2018_2028.2 | awk '{print "Jupiter\t"$0}' > jupiter.satellite.2018_2028.UTC.format
