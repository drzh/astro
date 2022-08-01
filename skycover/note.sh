#!/bin/bash

wgrib2 sample.grib2 -vt -s | grep 2017082900 | wgrib2 -i sample.grib2 -bin small.grb2
