#http://api.bandsintown.com/events/search?location=abq,IL&radius=10&format=xml&date=2011-04-03&app_id=YOUR_APP_ID
#http://tinysong.com/s/Beethoven?format=json&limit=32&key=b95b463bb8b995ba3a66d0b9e187273a
#yyyy-mm-dd,yyyy-mm-dd 
global keyarray
global keyi

keyarray=['fbe7d8b71f7f0512a03df4b1f60ebf0f','b95b463bb8b995ba3a66d0b9e187273a','272d047528bcb414904dee134c863840']
keyi=0

DBlist=[\
		{'name':'abq','location':"Albuquerque,NM",'radius':'25','start_offset':0,'stop_offset':31},
		{'name':'chi','location':"Chicago,IL",'radius':'10','start_offset':0,'stop_offset':7},\
		{'name':'lv','location':"Las%20Vegas,NV",'radius':'30','start_offset':0,'stop_offset':7},\
		{'name':'la','location':"Los%20Angeles,CA",'radius':'30','start_offset':0,'stop_offset':7},\
		{'name':'bos','location':"Boston,MA",'radius':'30','start_offset':0,'stop_offset':7},\
		{'name':'otw','location':"Ottawa,Canada",'radius':'30','start_offset':0,'stop_offset':7},\
		{'name':'nyc','location':"New%20York,NY",'radius':'20','start_offset':0,'stop_offset':7},\
		{'name':'sf','location':"San%20Francisco,CA",'radius':'20','start_offset':0,'stop_offset':7}\
		]

import json, urllib, cgi, sys, pickle, traceback, MySQLdb, datetime, time

def get_bands():
	global eventdict
	global DBcity
	###set date variables, run for a week
	start_date=(datetime.date.today()+datetime.timedelta(days=DBcity['start_offset'])).strftime("%Y-%m-%d")
	print start_date
	end_date=(datetime.date.today()+datetime.timedelta(days=DBcity['stop_offset'])).strftime("%Y-%m-%d")
	print end_date
	eventresponse='null'
	totalresponse=''
	eventslist=[]
	i=1
	while (eventresponse[0:3] != '[ ]'):
		eventsurl='http://api.bandsintown.com/events/search?location='+DBcity['location']+'&radius='+DBcity['radius']+'&format=json&date='+start_date+','+end_date+'&per_page=100&page='+str(i)+'&app_id=BNM'
		print eventsurl
		eventresponse=urllib.urlopen(eventsurl).read()
		totalresponse=totalresponse+eventresponse
		i=i+1
		eventslist=eventslist+json.loads(eventresponse)
	print "done"
	
	print len(eventslist)
	for event in eventslist:
		try:
			for artist in event['artists']:
				name = urllib.quote(artist['name'])
				date=event['datetime'].split("T")[0]
				time=event['datetime'].split("T")[1]
				eventdict[name]={'venue':event['venue'],'datetime':event['datetime'], 'songID':[], 'date':date, 'time':time, 'ticket_url':event['ticket_url']}
		except:
			print "eventlist Error"
	

def get_songs(name):
	global keyarray
	global keyi
	songs=[]
	global eventdict
	global DBcity
	#url='http://tinysong.com/s/Beethoven?format=json&limit=32&key=b95b463bb8b995ba3a66d0b9e187273a'
	#272d047528bcb414904dee134c863840
	songlist=''
	try:
		artisturl='http://tinysong.com/s/'+name.lower()+'?format=json&limit=32&key='+keyarray[keyi]
		songlist=json.loads(urllib.urlopen(artisturl).read())
		#print name.lower().replace("the","").strip()
	except:
		traceback.print_exc()
		sys.exit()
		print "songlist error"
	#songlist=""
	
	
	
	for song in songlist:
		try:
			print urllib.quote(song['ArtistName'].lower()).replace("the","").strip()
			print name.lower().replace("the","").strip()
			if urllib.quote(song['ArtistName'].lower()).replace("the","").strip() == name.lower().replace("the","").strip():
				songs=songs+[str(song['SongID'])]
				print "*****Success*****"
				
		except:
			keyi=min(keyi+1,2)
			print "songlist error"
			print "error: ", song
	return songs


def get_remote_info():
	global DBcity
	eventpkl= open(DBcity['name']+'.pkl','w')
	eventtext=open(DBcity['name']+'.txt','w')
	get_bands()
	check_artistDB()
	#get_songs()
	pickle.dump(eventdict, eventpkl)
	eventtext.write(str(eventdict))

def check_artistDB():
	global eventdict
	global DBcity
	conn = MySQLdb.connect (host = "localhost",
							user = "root",
                           passwd = "cradle69",
                           db = "bnm")
	cursor = conn.cursor ()
	
	
	for name in eventdict.keys():
		keyname=name
		name=name.strip()
		#check if artist exists
		try:
			sql="Select idartists,asongs,refresh_date from artists where idartists = '"+name+"';"
			cursor.execute (sql)
			rows=cursor.fetchall()
		except:
			print "error:",name
			traceback.print_exc()
			rows=''
			
		
		if len(rows)>=1:
			
			#yes, check if refresh date is less than a week
			if (((eval(rows[0][2])-time.time()) < (3*24*60*60)) or (((eval(rows[0][2])-time.time()) < (7*24*60*60)) and (rows[0][1] != '[]'))):
				print "data found for:"+name
				eventdict[keyname]['songID']=rows[0][1]
				#yes, load songs into eventDB
				#eventdict[name]['songID']=rows[0]['songs']
			#no, getsongs from tinysong and update
			else:
				print "refreshing data for:"+name
				songs=get_songs(name)
				eventdict[keyname]['songID']=songs
				#update
				sql="UPDATE artists SET asongs ='"+json.dumps(songs)+"', refresh_date='"+str(time.time())+"'WHERE idartists ='"+name+"';"
				cursor.execute (sql)
		else:
			#no, create artist and get_songs from tinysong
				print "creating entry for:"+name
				songs=get_songs(name)
				eventdict[keyname]['songID']=songs
				sql="INSERT into bnm.artists(idartists, asongs, refresh_date) VALUES('"+name+"','"+json.dumps(songs)+"','"+str(time.time())+"')"
				cursor.execute (sql)
	cursor.close ()
	conn.commit ()
	conn.close ()

	
def	load_DB():
	global eventdict
	global DBcity
	start_date=datetime.date.today().strftime("%Y-%m-%d")
	conn = MySQLdb.connect (host = "localhost",
                           user = "root",
                           passwd = "cradle69",
                           db = "bnm")
	cursor = conn.cursor ()
	
	
	sql="DELETE FROM `bnm`.`event` where (date < '"+start_date+"') = 1 and city='"+DBcity['name']+"';"
	cursor.execute (sql)
	for eventkey in eventdict.keys():
		try:
			event = eventdict[eventkey]
			un_id=eventkey+event['datetime']

			
			sql="INSERT INTO event(idevent,name,venue,datetime,date,time, ticket_url,city) VALUES('"+urllib.quote(un_id)+"','"+eventkey+"','"+urllib.quote(event['venue']['name'])+"','"+event['datetime']+"','"+event['date']+"','"+event['time']+"','"+event['ticket_url']+"','"+DBcity['name']+"');"
			cursor.execute (sql)
		except:
		
			#traceback.print_exc()
			#print event
			#sys.exit()
			error=1
	cursor.close ()
	conn.commit ()
	conn.close ()
	
	
global DBcity
global eventdict
eventdict={}
conn = MySQLdb.connect (host = "localhost",
							user = "root",
                           passwd = "cradle69",
                           db = "bnm")
cursor = conn.cursor ()
#sql="DELETE FROM `bnm`.`artists` where asongs like '[]' ;"
#cursor.execute (sql)
cursor.close ()
conn.commit ()
conn.close ()

for DBcity in DBlist:
	get_remote_info()
	eventdict = pickle.load(open(DBcity['name']+'.pkl','r'))
	load_DB()
print DBlist