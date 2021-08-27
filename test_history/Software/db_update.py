"""
    This Python script is intended to be given a path to a 3070 Job,
    along with some rudamentry information.
    
    This script will then use these inputs to extract data from the 3070
    log files, and store them in a database.
    
    test inputs for debug mode.
    
    #test input for pins fail
    C:/Users/AndrewKenny/Dropbox/Log_Processing/sqlite_learning>python db_update.py "C:/Agilent_ICT/boards/51217" P0003_EP_00_11 ESB0000147 160712113151 3 3
    
    #test input for analog fails
    C:/Users/AndrewKenny/Dropbox/Log_Processing/sqlite_learning>python db_update.py "C:/Agilent_ICT/boards/51217" P0003_EP_00_11 ESA0000073 160712111023 1 1
    
    #test input for testjet fails.
    C:/Users/AndrewKenny/Dropbox/Log_Processing/sqlite_learning>python db_update.py "C:/Agilent_ICT/boards/51217" P0003_EP_00_11 ESB0000145 160712111737 1 3

    #test input for shorts test with Open fails.
    C:/Users/AndrewKenny/Dropbox/Log_Processing/sqlite_learning>python db_update.py "C:/Agilent_ICT/boards/51217" P0003_EP_00_11 ESA0000291 160712105805 3 3

    #test input for shorts test with Short fails.
    C:/Users/AndrewKenny/Dropbox/Log_Processing/sqlite_learning>python db_update.py "C:/Agilent_ICT/boards/51217" P0003_EP_00_11 ESA0000076 160712111023 4 4
    
    #the test for multiple entries.
    python db_update.py "C:/Agilent_ICT/boards/51217" P0003_EP_00_11 ABC0000 1234 1 4
""" 

#Define stdlib imports
import sys, os
import sqlite3
import subprocess

from pathlib import Path
from collections import namedtuple

#define local imports
from sql_lib import create_template_db
from sql_lib import process_btest_data
from sql_lib import process_pins_data
from sql_lib import process_block_data
from sql_lib import process_retest_data
from sql_lib import process_shorts_data

from log_lookup import process_log, get_log_data
from parse_reports import extract_report_info

#define constants.

#if running on development computer, debug = True
debug = False

#The master path where the databases are stored.
if debug:
    DATABASE_ROOT_PATH = Path(r"C:\Users\AndrewKenny\Dropbox\Log_Processing\sqlite_learning\sqllite")
else:
    DATABASE_ROOT_PATH = Path(r"O:\Production\Test_History")

DELETE_LOG_FILE = True





#ensure that correct number of arguments have been provided
try:
    path_arg, board_name, serial_arg, time_stamp, first_board_arg, last_board, Status_list = sys.argv[1:]
    
except ValueError as V:
    print(V)
    print("db_update expects 7 arguments,")
    print("The path to the job dir,")
    print("The serial number of the board being tested,")
    print("The 'name' of the board,")
    print("The number of the first board of this type")
    print("The number of the last board of this type")
    sys.exit(1)

    
first_board = int(first_board_arg)

last_board = int(last_board)
    
BOARD_DIR = Path(path_arg)

board_path = BOARD_DIR / "board"

Status_list = eval(Status_list.replace(".",","))
#print(Status_list, type(Status_list))
#input()
#ensure that the folder exists and 
if not (BOARD_DIR.is_dir() and board_path.exists()):
    print("The directory argument passed to this function")
    print("must be a valid 3070 job directory.")
    sys.exit(1)
    
#get the job id
#(which is the first 3 letters of the serial number.)
job_id = serial_arg[:3]

#see if a database has already been created for this job_id.
DATABASE_SUBDIR = DATABASE_ROOT_PATH / job_id
DATABASE_FILEPATH = DATABASE_SUBDIR / "repair_db.sqlite3"
if not DATABASE_SUBDIR.exists():
    #if it has not, create the folder and
    #the sqlite3 template.
    DATABASE_SUBDIR.mkdir()


    
if not DATABASE_FILEPATH.exists():
        #open the database
    db_conn = sqlite3.connect(DATABASE_FILEPATH._str)
    create_template_db(db_conn)
    
else:
    db_conn = sqlite3.connect(DATABASE_FILEPATH._str)

def update_serial(Board_Position, Serial):
    
    #board 1 has no change to serial number
    #so modifier = 0.
    #The serial number for board 2 will be
    #increase by 1, so board number - 1
    #will equal the serial modifier.
    Serial_Modifier = Board_Position - 1
    
    Job_Code = Serial[:3]
    
    #the length is obtained to allow us to
    #append the correct number of zeros
    Base_Serial_Len = len(Serial[3:])
    Base_Serial_Number = int(Serial[3:])
    
    #by adding the modifier we now have the actual
    #serial number as printed on the board.
    Serial_Number = str(Base_Serial_Number + Serial_Modifier)
    Serial_Number_Len = len(Serial_Number)
    
    #work out how many zeros are required at the starts
    #then update the serial number string.
    Zeros_Required = Base_Serial_Len - Serial_Number_Len
    Serial_Number = Zeros_Required *"0" + Serial_Number
    
    return Job_Code + Serial_Number


#This function is a stub.
def get_fail_count(log_data):
    
    fail_count_tuple = namedtuple("fail_count", ["pins","shorts","analog","testjet",
                                                 "polarity", "digital"])
    
    return fail_count_tuple(-1, -1, -1, -1, -1, -1)
    

#enter the folder where the logging directories are stored.
log_path = (BOARD_DIR / "log_dir") / board_name



#extract the report data.
#this is what is normally printed out.


fname = subprocess.check_output("uname -n", shell=True).decode().strip()


report_path = BOARD_DIR / "debug" / fname
report_data = extract_report_info(report_path)

for i, status in zip(range(first_board, last_board + 1), Status_list):

    if status == 13:
        continue
    serial_offset = (i - first_board) + 1

    #get the serial number for the individual board.
    board_serial = update_serial(serial_offset, serial_arg)

    
    #get the list of files within the folder
    file_list = os.listdir(str(log_path))
  
    #get all the files that start with
    #the relevent serial number
    filtered_file_list = [f for f in file_list if f.startswith(board_serial)]
    #print(filtered_file_list)
    #input()
    
    #further - filter the log files containing the correct timestamp.
    filtered_file_list = [f for f in filtered_file_list if time_stamp in f]
    
    if len(filtered_file_list) == 0:
        print("Log file not found")
        sys.exit()
    if len(filtered_file_list) > 1:
        print("Too many log files")
        sys.exit()
    



    log_file = log_path / filtered_file_list[0]

    #extract the log data from the file.
    batch_data, btest_list = get_log_data(str(log_file))
    
    #if a board fails then passes pins (often after a re-vac)
    #a new set of tests is added to the log file.
    for btest_data, log_data in btest_list.items():
    
        fail_count = get_fail_count(log_data)

        #add data to the btest table
        test_id = process_btest_data(db_conn, time_stamp, batch_data, btest_data, fail_count)

        #loop through the results
        for record, result in log_data.items():
            if record.name == "PF":
                process_pins_data(db_conn, test_id, record, result, report_data)
                continue
    
            if record.name == "TS":
                process_shorts_data(db_conn, test_id, record, result, report_data)
                continue
    
            if record.name == "RETEST":
                process_retest_data(db_conn, test_id, record) 
                continue
            
            if record.name == "BLOCK":
                process_block_data(db_conn, test_id, record, result, report_data)
                continue

    print("board {} complete".format(i))
db_conn.close()









