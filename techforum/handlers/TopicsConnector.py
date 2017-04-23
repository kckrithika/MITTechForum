#!usr/bin/python3.4
import MySQLdb
import json

def getAll():
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""select * from topics""")
	result = cursor.fetchall()
	rows = []
	for row in result:
		new_dict = {"id":row[0], "topic":row[1]}
		rows.append(new_dict)
	rows_dict = dict(enumerate(rows))
	rows_dict["count"] = len(rows);
	rows_json = json.dumps(rows_dict);
	return rows_json;

def get(topicID):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""select name from topics where id=%s""", (topicID,))
	result = cursor.fetchone()
	if(result):
		new_dict = {"topic":result[0]}
		return json.dumps(new_dict);
	else:
		return {}
		
def insert(topic):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""insert into topics values(default, %s)""", (topic,))
	conn.commit()
	conn.close()
