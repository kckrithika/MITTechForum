#!/usr/bin/python3.4
import MySQLdb
import json
def getRank(username, topic):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""select skill, uncertainty from Rankings where username=%s and topic=%s""", (username, topic))
	result = cursor.fetchone()
	if result:
		return result
	else:
		cursor.execute("""insert into Rankings values(%s, %s, 25.000, 8.333)""", (username, topic))
		conn.commit()
		conn.close()
		return (25.000, 8.333)

def setLevel(username, topic, skill, uncertainty):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""update Rankings set skill=%s, uncertainty=%s where username=%s and topic=%s""", (skill, uncertainty, username, topic))
	conn.commit()
	conn.close()
	
def getInterestedTopic(username):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""select topic, skill, uncertainty from Rankings where username=%s and skill in (select max(skill) from Rankings where username=%s)""", (username, username))
	result = cursor.fetchall()
	rows = []
	for row in result:
		new_dict = {"topic":row[0], "skill":row[1], "uncertainty":row[2]}
		rows.append(new_dict)
	rows_dict = dict(enumerate(rows))
	rows_dict["count"] = len(rows);
	rows_json = json.dumps(rows_dict);
	return rows_json;
