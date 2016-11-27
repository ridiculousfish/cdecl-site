#!/usr/bin/env python

import subprocess
import sys

SYNTAX_ERROR="syntax error"

def lambda_handler(event, context):
    query = event['q']
    if not query:
        return 'Bad query'
    return run_3_cdecl(query)

def run_3_cdecl(query):
    queries = [query, 'explain ' + query, 'declare ' + query]
    path = "./exe/cdecl_" + ('darwin' if sys.platform == 'darwin' else 'linux')
    proc = subprocess.Popen([path], stdin=subprocess.PIPE, stdout=subprocess.PIPE)
    outdata, _ = proc.communicate('\n'.join(queries))
    for line in outdata.split('\n'):
        if line and line != SYNTAX_ERROR:
            return line
    else:
        return SYNTAX_ERROR

if __name__ == '__main__':
    for arg in sys.argv[1:]:
        print run_3_cdecl(arg)
