#!/bin/bash
./getdate.sh 20220101 20230101 | ./calc_jgrs.pl | sort | uniq -w11 | awk '{print "Jupiter\tGRS Tra\t"$0}' > jupiter.grs_transit.2022.UTC.format
