#!/usr/bin/env python3

import serial
import time
import os
import sys
from string import Template
import MySQLdb

mylat = 0
mylon = 0

con = MySQLdb.connect("localhost","root","linux","gsv")
db = con.cursor()

if os.geteuid() != 0: # Source: https://gist.github.com/davejamesmiller/1965559
    os.execvp("sudo", ["sudo"] + sys.argv)

ser = serial.Serial('/dev/ttyAMA0',9600,timeout=1) # Open Serial port

counter = 0 # Used to generate a html page every 10s

def readString():
	while 1:
		while ser.read().decode("utf-8") != '$': # Wait for the begging of the string
        		pass # Do nothing
		line = ser.readline().decode("utf-8") # Read the entire string
		return line

def getTime(string,format,returnFormat):
	return time.strftime(returnFormat, time.strptime(string, format)) # Convert date and time to a nice printable format

def getLatLng(latString,lngString):
	lat = latString[:2].lstrip('0') + "." + "%.7s" % str(float(latString[2:])*1.0/60.0).lstrip("0.")
	lng = lngString[:3].lstrip('0') + "." + "%.7s" % str(float(lngString[3:])*1.0/60.0).lstrip("0.")
	return lat,lng

def checksum(line):
	checkString = line.partition("*")
	checksum = 0
	for c in checkString[0]:
		checksum ^= ord(c)

	try: # Just to make sure
		inputChecksum = int(checkString[2].rstrip(), 16);
	except:
		print("Error in string")
		return False
	
	if checksum == inputChecksum:
		return True
	else:
		print("=====================================================================================")
		print("===================================Checksum error!===================================")
		print("=====================================================================================")
		print(hex(checksum), "!=", hex(inputChecksum))
		return False

def printGGA(lines):	
	print("========================================GGA========================================")
	#print(lines, '\n')
	print("Fix taken at:", getTime(lines[1], "%H%M%S.%f", "%H:%M:%S"), "UTC")
	latlng = getLatLng(lines[2],lines[4])
	print("Lat,Long: ", latlng[0], lines[3], ", ", latlng[1], lines[5])
	print("Fix quality (0 = invalid, 1 = fix, 2..8):", lines[6])
	print("Satellites:", lines[7].lstrip("0"))
	print("Horizontal dilution:", lines[8])
	print("Altitude: ", lines[9], lines[10])
	print("Height of geoid: ", lines[11],lines[12])
	print("Time in seconds since last DGPS update:", lines[13])
	print("DGPS station ID number:", lines[14].partition("*")[0])
	return

def updateLocation():
	global mylat, mylon
	db.execute("update myloc set id=%s, mylat=%s, mylon=%s where id=1",(1,mylat,mylon))
	db.commit()

while 1:
	line = readString()
	lines = line.split(",")
	if checksum(line):
		if lines[0] == "GPGGA":
			printGGA(lines)
			pass
