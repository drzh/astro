#!/bin/bash
./getdate.sh 20251119 20270101 | ./calc_jgrs.pl | sort | uniq -w11 | awk '{print "Jupiter\tGRS Tra\t"$0}' > jupiter.grs_transit.2026.UTC.format
