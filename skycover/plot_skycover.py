#!/usr/bin/python3

import matplotlib
matplotlib.use('Agg')

import sys
import matplotlib.pyplot as plt
import pygrib
from mpl_toolkits.basemap import Basemap
import numpy as np

_, minlon, minlat, maxlon, maxlat, psize, fsites, fpaths, title, fi, fo = sys.argv

extent = [float(x) for x in [minlon, minlat, maxlon, maxlat]]

grbs = pygrib.open(fi)

grb = grbs.select(parameterName='Total cloud cover')[0]
# grb = grbs[1]
data = grb.values
lat,lon = grb.latlons()

psize = float(psize)
DPI = 100
fig = plt.figure(figsize=(200*psize/float(DPI), 200*psize/float(DPI)), frameon=True, dpi=DPI)
ax = fig.add_axes([0, 0, 1, 1])
ax.axis('off')
# ax = plt.figure(figsize=(768/float(DPI), 768/float(DPI)), frameon=True, dpi=DPI)

# Create the basemap reference for the Rectangular Projection
bmap = Basemap(llcrnrlon=extent[0], llcrnrlat=extent[1], urcrnrlon=extent[2], urcrnrlat=extent[3], epsg=3395)

# Draw the countries and Brazilian states shapefiles
bmap.readshapefile('shapefiles/cb_2021_us_state_500k','cb_2021_us_state_500k',linewidth=0.5,color='darkslategray')

x, y = bmap(lon,lat)

# cb = bmap.pcolormesh(x, y, data, shading='flat', cmap=plt.cm.jet)
cb = bmap.pcolormesh(x, y, data, shading='gouraud', cmap=plt.cm.jet)

# Draw the coastlines, countries, parallels and meridians
steplon = int((extent[2] - extent[0]) / 10)
steplat = int((extent[3] - extent[1]) / 10)
step = max(steplon, steplat)

# Plot site markers
if fsites != '-':
    for fsite in fsites.split(','):
        with open(fsite) as f:
            for line in f:
                name, lon, lat = line.strip('\n').split('\t')
                lon, lat = bmap(float(lon), float(lat))
                bmap.plot(lon, lat, '+', markersize=2, color='r')

# Plot paths
lons = {}
lats = {}
if fpaths != '-':
    for fpath in fpaths.split(','):
        with open(fpath) as f:
            for line in f:
                name, lon, lat = line.strip().split('\t')
                lon, lat = bmap(float(lon), float(lat))
                if name not in lons:
                    lons[name] = []
                    lats[name] = []
                lons[name].append(lon)
                lats[name].append(lat)
            for name in lons:
                bmap.plot(lons[name], lats[name], linestyle = (0, [5, 25]), linewidth = 0.5, color = 'yellow')
                bmap.plot(lons[name], lats[name], linestyle = (15, [5, 25]), linewidth = 0.5, color = 'green')
                
# Add a title to the plot
# plt.title(fi[0:4] + '-' + fi[4:6] + '-' + fi[6:8] + ' ' + fi[8:10] + 'h UTC', fontsize=6)
plt.title(title, fontsize=10)

plt.savefig(fo, bbox_inches='tight', pad_inches=0)
