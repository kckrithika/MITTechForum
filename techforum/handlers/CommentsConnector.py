#!/usr/bin/python3.4
import MySQLdb
import json

def insert(thread, author, comment, time):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""insert into QuestionComments values(%s, %s, %s, %s, default)""", (thread, comment, author, time))
	conn.commit()
	conn.close()

def getCommentIDs(thread):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""select id from QuestionComments where thread = %s""", (thread, ))
	result = cursor.fetchall()
	return result;

def getCommentAbove(date):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""select id from QuestionComments where timeOfComment > %s""", (date, ))
	result = cursor.fetchone()
	if(result):
		return result[0]
	else:
		return result;

def getThread(commentid):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""select thread from QuestionComments where id = %s""", (commentid, ))
	result = cursor.fetchone()
	return result[0];

def getAuthor(commentid):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""select author from QuestionComments where id = %s""", (commentid, ))
	result = cursor.fetchone()
	return result[0]

def getComment(commentid):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""select comment from QuestionComments where id = %s""", (commentid, ))
	result = cursor.fetchone()
	return result[0]

def getTimeOfComment(commentid):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""select timeOfComment from QuestionComments where id = %s""", (commentid, ))
	result = cursor.fetchone()
	return result[0]
	
def getComments(thread):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""select * from QuestionComments where thread=%s""",(thread,))
	result = cursor.fetchall()
	rows = []
	for row in result:
		new_dict = {"thread":row[0], "comment":row[1], "author":row[2], "timeOfComment":row[3].strftime("%Y-%m-%d %H:%M:%S"), "id":row[4]}
		rows.append(new_dict)
	rows_dict = dict(enumerate(rows))
	rows_dict["count"] = len(rows);
	rows_json = json.dumps(rows_dict);
	return rows_json;
