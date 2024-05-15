#!/bin/bash
cd /home/celaeno/web/astro/table/
wget "https://cobs.si/api/comet_list.api?is-observed=true" -O cobs.commet.list.observed.json
python3 ~/script/hobby/astro/convert_json_to_table.py -i cobs.commet.list.observed.json -o cobs.commet.list.observed.json.txt -k objects
cat <(head -n1 cobs.commet.list.observed.json.txt) <(cat cobs.commet.list.observed.json.txt | sed '1d' | awk -F'\t' '{if($7!="" && $7<=16)print}' | sort -t$'\t' -k7,7g) > tmp
mv tmp cobs.commet.list.observed.json.txt
