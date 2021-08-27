from collections import namedtuple
from itertools import zip_longest, cycle
import re

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

#Because creating a function for each type of record
#was silly and time consuming, The generic function
#below was created. This function would take an annotated
#list of fields, based on the Record Label.
#The annotations are as follows:
#f:xxxxx                  : Floating point data
#i:xxxxx                  : integer data
#s:xxxxx                  : string data
#b:xxxxx                  : boolean data
#x:xxxxx\         : field is a list of given length
RECORD_LOOKUP = {}

RECORD_LOOKUP["A-CAP"] = ["i:test_status", "f:measured_value", "s:subtest_designator", "s:limit_data"]
RECORD_LOOKUP["A-DIO"] = ["i:test_status", "f:measured_value", "s:subtest_designator", "s:limit_data"]
RECORD_LOOKUP["A-FUS"] = ["i:test_status", "f:measured_value", "s:subtest_designator", "s:limit_data"]
RECORD_LOOKUP["A-IND"] = ["i:test_status", "f:measured_value", "s:subtest_designator", "s:limit_data"]
RECORD_LOOKUP["A-JUM"] = ["i:test_status", "f:measured_value", "s:subtest_designator", "s:limit_data"]
RECORD_LOOKUP["A-MEA"] = ["i:test_status", "f:measured_value", "s:subtest_designator", "s:limit_data"]
RECORD_LOOKUP["A-NFE"] = ["i:test_status", "f:measured_value", "s:subtest_designator", "s:limit_data"]
RECORD_LOOKUP["A-NPN"] = ["i:test_status", "f:measured_value", "s:subtest_designator", "s:limit_data"]
RECORD_LOOKUP["A-PFE"] = ["i:test_status", "f:measured_value", "s:subtest_designator", "s:limit_data"]
RECORD_LOOKUP["A-PNP"] = ["i:test_status", "f:measured_value", "s:subtest_designator", "s:limit_data"]
RECORD_LOOKUP["A-POT"] = ["i:test_status", "f:measured_value", "s:subtest_designator", "s:limit_data"]
RECORD_LOOKUP["A-RES"] = ["i:test_status", "f:measured_value", "s:subtest_designator", "s:limit_data"]
RECORD_LOOKUP["A-SWI"] = ["i:test_status", "f:measured_value", "s:subtest_designator", "s:limit_data"]
RECORD_LOOKUP["A-ZEN"] = ["i:test_status", "f:measured_value", "s:subtest_designator", "s:limit_data"]


RECORD_LOOKUP["AID"] = ["s:datetime_detected", "s:subtest_designator"]

RECORD_LOOKUP["ALM"] = ["i:alarm_type", "b:alarm_status", "s:datetime_detected",\
                         "s:board_type", "s:board_type_rev", "i:alarm_limit",\
                         "i:detected_value", "s:controller", "i:testhead number"]

RECORD_LOOKUP["ARRAY"] = ["s:subtest_designator", "i:status", "i:failure_count", "i:samples"]


RECORD_LOOKUP["BATCH"] = ["s:UUT_type", "s:UUT_type_rev", "i:fixture_id",\
                          "i:testhead_number", "s:testhead_type", \
                          "s:process_step", "s:batch_id",\
                          "s:operator_id", "s:controller", "s:testplan_id",\
                          "s:testplan_rev", "s:parent_panel_type",\
                          "s:parent_panel_type_rev", "s:version_label"]

RECORD_LOOKUP["BLOCK"] = ["s:block_designator", "i:test_status"]

RECORD_LOOKUP["BS-CON"] = ["s:test_designator", "i:test_status", "i:shorts_count", "i:opens_count"]

RECORD_LOOKUP["BS-O"] = ["s:first_device_name", "s:first_device_pin",\
                         "s:second_device_name", "s:second_device_pin"]

RECORD_LOOKUP["BS-S"] = ["s:cause"]

RECORD_LOOKUP["BTEST"] = ["s:board_id", "i:test_status", "i:start_datetime",\
                          "i:duration", "b:multiple_test", "s:log_level",\
                          "i:log_set", "b:learning", "b:known_good", "i:end_datetime",\
                          "s:status_qualifier", "i:board_number", "s:parent_panel_id"]

RECORD_LOOKUP["CCHK"] = ["i:test_status", "i:pin_count", "s:device_designator"]

RECORD_LOOKUP["DPIN"] = ["s:device_name", "node_list('s:node_id','s:device_id')"]

RECORD_LOOKUP["D-PLD"] = ["s:file_name", "s:action", "i:action_return_code", \
                          "s:result_message_string", "i:power_program_counter"]

RECORD_LOOKUP["D-T"] = ["i:test_status", "i:test_substatus", "i:failing_vector_number",\
                        "i:pin_count", "s:test_designator", "s:device_pins"]

RECORD_LOOKUP["INDICT"] = ["s:technique", "s:device_list"]

RECORD_LOOKUP["LIM2"] = ["f:high_limit", "f:low_limit"]

RECORD_LOOKUP["LIM3"] = ["f:nominal_value", "f:high_limit", "f:low_limit"]

RECORD_LOOKUP["NETV"] = ["s:datetime", "s:test_system", "s:repair_system", "b:source"]

RECORD_LOOKUP["NODE"] = ["s:node_list"]

RECORD_LOOKUP["PCHK"] = ["i:test_status", "s:test_designator"]

RECORD_LOOKUP["PF"] = ["s:designator", "i:test_status", "i:total_pins"]

RECORD_LOOKUP["PIN"] = ["s:pin_list"]

RECORD_LOOKUP["PRB"] = ["i:test_status", "i:pin_count", "s:test_designator"]

RECORD_LOOKUP["RETEST"] = ["s:datetime"]

RECORD_LOOKUP["RPT"] = ["s:message"]

RECORD_LOOKUP["TJET"] = ["i:test_status", "i:pin_count", "s:test_designator", "s:device_pins"]

RECORD_LOOKUP["TS"] = ["i:test_status", "i:shorts_count", "i:opens_count", \
                       "i:phantoms_count", "s:test_designator"]

RECORD_LOOKUP["TS-D"] = ["destination_list('s:node_id','f:deviation')"]

RECORD_LOOKUP["TS-O"] = ["s:source_node", "s:destination_node", "f:deviation"]

RECORD_LOOKUP["TS-P"] = ["f:deviation"]

RECORD_LOOKUP["TS-S"] = ["i:shorts_count", "i:phantoms_count", "s:source_node"]




    
TYPE_LOOKUP = {"f":lambda x: None if x == None else float(x), 
               "i":int, "s":str, \
               "b": lambda x: "" if x == "" else bool(x)}

#this function is used to add functionality to the TYPE_LOOKUP
#dict (which will still be referenced)
#this function will allow list entries to be processsed.
def type_lookup(field_label):
    
    #only list entries will have ) or ( in them if these 
    #are missing, the standard code can be used.
    if "(" not in field_label and ")" not in field_label:
        dtype_code, field_name = field_label.strip("\\").split(":")
        dtype = TYPE_LOOKUP[dtype_code]
        return dtype, field_name
    
    
    
    
    #the format of a list datatype is:
    #  field_name(dtype1:name1, dtype2:name2,dtypeN:nameN)
    #due to the dynamic nature of this, a function will
    #be made specially for this.
    
    tuple_name = field_label[:field_label.find("(")]
    field_types = eval(field_label[field_label.find("("):])
    

    
    #because the there could be an arbitrary number of values in the
    #list fields, the field types have to be looped indefinatly.
    #This also means that we need the number of field items.
    field_types_loop = cycle(field_types)
    field_types_len  = len(field_types)
    
    
    #number of cycles = (i + (len - 1)) // len
    #
    #work out the adjuster to add to i (before division.
    adjuster = field_types_len - 1
    
    
    
    def list_type(list_data):
        
        typed_data = []
        label_data = []

        for i, (label, data) in enumerate(zip(field_types_loop,list_data),1):
        
            #work out the number of times the field types have cycled.
            cycle_times = (i + adjuster) // field_types_len
            
            dtype_code, field_name = label.strip("\\").split(":")

            dtype = TYPE_LOOKUP[dtype_code]
            


            typed_data.append(dtype(data))

            
            label_data.append(field_name + "_{}".format(cycle_times))
    
  
        data_tuple = namedtuple(tuple_name,label_data)
    
        #given an ordered dict, with an even
        #number of entries, yield
        def get_two(tuple_data):
            
            pair = []
            for value in tuple_data:
                pair.append(get_pure_testname(value))
                if len(pair) == 2:
                    
                    yield tuple(pair)
                    pair = []
    
    
        #create function to represent the data
        #within the database
        @property
        def db_text(self):
            
    
            table_data = get_two(("NODE", "PIN") + self)
            text = "\n".join("{:<35} {}".format(node, pin) for node, pin in table_data)
    
            return text
     
        data_tuple.db_text = db_text
     
        return data_tuple(*typed_data)
    
    return list_type, tuple_name
        
    
    
    
    
    
    
    
    
"""
    @brief Where Possible, generates a named tuple for a log record
    
    @param [in] name   The name of the log record eg A-RES, or A-ZEN
    @param [in] fields A tuple of fields containing log record data.
    @return A named tuple - labeling each data field.
    
    @details Details
""" 
def field_tuple(name, fields):

    if name == "LIM2":
        name = "LIM3"
        fields = [None] + fields


    #get the label data of the record type "name"
    label_data = RECORD_LOOKUP[name]
    tuple_names = []
    tuple_data = []

    
    for label, data in zip_longest(label_data, fields, fillvalue=""):
        
        #dtype, field_name = label.strip("\\").split(":")
        #dtype = TYPE_LOOKUP[dtype]
        dtype, field_name = type_lookup(label)

        tuple_names.append(field_name)
        try:
            typed_field = dtype(data)
        except ValueError:
            typed_field = ""
        finally:
            tuple_data.append(typed_field)

    labelled_data = namedtuple(name.replace("-","_"), tuple_names)
    
    @property
    def name(self):
        return type(self).__name__
    
    labelled_data.name = name
    
    generated_tuple = labelled_data(*tuple_data)

    return generated_tuple



"""
    @brief Extracts raw data from log file line
    
    @param [in] line A single line in the log file
    @return returns a single field from the line
    
    @details Details
""" 
def extract_raw_data(line):

    max_records = None
    #while data contains data,
    #there is more data to extract
    while line:

        if line.startswith("\\"):
            #look for the back slash of a list field
            list_re = re.compile(r"\\(\d+)\|").match(line)
            max_records = int(list_re.groups()[0])

            #update line
            list_start = list_re.end() - 1
            line = line[list_start:]


            #indicate that a list will start
            #by yielding the number given
            #in the list declaration.
            yield max_records
            continue


        #This code is to take into account
        #blank records.
        if line.startswith("|"):
            if len(line) == 1 or line[1] in "|~\\":
                yield ""
                line = line[1:]
                continue
            #This is to account for fields which
            #end in a new line.
            if all(c not in line[1:] for c in "|~\\"):
                yield line[1:]
                line = ""
                continue


        if line.startswith(("@", "|")):
            #extract the name of the record
            field_re = re.compile(r"^\|?(.+?)[\\|~]|").match(line)

            field = field_re.groups()[0]

            yield field.strip("@")

            #extract the rest of the data.
            data_start = field_re.end() - 1
            line = line[data_start:]
            continue

        literal_re = re.compile(r"^~(\d+)\|").match(line)
        if literal_re:
            length = int(literal_re.groups()[0])

            literal_start = literal_re.end()
            iteral_end = literal_start + length

            literal_field = line[literal_start:iteral_end]
            yield literal_field.replace("\v", "{").replace("\a", "\n")

            line = line[iteral_end:]
            continue

"""
    @brief combines records into an anonymous tuple
           Converting it into a proper named one if possible
    
    @param [in] line a line from the log file
    @return A tuple (hopefully named) containing log data
    
    @details Details
""" 
def generic_parse(line):

    line = line.strip("{}")
    fields = []
    is_list = False
    list_data = []

    for i, data in enumerate(extract_raw_data(line)):


        #an integer is yielded at
        #the start of a list giving the size
        if isinstance(data, int):
            is_list = data
            continue

        if i == 0:
            field_name = data
            continue

        #the code to take lists into account.
        if is_list:

            is_list -= 1
            list_data.append(data)

            if is_list == 0:
                is_list = False
                fields.append(tuple(list_data))
            continue

            
        fields.append(data)


    if field_name not in RECORD_LOOKUP:
    
        template_tuple = namedtuple(field_name.replace("-","_"), ["fields"])
        @property
        def name(self):
            return type(self).__name__
    
        template_tuple.name = name
    
        return template_tuple(tuple(fields))

    return field_tuple(field_name, fields)

    
    
    
    