#!/usr/bin/python3.4
import MySQLdb
import RankingsConnector
import json

def insert(question, asker, topic, time):
	askerskill, askeruncertainty = RankingsConnector.getRank(asker, topic)
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""insert into Threads values(default, %s, %s, 0, %s, %s, Null, Null, Null, %s, Null, %s)""", (question, asker, askerskill, askeruncertainty, time, topic))
	conn.commit()
	conn.close()

def getPostAbove(date):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""select questionID from Threads where timeOfPost > %s""", (date, ))
	result = cursor.fetchone()
	if(result):
		return result[0]
	else:
		return result;

def getLastID():
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""select max(questionID) from Threads""")
	result = cursor.fetchone()
	return result[0]
	
def getAsker(questionid):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""select asker from Threads where questionID = %s""", (questionid, ))
	result = cursor.fetchone()
	return result[0];

def getQuestion(questionid):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""select question from Threads where questionID = %s""", (questionid, ))
	result = cursor.fetchone()
	return result[0]

def isClosed(questionid):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""select closed from Threads where questionID = %s""", (questionid, ))
	result = cursor.fetchone()
	return result[0];

def getTopic(questionid):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""select topic from Threads where questionID = %s""", (questionid, ))
	result = cursor.fetchone()
	return result[0];

def getTimeOfPost(questionid):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""select timeOfPost from Threads where questionID = %s""", (questionid, ))
	result = cursor.fetchone()
	return result[0].strftime("%Y-%m-%d %H:%M:%S");

def setclosed(questionid):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""update Threads set closed=1 where questionID=%s""", (questionid, ))
	conn.commit()
	conn.close()

def setAnswerer(thread, answerer):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""update Threads set bestAnswerer=%s where questionID=%s""", (answerer, thread))
	conn.commit()
	conn.close()

def setAnswererLevels(questionid, skill, uncertainty):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""update Threads set answererSkill=%s, answererUncertainty=%s where questionID=%s""", (skill, uncertainty, questionid))
	conn.commit()
	conn.close()
	
def getBelow(skill, topic):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""select questionID, question, asker from Threads where ROUND(askerSkill, 5) < ROUND(%s, 5) and topic = %s and closed = 0""", (skill, topic))
	result = cursor.fetchall()
	rows = []
	for row in result:
		new_dict = {"thread":row[0], "question":row[1], "asker":row[2]}
		rows.append(new_dict)
	rows_dict = dict(enumerate(rows))
	rows_dict["count"] = len(rows);
	rows_json = json.dumps(rows_dict);
	return rows_json;
	
def getThread(thread):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""select * from Threads where questionID=%s""", (thread,))
	row = cursor.fetchone()
	if row:
		if row[10]:
			row[10] = row[10].strftime("%Y-%m-%d %H:%M:%S")
		row_dict = {"id":row[0], "question":row[1], "asker":row[2], "closed":row[3], "askerSkill":row[4], "askerUncertainty":row[5], "bestAnswerer":row[6], "answererSkill":row[7], "answererUncertainty":row[8], "timeOfPost":row[9].strftime("%Y-%m-%d %H:%M:%S"), "timeOfClose":row[10], "topic":row[11]}
		return json.dumps(row_dict)
	return {}

def getLatest():
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""select * from Threads where questionID in (select max(questionID) from Threads)""")
	row = cursor.fetchone()
	if row:
		if row[10]:
			row[10] = row[10].strftime("%Y-%m-%d %H:%M:%S")
		row_dict = {"id":row[0], "question":row[1], "asker":row[2], "closed":row[3], "askerSkill":row[4], "askerUncertainty":row[5], "bestAnswerer":row[6], "answererSkill":row[7], "answererUncertainty":row[8], "timeOfPost":row[9].strftime("%Y-%m-%d %H:%M:%S"), "timeOfClose":row[10], "topic":row[11]}
		return json.dumps(row_dict)
	return {}
