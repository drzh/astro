#!/usr/bin/python3

import matplotlib
matplotlib.use('Agg')

import sys
import matplotlib.pyplot as plt
from mpl_toolkits.basemap import Basemap
import numpy as np
import datetime
import dateutil

from remap_conus import remap # Import the Remap function
from cpt_convert import loadCPT # Import the CPT convert function
from matplotlib.colors import LinearSegmentedColormap # Linear interpolation for color maps

_, minlon, minlat, maxlon, maxlat, psize, fsite, fi, fo = sys.argv

channel = {
    '01' : '0.47µm , Visible - Blue',
    '02' : '0.64µm , Visible - Red',
    '03' : '0.86µm , Near IR - Vegetation',
    '04' : '1.37µm , Near IR - Cirrus',
    '05' : '1.6µm , Near IR - Snow/Ice',
    '06' : '2.2µm , Near IR - Cloud Ice',
    '07' : '3.9µm , IR - Shortwave',
    '08' : '6.2µm , IR - Upper-Level Water Vapor',
    '09' : '6.9µm , IR - Mid-Level Water Vapor',
    '10' : '7.3µm , IR - Lower-Level Water Vapor',
    '11' : '8.4µm , IR - Cloud Top Phase',
    '12' : '9.6µm , IR - Ozone',
    '13' : '10.3µm , IR - Clean Longwave',
    '14' : '11.2µm , IR - Standard Longwave',
    '15' : '12.3µm , IR - Dirty Longwave',
    '16' : '13.32µm , IR - CO2 Longwave'
}

# Basic information
# (_, prod, sat, start, end, creat) = fi.split('.')[0].split('_')
(_, prod, sat, start, end, creat) = fi.split('/')[-1].split('.')[0].split('_')

# Convert time zone
from_zone = dateutil.tz.tzutc()
to_zone = dateutil.tz.tzlocal()
utctime = datetime.datetime.strptime(start[1:14], '%Y%j%H%M%S')
utctime = utctime.replace(tzinfo=from_zone)
localtime = utctime.astimezone(to_zone).strftime('%a %m/%d %H:%M %Z')

# Choose the visualization extent (min lon, min lat, max lon, max lat)
extent = [float(x) for x in [minlon, minlat, maxlon, maxlat]]

# Choose the image resolution (the higher the number the faster the processing is)
# res = float(res)
res = 1.0
# Call the reprojection funcion
grid = remap(fi, extent, res, 'HDF5')
data = grid.ReadAsArray()

# Read the data returned by the function and convert it to Celsius
band = int(prod[-2:]);
if band > 6:
    data = data - 273.15
else:
    data = data * 100;

psize = float(psize)
DPI = 100
ax = plt.figure(figsize=(768 * psize / float(DPI), 768 * psize / float(DPI)), frameon=True, dpi=DPI)

# Create the basemap reference for the Rectangular Projection
bmap = Basemap(llcrnrlon=extent[0], llcrnrlat=extent[1], urcrnrlon=extent[2], urcrnrlat=extent[3], epsg=4326)
# bmap = Basemap(llcrnrlon=extent[0], llcrnrlat=extent[1], urcrnrlon=extent[2], urcrnrlat=extent[3], epsg=3395)

if band <= 6:
    # Converts the CPT file to be used in Python
    cpt = loadCPT('colortables/Square_Root_Visible_Enhancement.cpt')
    # Makes a linear interpolation with the CPT file
    cpt_convert = LinearSegmentedColormap('cpt', cpt)
    bmap.imshow(data, origin='upper', cmap=cpt_convert, vmin=0, vmax=100) 
elif band == 7:
    cpt = loadCPT('colortables/SVGAIR2_TEMP.cpt')
    cpt_convert = LinearSegmentedColormap('cpt', cpt)
    bmap.imshow(data, origin='upper', cmap=cpt_convert, vmin=-112.15, vmax=56.85) 
elif band > 7 and band < 11:
    cpt = loadCPT('colortables/SVGAWVX_TEMP.cpt')
    cpt_convert = LinearSegmentedColormap('cpt', cpt)
    bmap.imshow(data, origin='upper', cmap=cpt_convert, vmin=-112.15, vmax=56.85)
elif band > 10:
    cpt = loadCPT('colortables/IR4AVHRR6.cpt')
    cpt_convert = LinearSegmentedColormap('cpt', cpt)
    bmap.imshow(data, origin='upper', cmap=cpt_convert, vmin=-103, vmax=84) 

# Draw the countries and Brazilian states shapefiles
# bmap.readshapefile('shapefiles/BRA_adm1','BRA_adm1',linewidth=0.50,color='darkslategray')
bmap.readshapefile('shapefiles/cb_2017_us_state_500k','cb_2017_us_state_500k',linewidth=0.5,color='darkslategray')

# Draw the coastlines, countries, parallels and meridians
steplon = int((extent[2] - extent[0]) / 10 / psize)
steplat = int((extent[3] - extent[1]) / 10 / psize)
step = max(steplon, steplat)
bmap.drawparallels(np.arange(-90.0, 90.0, step), linewidth=0.2, color='white', labels=[True,False,False,True], fontsize=8, labelstyle='+/-')
bmap.drawmeridians(np.arange(0.0, 360.0, step), linewidth=0.2, color='white', labels=[True,False,False,True], fontsize=8, labelstyle='+/-')

# Insert the legend
if band <= 6:
    cb = bmap.colorbar(location='right', size = str(2 / psize) + '%', pad = '0.5%', ticks=[20, 40, 60, 80])
    cb.ax.set_xticklabels(['20', '40', '60', '80'])
else:
    cb = bmap.colorbar(location='right', size = str(2 / psize) + '%', pad = '0.5%')
     
cb.outline.set_visible(False) # Remove the colorbar outline
cb.ax.tick_params(width = 0) # Remove the colorbar ticks
cb.ax.xaxis.set_tick_params(pad=-5) # Put the colobar labels inside the colorbar
cb.ax.tick_params(axis='x', colors='yellow', labelsize=6) # Change the color and size of the colorbar labels

# Plot site markers
if (fsite != '-'):
    with open(fsite) as site:
        cont = site.readlines()
        for line in cont:
            name, lon, lat = line.strip('\n').split('\t')
            lons, lats = bmap(float(lon), float(lat))
            bmap.plot(lons, lats, '+', markersize=5, color='r')

# Add a title to the plot
plt.title(prod[0:-3] + ' , C' + prod[-2:] + ' , ' + channel[prod[-2:]] + ' , ' + localtime, fontsize=8)

plt.savefig(fo, bbox_inches='tight', pad_inches=0)
