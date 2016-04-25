#!/usr/bin/python3

from i2clibraries import i2c_hmc5883l
from time import sleep
import os

hmc = i2c_hmc5883l.i2c_hmc5883l(1)

hmc.setContinuousMode()
hmc.setDeclination(0,-43)

while True:
	os.system("clear")
	print(hmc)
	sleep(0.2)
