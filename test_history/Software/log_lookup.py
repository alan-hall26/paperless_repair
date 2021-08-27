# -*- coding: utf-8 -*
#assumption 1
#a line either starts with { or }

#assumption 2
#an optional field will (when blank) still
#have its || seperator. However, when the optional
#field is last, instead of:
# {@name|compulsory1|compulsory2|}
# {@name|compulsory1|compulsory2|{lim2|1|2}}
# the program goes with:
# {@name|compulsory1|compulsory2}
# {@name|compulsory1|compulsory2{@lim2|1|2}}
#the exception being if the record finnishes with 2 optional
#fields which are blank - in which case, all required | are given.


#assumption 3
#in theory, a subrecord can start on the same line as
#its super or it can start on the next line.
#however - the log file will be pre-processed to
#prevent this and simplify.

#assumption 3a
#equally, the closing } for that super can be either
#on the end of its last subrecord, or on a new line
#afterwards

#assumption 2
#batch refers to a single runthrough of the testplan
#this includes a single board or panel test.

#assumption 3
#BTEST is the collection of tests for a single board within a panel.

#assumption 4
#a block is a single test.o.
#each subtest has its own scope{}

#assumption 5
#the suspected layout of a block statement:
#{@BLOCK|test_name|number of subtests that failed

#stdlib imports
from tkinter import messagebox as mb
from collections import OrderedDict
import re
import time
import subprocess


#local imports
from parse_records import generic_parse




        
    
class LabelObject(list):

    def unindent(self, number):
        if number > len(self):
            message = "    can not remove {} items\n" \
                      "    from the following list:\n" \
                      "    {}".format(number, self)
            mb.showwarning("Parsing Error", message)
            return
        for _ in range(number):
            self.pop()

    def indent(self, item):
        self.append(item)


class LogDict(OrderedDict):

    #lookup a nested log record

    def lookup(self, section_labels):

        #No section labels means the initial dict is used
        if not section_labels:
            return self


        for i, item in enumerate(section_labels):
            if i == 0:
                result = self[item]
                continue

            result = result[item]

        return result

    def update(self, section_labels, new_record):
        #define a list of record types, which
        #(disregarding the lim2/lim3 subrecords) only have
        #a single level of subrecord.
        #meaning none of their subrecords have their own subrecords.
        special_records = ["BLOCK","PF"]
        
        #Block subrecords get a list instead of an ordered dict
        if new_record.name in special_records:
            sub_dict = self.lookup(section_labels)

            sub_dict[new_record] = []
            return
        
        #if the master record is a block - add this
        #record as a sub entry
        if section_labels and section_labels[-1].name in special_records:
            sub_dict = self.lookup(section_labels)
            sub_dict.append(new_record)
            return
        
        #if this is a limit block - Add the limit data to the
        #analog reading block
        if new_record.name in ["LIM2","LIM3"]:
            #get the block list
            sub_dict = self.lookup(section_labels[:-1])

            
            #add the limit data to the final entry.
            sub_dict[-1] = sub_dict[-1]._replace(limit_data=new_record)
            return
        
        #add the DPIN result to the testjet data.
        if section_labels and section_labels[-1].name in ["TJET","D-T"]:
            #get the block list
            sub_dict = self.lookup(section_labels[:-1])
            
            #add the limit data to the final entry.
            sub_dict[-1] = sub_dict[-1]._replace(device_pins=new_record)
            return
            

        sub_dict = self.lookup(section_labels)
        

        sub_dict[new_record] = OrderedDict()


#literals can cause problems when it comes
#to parsing data. by pre-processing the literal
#so that the generic parsing code does not confuse
#literal text for formating data, the code should be simpler.
#node that this can not tell the difference between
#a literal description eg ~25|  within literal text,
#and a legit literal description.

def pre_process_literals(full_text):
    literal_list = re.compile(r"~(\d+)\|").finditer(full_text)



    for match in literal_list:

        literal_length = int(match.groups()[0])

        start, stop = match.span()[0], match.span()[1] + literal_length

        full_literal = full_text[start:stop]

        #The lack of the following chars
        #means that no changes are needed.
        if "\n" not in full_literal and "{" not in full_literal:
            continue

        #replace the new line and { with unicode chars.
        full_literal = full_literal.replace("\n", "\a").replace("{", "\v")

        full_text = full_text[:start] + full_literal + full_text[stop:]

    return full_text


    
#This is a variation on the pre_process_literals
#function. The difference being, that this function
#will act as a psudo generator iterating over lines
#instead of the whole text. This should save memory
#and processing time.
#def pre_process_literals2(full_text):
#
#    literal_list = re.compile(r"~(\d+)\|").finditer(full_text)
#    
#    def line_generator(file_iterator):
#        
#        for line in file_iterator:
#            
#    
#            #literal flag is True if the line
#            #is part of a literal string.
#            if not literal:
#                #yields normal lines with no literal data.
#                if "~" not in line:
#                    yield line
#                    continue
#                
#                #a '~' could indicate the start of literal data.
#                
#            
#            
#
#            else:
#            
#    
#    
#    
#    for line in full_text.splitlines(keepends=True):
        
        




#the purpose of the  pre_process function
#is to simplify the parsing of a log file.
#it does this by:
#   look for new lines inside literal fields.
#       These newlines will prevent the
#       simple looping over - line by line.
#   making sure each new field (beginning
#   with a '{@' starts on a new line.
#       This means that each line can be
#       considered a seperate field.
def pre_process(file_name):

    with open(file_name, 'r') as raw_log_file:

        #extract entire text file as one string.
        full_text = raw_log_file.read()

    #ensure that there are no formating characters
    #within any literal strings.
    full_text = pre_process_literals(full_text)


    #look for "{" which don't follow a new line
    inline_records = re.compile(r"[^\n][{]").findall(full_text)

    for match in set(inline_records):
        #generate the string which match
        #will be replaced with.
        replace = match.replace("{", "\n{")

        full_text = full_text.replace(match, replace)

    with open("temp", "w") as temp:
        temp.write(full_text)

    return "temp"





def process_log(file_name):

    log_storage = LogDict()

    processed_file_name = pre_process(file_name)

    section_labels = LabelObject()

    with open(processed_file_name, 'r') as raw_log_file:

        #remove the new lines from the file
        log_file = (line.strip("\n") for line in raw_log_file)

        for line in log_file:

            #a line beginning with }
            #should contain only }
            if line.startswith("}"):
                section_labels.unindent(line.count("}"))
                continue

            data = generic_parse(line)


            log_storage.update(section_labels, data)

            section_labels.indent(data)

            #does the line end in some }
            end_braces = re.compile(r"\}+$").search(line)

            #if there are
            if end_braces:
                #apply the unindent
                section_labels.unindent(len(end_braces.group()))

    return log_storage
    
  
def get_log_data(file_name):
    """
    This function will call the process_log function,
    It will then extract the batch data, and the 
    btest dict and return it.
    """
    
    log_storage = process_log(file_name)
    
    batch_info = list(log_storage.keys())[0]
    
    #get the list of btest entries in the log file.
    btest_list = log_storage[batch_info]
    
    return batch_info, btest_list
    
    
    
    
    
