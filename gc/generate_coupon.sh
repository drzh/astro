#!/bin/bash

# Usage: <STDIN> | prog

for f in `cat`; do
    echo $f | perl -npe 's#\s##g' | barcode -e 128 -g 350x100 | convert -crop 400x200+0+670 - png:- | base64 -w 0 | cat - <(echo)
done
