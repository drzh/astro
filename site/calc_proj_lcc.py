#!/usr/bin/python3

# Usage: <STDIN_name_lon_lat> | prog | <STDOUT>

import pyproj
import sys

# # GOES-16 Extent (satellite projection) [llx, lly, urx, ury]
extend = [-5434894.885056, -5434894.885056, 5434894.885056, 5434894.885056]

# figsize = [5424, 5424]
figsize = [1073, 689]
offset = 0

# sat = pyproj.Proj('+proj=geos +h=35786023.0 +a=6378137.0 +b=6356752.31414 +f=0.00335281068119356027 +lat_0=0.0 +lon_0=-75.0 +sweep=x +no_defs')
sat = pyproj.Proj('+proj=lcc +lat_1=20.191999 +lon_0=265 +lat_2=45')

for line in sys.stdin:
    id, lon, lat = line.strip().split()
    lon = float(lon)
    lat = float(lat)
    try:
        x, y =  sat(lon, lat, radians = False, errcheck = True)
        x = int((extend[2] + x) / (extend[2] - extend[0]) * figsize[0])
        y = int(figsize[1] - (extend[3] + y) / (extend[3] - extend[1]) * figsize[1] + offset)
        print(id, x, y, sep = '\t')
    except:
        print(id, 'NA', 'NA', sep = '\t')
