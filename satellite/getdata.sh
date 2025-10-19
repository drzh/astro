#!/bin/bash

cd /home/celaeno/web/astro/satellite/data

find . -mtime +5 | xargs rm

curl https://www.amsat.org/track/index.php > index.html
cat index.html | perl -npe 'undef $/;s#.*<select name="satellite" size="1">(.+?)</select>.*#$1#gs' | grep option | perl -npe 's#.*<option value="##; s#".+##' > satellite.list

for s in `cat satellite.list | sed 's# #+#g'`; do
    newf=`echo $s | sed 's#\+#_#g; s#/#_#'`
    curl --data "lang=en&satellite=${s}&count=50&loc=&lat=32.866&latdir=+&lng=96.761&longdir=+&ele=0&doPredict=+Predict+" https://www.amsat.org/track/index.php > sat.${newf}.ori.html
    # cat sat.${newf}.ori.html | ../format_table_to_html.pl > sat.${newf}.table.html
    ../format_table_to_tsv.pl sat.${newf}.ori.html > sat.${newf}.table.tsv
done

cat `ls *tsv | grep -v ALL` | head -n1 > sat.ALL.table.tsv
cat *tsv | grep -v Date | sort -k2,3 >> sat.ALL.table.tsv
cat sat.ALL.table.tsv | head -n1 > sat.ALL_PRI.table.tsv
for s in `cat ../priority.list | grep -v ALL`; do f="sat.${s}.table.tsv" ; if [ -e $f ]; then cat $f; fi ; done | grep -v Date | sort -k2,3 >> sat.ALL_PRI.table.tsv
