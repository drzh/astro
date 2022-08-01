#!/usr/bin/python3

# Required libraries
from netCDF4 import Dataset
import sys

_, fi = sys.argv

# Open the file using the NetCDF4 library
nc = Dataset(fi)

# Calculate the image extent required for the reprojection
H = nc.variables['goes_imager_projection'].perspective_point_height
x1 = nc.variables['x_image_bounds'][0] * H
x2 = nc.variables['x_image_bounds'][1] * H
y1 = nc.variables['y_image_bounds'][1] * H
y2 = nc.variables['y_image_bounds'][0] * H 

# Print the results
print("x1 = " + str(x1))
print("y1 = " + str(y1))
print("x2 = " + str(x2))
print("y2 = " + str(y2))
