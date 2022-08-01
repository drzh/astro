#!/usr/bin/python3

import matplotlib as mpl
mpl.use('Agg')

import sys
import numpy as np
import matplotlib.pyplot as plt
import pyart
from mpl_toolkits.basemap import Basemap
from netCDF4 import num2date
import pytz
import warnings

#suppress deprecation warnings
warnings.simplefilter("ignore", category=DeprecationWarning)

_, minlon, minlat, maxlon, maxlat, psize, fsite, fi, fo = sys.argv

minlon = float(minlon)
minlat = float(minlat)
maxlon = float(maxlon)
maxlat = float(maxlat)
psize = float(psize)

fontsz = 8 * psize

radar = pyart.io.read_nexrad_archive(fi)

display = pyart.graph.RadarMapDisplay(radar)
x,y = display._get_x_y(0,True,None)
lat0 = display.loc[0]
lon0 = display.loc[1]

#get the date and time from the radar file for plot enhancement
# time = radar.time['units'].split(' ')[-1].split('T')
sweep = 0
index_at_start = radar.sweep_start_ray_index['data'][sweep]
time_at_start_of_radar = num2date(radar.time['data'][index_at_start],
                                  radar.time['units'])
pacific = pytz.timezone('America/Chicago')
local_time = pacific.fromutc(time_at_start_of_radar)
# fancy_date_string = local_time.strftime('%A %B %d at %I:%M %p %Z')
fancy_date_string = local_time.strftime('%a %m/%d %H:%M %Z')

#set up a 1x1 figure for plotting
fig, axes = plt.subplots(nrows=1,ncols=1,figsize=(8*psize,6*psize),dpi=100)

m = Basemap(projection='lcc',lon_0=lon0,lat_0=lat0,
            llcrnrlat=minlat,llcrnrlon=minlon,
            urcrnrlat=maxlat,urcrnrlon=maxlon,
            resolution='h')

#get the plotting grid into lat/lon coordinates
x0,y0 = m(lon0,lat0)
glons,glats = m((x0+x*1000.), (y0+y*1000.),inverse=True)

#read in the lowest scan angle reflectivity field in the NEXRAD file 
refl = np.squeeze(radar.get_field(sweep=0,field_name='reflectivity'))

#set up the plotting parameters (NWSReflectivity colormap, contour levels,
# and colorbar tick labels)
cmap = 'pyart_NWSRef'
levs = np.linspace(0,80,41,endpoint=True)
ticks = np.linspace(0,80,9,endpoint=True)
label = 'Radar Reflectivity Factor ($\mathsf{dBZ}$)'

#define the plot axis to the be axis defined above
ax = axes

#normalize the colormap based on the levels provided above
norm = mpl.colors.BoundaryNorm(levs,256)

#create a colormesh of the reflectivity using with the plot settings defined above
cs = m.pcolormesh(glons,glats,refl,norm=norm,cmap=cmap,ax=ax,latlon=True)

#add geographic boundaries and lat/lon labels
m.drawparallels(np.arange(20,70,0.5),labels=[1,0,0,0],fontsize=fontsz,
                color='k',ax=ax,linewidth=0.001)
m.drawmeridians(np.arange(-150,-50,1),labels=[0,0,0,1],fontsize=fontsz,
               color='k',ax=ax,linewidth=0.001)
m.drawcounties(linewidth=0.1*psize,color='gray',ax=ax)
m.drawstates(linewidth=0.5*psize,color='k',ax=ax)
m.drawcoastlines(linewidth=0.5*psize,color='k',ax=ax)

# add roads
m.readshapefile('shapefiles/tl_2016_us_primaryroads','tl_2016_us_primaryroads',linewidth=0.2*psize,color='darkslategray')

#mark the radar location with a black dot
m.scatter(lon0,lat0,marker='o',s=10,color='k',ax=ax,latlon=True)

# Plot site markers
if (fsite != '-'):
    with open(fsite) as site:
        cont = site.readlines()
        for line in cont:
            name, lon, lat = line.strip('\n').split('\t')
            lons, lats = m(float(lon), float(lat))
            m.plot(lons, lats, '+', markersize=3*psize, color='k')
            
#add the colorbar axes and create the colorbar based on the settings above
# cax = fig.add_axes([0.075,0.075,0.85,0.025])
# cbar = plt.colorbar(cs,ticks=ticks,norm=norm,cax=cax,orientation='horizontal')
cax = fig.add_axes([0.9,0.095,0.02,0.80])
cbar = plt.colorbar(cs,ticks=ticks,norm=norm,cax=cax,orientation='vertical')
cbar.set_label(label,fontsize=fontsz)
cbar.ax.tick_params(labelsize=fontsz)

#add a title to the figure
fig.text(0.5, 0.89, fancy_date_string, horizontalalignment='center',fontsize=fontsz)

plt.savefig(fo, bbox_inches='tight', pad_inches=0)
