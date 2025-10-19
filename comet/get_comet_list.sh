#!/bin/bash
cd /home/celaeno/web/astro/comet/
wget "https://cobs.si/api/comet_list.api?is-observed=true" -O cobs.commet.list.observed.json
python3 ~/script/hobby/astro/convert_json_to_table.py -i cobs.commet.list.observed.json -o cobs.commet.list.observed.json.txt -k objects
cat <(head -n1 cobs.commet.list.observed.json.txt) <(cat cobs.commet.list.observed.json.txt | sed '1d' | awk -F'\t' '{if($8!="" && $8<=16)print}' | sort -t$'\t' -k8,8g) | cut -f4,8-12 > tmp
mv tmp cobs.commet.list.observed.json.txt
