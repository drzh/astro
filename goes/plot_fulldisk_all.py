#!/usr/bin/python3

import matplotlib
matplotlib.use('Agg')

import sys
import matplotlib.pyplot as plt
from netCDF4 import Dataset
from mpl_toolkits.basemap import Basemap
import numpy as np

from cpt_convert import loadCPT # Import the CPT convert function
from matplotlib.colors import LinearSegmentedColormap # Linear interpolation for color maps

fi = sys.argv[1]

# Basic information
(_, prod, sat, start, end, creat) = fi.split('.')[0].split('_')
startdate = start[1:5] + '-' + start[5:8] + '-' + ':'.join([start[8:10], start[10:12], start[12:14]])
enddate = end[1:5] + '-' + end[5:8] + '-' + ':'.join([end[8:10], end[10:12], end[12:14]])

# Converts the CPT file to be used in Python
cpt = loadCPT('IR4AVHRR6.cpt')
# Makes a linear interpolation with the CPT file
cpt_convert = LinearSegmentedColormap('cpt', cpt)

nc = Dataset(fi)
data = nc.variables['CMI'][:]

# Store the NetCDF file key variables in the "variable" variable, hahaha
variables = nc.variables.keys()
# print (variables)
# Read the header to retrieve the geospatial extent
geo_extent = nc.variables['geospatial_lat_lon_extent']
# print (geo_extent)
# Extract the image bounds and center, converting to string
center = str(geo_extent.geospatial_lon_center)
west = str(geo_extent.geospatial_westbound_longitude)
east = str(geo_extent.geospatial_eastbound_longitude)
north = str(geo_extent.geospatial_northbound_latitude)
south = str(geo_extent.geospatial_southbound_latitude)
center = geo_extent.geospatial_lon_center
# print (center)
# sys.exit()

# img = plt.gcf()
DPI = 100
ax = plt.figure(figsize=(2000/float(DPI), 2000/float(DPI)), frameon=True, dpi=DPI)

# Create the basemap reference for the Satellite Projection
bmap = Basemap(projection='geos', lon_0=-75.0, lat_0=0.0, satellite_height=35786023.0, ellps='GRS80', resolution = 'c')

# Plot GOES-16 Channel using 170 and 378 as the temperature thresholds
bmap.imshow(data, origin='upper', vmin=170, vmax=378, cmap=cpt_convert)

# Draw the coastlines, countries, parallels and meridians
bmap.drawcoastlines(linewidth=0.3, linestyle='solid', color='black')
bmap.drawcountries(linewidth=0.3, linestyle='solid', color='black')
bmap.drawparallels(np.arange(-90.0, 90.0, 10.0), linewidth=0.1, color='white')
bmap.drawmeridians(np.arange(0.0, 360.0, 10.0), linewidth=0.1, color='white')

# Insert the legend
bmap.colorbar(location='bottom', label='Brightness Temperature [K]')
# Add a title to the plot
plt.title(prod[0:-3] + ' ' + prod[-3:] + ' - from ' + startdate + ' to ' + enddate, fontsize=16)
plt.text(-10000,10000,'Geospatial Extent \n' + west + '°W\n' + east + '°E\n' + north + '°N\n' + south + '°S\n' + 'Center = ' + '°', fontsize = 12)

# Save image
plt.savefig('test.png', bbox_inches='tight', pad_inches=0)
