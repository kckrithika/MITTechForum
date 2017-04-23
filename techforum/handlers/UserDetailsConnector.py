#!usr/bin/python3.4
import MySQLdb
from datetime import datetime

def check(register_no, dob):
	conn = MySQLdb.connect(host='127.0.0.1',user='root',passwd='root',db='KnowledgeLevelPredictor')
	cursor = conn.cursor();
	date = datetime.strptime(dob, "%d/%m/%Y")
	dob = date.strftime("%Y-%m-%d")
	cursor.execute("""select * from UserDetails where register_no = %s and dob = %s""", (register_no, dob))
	if cursor.fetchone():
		return True
	return False

def isRegistered(reg):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""select * from UserDetails where register_no = %s""", (reg, ))
	if cursor.fetchone():
		return True
	else:
		return False

def insert(name, dob, register_no):
	conn = MySQLdb.connect(host='127.0.0.1', user='root',passwd='root',db='KnowledgeLevelPredictor')
	cursor = conn.cursor();
	cursor.execute("""insert into UserDetails values (%s, %s, %s)""", (name, register_no, dob))
	conn.commit()
	conn.close()
	
def getName(register_no):
	conn = MySQLdb.connect(host='127.0.0.1',user='root',passwd='root',db='KnowledgeLevelPredictor')
	cursor = conn.cursor();
	cursor.execute("""select name from UserDetails where register_no = %s""", (register_no, ))
	result = cursor.fetchone()
	return result[0]
