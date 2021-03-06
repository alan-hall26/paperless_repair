
import re

shorts_regex = re.compile(r'^Shorts Report for "(?P<Test_Name>.+)".$')
shorts_end_regex = re.compile(r'End, \d+ Problems? Reported')

#This function when given a location
#of a test report 
#(which is the data which is normally
#spat out of the printer)
#converts it into a dictionary.
def extract_report_info(dir):

    if not dir.exists():
        return {}

    Shorts_Test = False
    Test_Name   = ""
    
    report_dict = {}
    
    shorts_list = []

    with dir.open("r") as report_data:
        for line in report_data:
            line = line.strip()  
            
            if not line:
                continue
                
            if line.startswith("Shorts Report for"):
                re_shorts_name = shorts_regex.match(line)
                if re_shorts_name:
                    Test_Name   = re_shorts_name.group("Test_Name")
                    Shorts_Test = True
                    shorts_list = []
                continue
            
            if Shorts_Test:
                shorts_list.append(line)
            
            
            
            
                if shorts_end_regex.search(line):
                    Shorts_Test = False
                    report_dict[Test_Name] = "\n".join(l for l in shorts_list)
                    continue
                     
            if not line:
                continue
            

            


    return report_dict