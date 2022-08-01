#!/bin/bash

url1='s3://noaa-goes16/ABI-L2-CMIPC/'
#url2='s3://noaa-goes16/ABI-L2-CMIPF/'

chs=`seq -w 1 16 | sed 's# #,#g'`

urlall=`echo $url1 $url2`

cmdls='aws s3 --no-sign-request --endpoint-url https://osdc.rcc.uchicago.edu --no-verify-ssl ls'
cmdcp='aws s3 --no-sign-request --endpoint-url https://osdc.rcc.uchicago.edu --no-verify-ssl cp'
# aws s3 cp s3://noaa-goes16/ABI-L2-CMIPF/2017/361/22/OR_ABI-L2-CMIPF-M3C16_G16_s20173612200449_e20173612211228_c20173612211306.nc --no-sign-request --endpoint-url https://osdc.rcc.uchicago.edu --no-verify-ssl

for url in `echo $urlall`; do
    for i in `seq 1 3`; do
        d=`$cmdls $url | tail -n1 | awk '{print \$2}'`
        url=`echo ${url}${d}`
        # echo $d
    done
    fs=`$cmdls $url | awk '{print $4}' | perl -npe 's#\n#,#g'`;
    for c in `echo $chs | sed 's#,# #g'`; do
        f=`echo $fs | sed 's#,#\n#g' | grep C$c | tail -n1`;
        $cmdcp ${url}${f} data/
    done
done
