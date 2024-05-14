#!/bin/bash
./getdate.sh 20240509 20250101 | ./calc_jgrs.pl | sort | uniq -w11 | awk '{print "Jupiter\tGRS Tra\t"$0}' > jupiter.grs_transit.2024.UTC.format
