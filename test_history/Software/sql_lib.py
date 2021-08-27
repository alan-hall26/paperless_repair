
ERROR_CODES = [(0, 'No Action Taken'),
               (1, 'Replaced Device - Device Missing'),
               (2, 'Replaced Device - Wrong Device Fitted'),
               (3, 'Replaced Device - Device Likely Damaged'),
               (4, 'Incorrect Orientation - Device Replaced'),
               (5, 'Incorrect Orientation - Device Resoldered'),
               (6, 'Removed Short'),
               (7, 'Solder Joint Missing - Device Resoldered'),
               (8, 'Solder Joint Dry - Resoldered Pin'),
               (9, 'Removable Panel Added'),
               (10, 'Board Scrapped'),
               (12, 'New repair code')]



#import standard libraries.
import sqlite3

from collections import namedtuple

btest_cmd = '''CREATE TABLE btest
              (test_id int  PRIMARY KEY NOT NULL, 
               serial int, test_duration int, date text, 
               tester_id text, test_operator text,
               position int, status int, repair_operator text, 
               pins int, shorts int, analog int, testjet int, 
               polarity int, digital int)'''


analog_cmd = '''CREATE TABLE analog
               (analog_id int  PRIMARY KEY NOT NULL, test_id int, 
                test_type text, test_name text, subtest text,
                status int, measured_val real,
                nominal_val real, upper_limit real,
                lower_limit real, report text, view int, comment int)'''

                       
repair_cmd = '''CREATE TABLE repair_table
               (repair_table_id int  PRIMARY KEY NOT NULL, 
                repair_lookup_id int, fail_reference string, 
                fail_id int, comment text)'''


repair_code_cmd = '''CREATE TABLE repair_codes
                    (repair_code_id int  PRIMARY KEY, 
                     description text)'''


testjet_cmd = '''CREATE TABLE testjet_test
               (testjet_id int  PRIMARY KEY NOT NULL, 
                test_id int, test_name text, device text,
                status int, pin_list text, report text, view int, comment int)'''

digital_cmd = '''CREATE TABLE digital_test
                (digital_id int   PRIMARY KEY NOT NULL,
                 test_id int, test_name text, 
                 test_designator, test_status int,
                 test_substatus int, failing_vector int,
                 pin_count int, pin_list text, report text, view int, comment int)'''

shorts_test_cmd = '''CREATE TABLE shorts_test
                    (shorts_test_id int  PRIMARY KEY NOT NULL, 
                     test_id int, test_name text, status int, 
                     shorts_count int, opens_count int,
                     phantoms_count, report text, view int, comment int)'''
                     


shorts_source_cmd = '''CREATE TABLE shorts_source
                      (shorts_source_id int  PRIMARY KEY NOT NULL, 
                       shorts_test_id int, shorts_count int, 
                       phantoms_count int, source_node text)'''


shorts_destination_cmd = '''CREATE TABLE shorts_destination
                    (shorts_destination_id int  PRIMARY KEY NOT NULL,
                     shorts_source_id int, destination_list text)'''
    
phantom_cmd = '''CREATE TABLE phantom
                (phantom_id int  PRIMARY KEY NOT NULL,
                 shorts_source_id int,deviation int)'''     
    
open_nodes_cmd = '''CREATE TABLE open_nodes
                   (open_nodes_id int  PRIMARY KEY NOT NULL, 
                    shorts_test_id int, source text,
                    destination text, deviation real)'''


pin_test_cmd = '''CREATE TABLE pins_test
                 (pin_test_id int  PRIMARY KEY NOT NULL, 
                  test_id int, test_name text, status int, 
                  total_pins int, pin_list text, report text, view int, comment int)'''

retest_cmd = '''CREATE TABLE retest
                 (retest_id int  PRIMARY KEY NOT NULL, 
                  test_id int, timestamp text)'''

#This table is used to create a template sqlite database
#this will be called after a new job is added,
#or after a database is taken away to save space.
def create_template_db(db_conn):

    

    c = db_conn.cursor()
    
    c.execute(btest_cmd)
    
    c.execute(analog_cmd)
      
    c.execute(repair_cmd)
    
    c.execute(repair_code_cmd)
    
    c.executemany('INSERT INTO repair_codes VALUES (?,?)',ERROR_CODES)
    
    c.execute(testjet_cmd)
    
    c.execute(digital_cmd)
    
    c.execute(retest_cmd)
    
    
    c.execute(shorts_test_cmd)
    
    #the shorts go first in the destinations
    #the phantoms go last. the count tells us when
    #the shorts stop, and the phantoms start.
    c.execute(shorts_source_cmd) 
    c.execute(shorts_destination_cmd) 
    c.execute(phantom_cmd) 

    
    c.execute(open_nodes_cmd)
    
    
    c.execute(pin_test_cmd)
    
    
    # Save (commit) the changes
    db_conn.commit()


#This function returns a list of strings.
#each string is an entry in the table created within it.
def get_field_list(table_creation_cmd):
    
    #remove the newlines.
    table_creation_cmd = table_creation_cmd.replace("\n","")


    #first seperate the create table cmd
    #and table name from field data within
    #the brackets.
    #while also removing the closing bracket
    cmd, field_data = table_creation_cmd.replace(")","").split("(")
    

    #now loop through the comma seperated values, adding
    #the field names to a list.
    
    field_list = []
    
    for entry in field_data.split(","):



        field_list.append(entry.split()[0])
    

    return field_list





def update_table(db_conn, table_name, key_field, data_tuple):

    c = db_conn.cursor()
    
    #insecure and bad practice - replace asap.
    max_cmd = "SELECT max({key_field}) FROM {table_name}"
    max_cmd = max_cmd.format(key_field=key_field,table_name=table_name)

    insert_cmd = "INSERT INTO {table_name} VALUES ({args})"
    args = ",".join("?" for c in range(len(data_tuple)))
    insert_cmd =insert_cmd.format(table_name=table_name, args=args)
    
    error =  "UNIQUE constraint failed: {table_name}.{key_field}"
    error = error.format(key_field=key_field,table_name=table_name)
    while True:

        #get the highest primary key from the table
        c.execute(max_cmd) 
    
        #add one (or initialise to zero if table is empty
        last_id = c.fetchone()[0]
        primary_key = 0 if last_id is None else last_id + 1
    
        data_tuple = data_tuple._replace(**{key_field:primary_key})
    
        try:
            c.execute(insert_cmd,data_tuple)
            break
        except sqlite3.IntegrityError as E:
            #on error - pass, to therefore repeat the loop.
            #but only if the error is a repeat unique id.
            #other errors must be raised and caught.
            if str(E)!=error:
                raise
    
    db_conn.commit()
    return primary_key
        

def get_pure_testname(test_name):
    first_pc = test_name.find("%")
    
    #if the text before the pc can be turned into
    #an int, the testname is assumed to start with
    # n%. which is removed.
    try:
        int(test_name[:first_pc])
    except ValueError:
        pass
    else:
        test_name = test_name[first_pc + 1:]
    
    return test_name


#This function is used to ensure that
#view and comment have default values.
#so they do not clutter up the 
#named tuple initialisation.
def custom_named_tuple(name, entries):
    
    func_tuple = namedtuple(name, entries)
    
    #a custom initialisation is only 
    if "view" in entries and "comment" in entries:
    
        def initialiser(**args):

            if "view" not in args:
                args["view"] = 0
        
            if "comment" not in args:
                args["comment"] = 0
        
            return func_tuple(**args)
        
        return initialiser
    
    else:
        return func_tuple
    
    
btest_tuple = namedtuple("btest_tuple", get_field_list(btest_cmd))


def process_btest_data(db_conn, date, batch_data, btest_data, fail_count):
    
    btest_data = btest_tuple( \
                             test_id            = 0,
                             serial             = btest_data.board_id,
                             test_duration      = btest_data.duration,
                             date               = btest_data.start_datetime,
                             tester_id          = batch_data.controller,
                             test_operator      = batch_data.operator_id,
                             position           = btest_data.board_number,
                             status             = btest_data.test_status,
                             repair_operator    = "",
                             pins               = fail_count.pins,
                             shorts             = fail_count.shorts,
                             analog             = fail_count.analog,
                             testjet            = fail_count.testjet,
                             polarity           = fail_count.polarity,
                             digital            = fail_count.digital)

    
    #insert data into table - and get the primary key.
    test_id = update_table(db_conn, "btest", "test_id", btest_data)
    

    return test_id


pins_test_tuple = custom_named_tuple("pins_test_tuple",get_field_list(pin_test_cmd))


def process_pins_data(db_conn, test_id, record, result, report_data):

    test_name = record.designator
    
    if test_name in report_data:
        report_text = report_data[test_name]
    else:
        report_text = ""
    
    #In the event that the test name starts with a n%,
    #this will be stripped - to make the test 
    #board position agnostic.
    test_name = get_pure_testname(test_name)
    
    
    #The documentation states that there will only be one pins
    #entry per PF entry. However in order to be defensive, 
    #this function will iterate accross the subrecord list.
    for pin_record in result:
        pins_test_data = pins_test_tuple(\
                                         pin_test_id = 0,
                                         test_id     = test_id,
                                         test_name   = test_name,
                                         status      = record.test_status,
                                         total_pins  = record.total_pins,
                                         pin_list    = pin_record.pin_list,
                                         report      = report_text)
        
                                         
        #insert data into table - and get the primary key.
        update_table(db_conn, "pins_test", "pin_test_id", pins_test_data)
    

retest_tuple = namedtuple("retest_tuple",get_field_list(retest_cmd))


def process_retest_data(db_conn, test_id, retest_record):
    
    retest_data = retest_tuple(retest_id = 0,
                               test_id   = test_id,
                               timestamp = retest_record.datetime)
    
    update_table(db_conn, "retest", "retest_id", retest_data)

    
analog_tuple = custom_named_tuple("analog_tuple", get_field_list(analog_cmd))
  
testjet_tuple = custom_named_tuple("testjet_tuple",get_field_list(testjet_cmd))

digital_tuple = custom_named_tuple("digital_tuple",get_field_list(digital_cmd))

def process_block_data(db_conn, test_id, record, result, report_data):

    test_name = record.block_designator
    
    if test_name in report_data:
        report_text = report_data[test_name]
    else:
        report_text = ""
    
    #In the event that the test name starts with a n%,
    #this will be stripped - to make the test
    #board position agnostic.
    test_name = get_pure_testname(test_name)
    
    #The documentation states that there will only be one pins
    #entry per PF entry. However in order to be defensive, 
    #this function will iterate accross the subrecord list.
    for sub_record in result:
    
        #print(sub_record)
        #input()
        if sub_record.name.startswith("A_"):
    
            limit_data = sub_record.limit_data

            analog_data = analog_tuple( \
                        analog_id    = 0,
                        test_id      = test_id,
                        test_type    = sub_record.name,
                        test_name    = test_name,
                        subtest      = sub_record.subtest_designator,
                        status       = record.test_status,
                        measured_val = sub_record.measured_value,
                        nominal_val  = limit_data.nominal_value,
                        upper_limit  = limit_data.high_limit,
                        lower_limit  = limit_data.low_limit,
                        report       = report_text)
        
                                         
            #insert data into table - and get the primary key.
            update_table(db_conn, "analog", "analog_id", analog_data)
            continue
    
        if sub_record.name == "TJET":
            

            
            
            #currently the data extraction from the DPIN
            #datatype is very simple. Therefore the pinlist
            #will be stored as text. currently working out
            #how to improve.
            
            pinfail_data = sub_record.device_pins.node_list.db_text

            testjet_data = testjet_tuple( \
                        testjet_id  = 0,
                        test_id     = test_id,
                        test_name   = test_name,
                        device      = get_pure_testname( sub_record.test_designator),
                        status      = record.test_status,
                        pin_list    = pinfail_data,
                        report      = report_text)
                                    
            #insert data into table - and get the primary key.
            update_table(db_conn, "testjet_test", "testjet_id", testjet_data)
            continue
    
        if sub_record.name == "D_T":
            

            
            
            #currently the data extraction from the DPIN
            #datatype is very simple. Therefore the pinlist
            #will be stored as text. currently working out
            #how to improve.
            pinfail_data = sub_record.device_pins.node_list.db_text
            

    
            digital_data = digital_tuple( \
                        digital_id      = 0,
                        test_id         = test_id,
                        test_name       = test_name,
                        test_designator = sub_record.test_designator,
                        test_status     = sub_record.status,
                        test_substatus  = sub_record.substatus,
                        failing_vector  = sub_record.failing_vector_number,
                        pin_count       = sub_record.pin_count,
                        pin_list        = pinfail_data,
                        report          = report_text)
                                    
            #insert data into table - and get the primary key.
            update_table(db_conn, "digital_test", "digital_id", digital_data)
            continue    
    

shorts_test_tuple = custom_named_tuple("shorts_test_tuple",get_field_list(shorts_test_cmd))

open_nodes_tuple = namedtuple("open_nodes_tuple",get_field_list(open_nodes_cmd))

shorts_source_tuple = namedtuple("shorts_source_tuple",get_field_list(shorts_source_cmd))

shorts_destination_tuple = namedtuple("shorts_destination_tuple",get_field_list(shorts_destination_cmd))

phantom_tuple = namedtuple("phantom_tuple",get_field_list(phantom_cmd))

def process_shorts_data(db_conn, test_id, record, result, report_data):

    test_name = record.test_designator
    
    if test_name in report_data:
        report_text = report_data[test_name]
    else:
        report_text = ""
      

    
    #In the event that the test name starts with a n%,
    #this will be stripped - to make the test
    #board position agnostic.
    test_name = get_pure_testname(test_name)
    
    
    shorts_test_data = shorts_test_tuple( \
                                        shorts_test_id = 0,
                                        test_id        = test_id,
                                        test_name      = test_name,
                                        status         = record.test_status,
                                        shorts_count   = record.shorts_count,
                                        opens_count    = record.opens_count,
                                        phantoms_count = record.phantoms_count,
                                        report         = report_text)

    
    shorts_test_id = update_table(db_conn, "shorts_test", "shorts_test_id", shorts_test_data)
    
    #loop through the open and short reports.
    for entry, sub_entry in result.items():
    
        #print(dict(sub_entry))
        #open statements have a blank sub_entry
        if entry.name == "TS_O":

        
            open_node_data = open_nodes_tuple( \
                                            open_nodes_id  = 0,
                                            shorts_test_id = shorts_test_id,
                                            source         = get_pure_testname(entry.source_node),
                                            destination    = get_pure_testname(entry.destination_node),
                                            deviation      = entry.deviation)
    
            update_table(db_conn, "open_nodes", "open_nodes_id", open_node_data)
            continue
    
        if entry.name == "TS_S":
    
            shorts_source_data = shorts_source_tuple( \
                                        shorts_source_id = 0,
                                        shorts_test_id = shorts_test_id,
                                        shorts_count = entry.shorts_count,
                                        phantoms_count = entry.phantoms_count,
                                        source_node = get_pure_testname(entry.source_node))
    
    
            shorts_source_id = update_table(db_conn, "shorts_source", "shorts_source_id", shorts_source_data)
            
            #loop through all of the destination records.
            for destination in sub_entry.keys():
                
                if destination.name == "TS_D":

                    #get the destination_data for processing
                    destination_list = destination.destination_list
    
                    #create a copy to send to the output.
                    destination_copy = destination_list
    
                    #strip the board position information from the nodes.
                    for name, data in zip(destination_list._fields, destination_list):
                        
                        if name.startswith("node_id"):
    
                            stripped_data = get_pure_testname(data)
                            destination_copy = destination_copy._replace(**{name:stripped_data})
    
                    shorts_destination_data = shorts_destination_tuple( \
                                       shorts_destination_id = 0,
                                       shorts_source_id      = shorts_source_id,
                                       destination_list      = str(destination_copy))
            
                    update_table(db_conn, "shorts_destination", "shorts_destination_id", shorts_destination_data)
                    continue
    
                if destination.name == "TS_P":
                    
                    phantom_data = phantom_tuple( \
                                           phantom_id = 0,
                                           shorts_source_id = shorts_source_id,
                                           deviation        = destination.deviation)
    
                    update_table(db_conn, "phantom", "phantom_id", phantom_data)
    
    
            
    
    
    
    
#end of file    