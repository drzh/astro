#!/usr/bin/python3

import matplotlib
matplotlib.use('Agg')

import sys
import matplotlib.pyplot as plt
import pygrib
from mpl_toolkits.basemap import Basemap
import numpy as np

_, minlon, minlat, maxlon, maxlat, psize, fsite, fo = sys.argv

extent = [float(x) for x in [minlon, minlat, maxlon, maxlat]]

psize = float(psize)

fontsz = 8 * psize

DPI = 100
fig = plt.figure(figsize=(6, 5.7), frameon=True, dpi=DPI)
ax = fig.add_axes([0, 0, 1, 1])
ax.axis('off')

# Create the basemap reference for the Rectangular Projection
# bmap = Basemap(llcrnrlon=extent[0], llcrnrlat=extent[1], urcrnrlon=extent[2], urcrnrlat=extent[3], epsg=3395)

bmap = Basemap(projection='cyl',
               llcrnrlat=minlat,llcrnrlon=minlon,
               urcrnrlat=maxlat,urcrnrlon=maxlon,
               resolution='h')

# Draw the countries and Brazilian states shapefiles
bmap.readshapefile('shapefiles/cb_2017_us_state_500k', 'cb_2017_us_state_500k', linewidth=0.8, color='black')
bmap.readshapefile('shapefiles/tl_2019_us_primaryroads', 'tl_2019_us_primaryroads', linewidth=0.2, color='black')

# Draw the coastlines, countries, parallels and meridians
step = 1
# bmap.drawmapboundary(fill_color='blue')
# bmap.drawparallels(np.arange(-90.0, 90.0, step), linewidth=0.2, color='black', labels=[False,False,False,False], fontsize=8)
# bmap.drawmeridians(np.arange(0.0, 360.0, step), linewidth=0.2, color='black', labels=[False,False,False,False], fontsize=8)

# Plot site markers
if (fsite != '-'):
    with open(fsite) as site:
        cont = site.readlines()
        for line in cont:
            name, lon, lat = line.strip('\n').split('\t')
            lons, lats = bmap(float(lon), float(lat))
            bmap.plot(lons, lats, '+', markersize=5, color='r')

plt.savefig(fo, bbox_inches='tight', pad_inches=0, transparent=True)
