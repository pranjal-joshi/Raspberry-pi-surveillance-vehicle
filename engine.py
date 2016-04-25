#!/usr/bin/python

import os
from gps import *
from motor import *
from compass import *
import MySQLdb
import schedule

while True:
	line = readString()
	lines = line.split(",")
	if checksum(line):
		if lines[0] == "GPGGA":
			printGGA(lines)
			updateLocation()
			pass
