#!/bin/bash

cd /home/celaeno/web/astro/satellite/ha

curl 'http://www.heavens-above.com/PassSummary.aspx?satid=25544&lat=32.8664&lng=-96.7615&loc=Dallas%2c+TX%2c+USA&alt=167&tz=CST' > ISS.html
../format_iss_to_tsv.pl ISS.html > ISS.tsv

curl 'http://www.heavens-above.com/PassSummary.aspx?satid=48274&lat=32.8664&lng=-96.7615&loc=Dallas%2c+TX%2c+USA&alt=167&tz=CST' > Tianhe-1.html
../format_iss_to_tsv.pl Tianhe-1.html > Tianhe-1.tsv

curl 'http://www.heavens-above.com/PassSummary.aspx?satid=20580&lat=32.8664&lng=-96.7615&loc=Dallas%2c+TX%2c+USA&alt=167&tz=CST' > HST.html
../format_iss_to_tsv.pl HST.html > HST.tsv

curl 'http://www.heavens-above.com/AllSats.aspx?lat=32.8664&lng=-96.7615&loc=Dallas%2c+TX%2c+USA&alt=167&tz=CST' > Satellites.html
../format_sat_to_tsv.pl Satellites.html > Satellites.tsv

cat Satellites.tsv | head -n1 | perl -npe 's#\(mag\)#Mag#' > All.tsv
cat `ls *tsv | grep -v All` | grep -v Satellite | sort -k2,2 -t$'\t' >> All.tsv
