#!/usr/bin/python3

import pyproj
import sys
import re

_, fsite = sys.argv

# GOES-16 Extent (satellite projection) [llx, lly, urx, ury]
extend = [-5434894.885056, -5434894.885056, 5434894.885056, 5434894.885056]
figsize = [5424, 5424]

# sat = pyproj.Proj('+proj=geos +lon_0=-75 +h=035785831.0 +x_0=0 +y_0=0')
sat = pyproj.Proj('+proj=geos +h=35786023.0 +a=6378137.0 +b=6356752.31414 +f=0.00335281068119356027 +lat_0=0.0 +lon_0=-75.0 +sweep=x +no_defs')

with open(fsite) as site:
    cont = site.read()
    pat = re.compile(r"array\s*\(\s*'([^']+)',\s*([-\d\.]+),\s*([-\d\.]+),")
    for name, lat, lon in pat.findall(cont):
        x, y =  sat(lon, lat, radians = False, errcheck = True)
        x = int((extend[2] + x) / (extend[2] - extend[0]) * figsize[0])
        y = int(figsize[1] - (extend[3] + y) / (extend[3] - extend[1]) * figsize[1])
        print('\t'.join([name, str(x), str(y)]))
