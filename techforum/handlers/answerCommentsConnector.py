#!usr/bin/python3.4
import MySQLdb
def getRecentID(date):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""select id from AnswerComments where timeOfComment > %s""", (date, ))
	result = cursor.fetchone()
	return result;
	
def getTimeOfComment(commentid):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""select timeOfComment from AnswerComments where id = %s""", (commentid, ))
	result = cursor.fetchone()
	return result[0].strftime("%Y-%m-%d %H:%M:%S");
	
def getThread(commentid):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""select thread from answerCommentsConnector where id = %s""", (commentid, ))
	result = cursor.fetchone()
	return result;
