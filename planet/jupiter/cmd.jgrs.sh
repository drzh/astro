#!/bin/bash
./getdate.sh 20230101 20240101 | ./calc_jgrs.pl | sort | uniq -w11 | awk '{print "Jupiter\tGRS Tra\t"$0}' > jupiter.grs_transit.2023.UTC.format
