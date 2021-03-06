#!/usr/bin/python3.4
import MySQLdb
import json
def insert(user, thread, notitype):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""insert into notifications values(%s, %s, %s, %s, default, default)""", (user, thread, notitype, 0)); 
	conn.commit()
	conn.close()
	
def getNotis(user):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""select id, thread, type, seen from notifications where user=%s order by time desc""", (user,));
	result = cursor.fetchall()
	rows = []
	for row in result:
		new_dict = {"id": row[0], "thread": row[1], "type":row[2], "seen":row[3]}
		rows.append(new_dict)
	rows_dict = dict(enumerate(rows))
	rows_dict["count"] = len(rows);
	rows_json = json.dumps(rows_dict);
	return rows_json;

def deleteNoti(notiID):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""delete from notifications where id=%s""", (notiID,)); 
	conn.commit()
	conn.close()
	
def setseen(notiID):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""update notifications set seen=1 where id=%s""", (notiID,));
	conn.commit()
	conn.close()

def getNotiAbove(date, user):
	conn = MySQLdb.connect(host='127.0.0.1', user='root', passwd='root', db='KnowledgeLevelPredictor')
	cursor = conn.cursor()
	cursor.execute("""select id, thread, type, seen, time from notifications where user=%s and time>%s""", (user, date))
	row = cursor.fetchone()
	if row:
		row_dict = {"id": row[0], "thread": row[1], "type":row[2], "seen":row[3], "time":row[4].strftime("%Y-%m-%d %H:%M:%S")} 
		return json.dumps(row_dict)
	return {}
