#!usr/bin/python3.4
import MySQLdb
import json

def insert(thread, answerer, answer, time):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""select * from Answers where thread=%s and answerer = %s""", (thread, answerer))
	result = cursor.fetchone()
	if(result):
		cursor.execute("""update Answers set answer = %s, time=%s where thread=%s and answerer=%s""", (answer, time, thread, answerer))
	else:
		cursor.execute("""insert into Answers values(%s, %s, %s, %s, 0, default)""", (thread, answer, answerer, time))
	conn.commit()
	conn.close()

def getAnswerAbove(date):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""select id from Answers where timeOfAnswer > %s""", (date, ))
	result = cursor.fetchone()
	if(result):
		return result[0]
	else:
		return result;

def getThread(answerid):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""select thread from Answers where id = %s""", (answerid, ))
	result = cursor.fetchone()
	return result[0];

def getAnswerIDs(thread):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""select id from Answers where thread = %s""", (thread, ))
	result = cursor.fetchall()
	return result

def getAnswerer(answerid):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""select answerer from Answers where id = %s""", (answerid, ))
	result = cursor.fetchone()
	return result[0]

def getAnswer(answerid):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""select answer from Answers where id = %s""", (answerid, ))
	result = cursor.fetchone()
	return result[0]

def getTimeOfAnswer(answerid):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""select timeOfAnswer from Answers where id=%s""", (answerid, ))
	result = cursor.fetchone()
	return result[0].strftime("%Y-%m-%d %H:%M:%S")

def setBest(answerid):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""update Answers set bestAnswer=1 where id=%s""", (answerid, ))
	conn.commit()
	conn.close()
	
def isBest(answerid):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""select bestAnswer from Answers where id=%s""", (answerid, ))
	result = cursor.fetchone()
	return result[0];
	
def getAnswers(thread):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""select * from Answers where thread=%s""", (thread,))
	result = cursor.fetchall()
	rows = []
	for row in result:
		new_dict = {"thread":row[0], "answer":row[1], "answerer":row[2], "timeOfAnswer":row[3].strftime("%Y-%m-%d %H:%M:%S"), "bestAnswer":row[4], "id":row[5]}
		rows.append(new_dict)
	rows_dict = dict(enumerate(rows))
	rows_dict["count"] = len(rows);
	rows_json = json.dumps(rows_dict);
	return rows_json;	
