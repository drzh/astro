#!/bin/bash
for f in `cat site.pos | perl -npe 's# #*#g; s#\t#_#g'`; do s=`echo $f | cut -f1 -d'_' | sed 's#*# #g'`; lon=`echo $f | cut -f2 -d'_'`; lat=`echo $f | cut -f3 -d'_'`; echo -ne $s"\t" ; ./calc_proj_fulldisk.2.py $lon $lat ; done
