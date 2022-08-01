#!/usr/bin/python3

import matplotlib
matplotlib.use('Agg')

import sys
import matplotlib.pyplot as plt
import pygrib
from mpl_toolkits.basemap import Basemap
import numpy as np
import glob
# import datetime
import re

_, minlon, minlat, maxlon, maxlat, fsite, di = sys.argv

fs = glob.glob(di + '/*.grb')
extent = [float(x) for x in [minlon, minlat, maxlon, maxlat]]
if (fsite != '-'):
    with open(fsite) as site:
        cont = site.read()
        pat = re.compile(r"array\s*\(\s*'([^']+)',\s*([-\d\.]+),\s*([-\d\.]+),")
        name_lat_lon = pat.findall(cont)

DPI = 100
ax = plt.figure(figsize=(768/float(DPI), 768/float(DPI)), frameon=True, dpi=DPI)

# Create the basemap reference for the Rectangular Projection
bmap = Basemap(llcrnrlon=extent[0], llcrnrlat=extent[1], urcrnrlon=extent[2], urcrnrlat=extent[3], epsg=4326)

# Draw the countries and Brazilian states shapefiles
bmap.readshapefile('../shapefiles/cb_2016_us_state_500k','cb_2016_us_state_500k',linewidth=0.5,color='darkslategray')

# Draw the coastlines, countries, parallels and meridians
bmap.drawparallels(np.arange(-90.0, 90.0, int((float(maxlat) - float(minlat)) / 10)), linewidth=0.25, color='white', labels=[True,False,False,True])
bmap.drawmeridians(np.arange(0.0, 360.0, int((float(maxlon) - float(minlon)) / 10)), linewidth=0.25, color='white', labels=[True,False,False,True])

# # Insert the legend
# cb = bmap.colorbar(location='right', size = '2%')
# cb.outline.set_visible(False) # Remove the colorbar outline
# cb.ax.tick_params(width = 0) # Remove the colorbar ticks
# cb.ax.xaxis.set_tick_params(pad=-12.5) # Put the colobar labels inside the colorbar
# cb.ax.tick_params(axis='x', colors='yellow', labelsize=6) # Change the color and size of the colorbar labels

for fi in fs:
    fo = fi.replace('.grb', '.png')
    dt = fi.split('/')[-1]
    grbs = pygrib.open(fi)
    grb = grbs[1]
    data = grb.values
    lat,lon = grb.latlons()

    x, y = bmap(lon,lat)
    cb = bmap.pcolormesh(x,y,data,shading='flat',cmap=plt.cm.jet)

    # Plot site markers
    for name, lat, lon in name_lat_lon:
        lons, lats = bmap(float(lon), float(lat))
        bmap.plot(lons, lats, '+', markersize=5, color='r')

    # Add a title to the plot
    plt.title(dt[0:4] + '-' + dt[4:6] + '-' + dt[6:8] + ' ' + dt[8:10] + 'h UTC', fontsize=6)
    
    plt.savefig(fo, bbox_inches='tight', pad_inches=0)
