#!/usr/bin/python

import sys
import time
import RPi.GPIO as GPIO
import os
import threading
import MySQLdb


thread = 0

in1 = 7
in2 = 11
in3 = 13
in4 = 15
speed = 20

GPIO.setmode(GPIO.BOARD)
GPIO.setup(in1, GPIO.OUT)
GPIO.setup(in3, GPIO.OUT)
GPIO.setup(in2, GPIO.OUT)
GPIO.setup(in4, GPIO.OUT)

pwm1 = GPIO.PWM(in1, 100)
pwm2 = GPIO.PWM(in2, 100)
pwm3 = GPIO.PWM(in3, 100)
pwm4 = GPIO.PWM(in4, 100)

pwm1.start(0)
pwm2.start(0)
pwm3.start(0)
pwm4.start(0)

def forward(spd):
	pwm3.ChangeDutyCycle(spd)
	pwm4.ChangeDutyCycle(0)
	pwm1.ChangeDutyCycle(spd)
	pwm2.ChangeDutyCycle(0)
	print "forward --> speed: %s" % spd
	#os.system("gpio readall")

def stop():
	pwm1.ChangeDutyCycle(0)
	pwm2.ChangeDutyCycle(0)
	pwm3.ChangeDutyCycle(0)
	pwm4.ChangeDutyCycle(0)
	print "Stopping..."
	#os.system("gpio readall")

def reverse(spd):
	pwm4.ChangeDutyCycle(spd)
	pwm3.ChangeDutyCycle(0)
	pwm2.ChangeDutyCycle(spd)
	pwm1.ChangeDutyCycle(0)
	print "backward --> speed: %s" % spd
	#os.system("gpio readall")

def turnRight():
	pwm2.ChangeDutyCycle(20)
	pwm1.ChangeDutyCycle(0)
	pwm3.ChangeDutyCycle(20)
	pwm4.ChangeDutyCycle(0)
	print "turning right..."

def turnLeft():
	pwm2.ChangeDutyCycle(0)
	pwm1.ChangeDutyCycle(20)
	pwm3.ChangeDutyCycle(0)
	pwm4.ChangeDutyCycle(20)
	print "turning left..."

def readMotorDatabase():
	cursor.execute("select dir from motor where id=1")
	dir = cursor.fetchone()
	dir = str(dir[0])
	cursor.execute("select stop from motor where id=1")
	#db.commit()
	isStop = cursor.fetchone()
	isStop = isStop[0]
	

def syncMotors():
	while True:
		try:
			db = MySQLdb.connect("localhost","root","linux","gsv")
			cursor = db.cursor()
			cursor.execute("select dir from motor where id=1")
			dir = cursor.fetchone()
			dir = str(dir[0])
			cursor.execute("select stop from motor where id=1")
			isStop = cursor.fetchone()
			isStop = isStop[0]
			print "in Try..."
			print dir
			print isStop
			print "------"
			if(isStop == '1'):
				stop()
			else:
				if(dir == "1"):
					forward(speed)
				if(dir == "2"):
					reverse(speed)
				if(dir == "3"):
					turnLeft()
				if(dir == "4"):
					turnRight()
			cursor.close()
			db.close()
		except :
			print "in Except..."
			GPIO.cleanup()
			raise
		time.sleep(0.25)

def motorController():
	global thread
	thread.start()

thread = threading.Thread(target=syncMotors)

motorController()
