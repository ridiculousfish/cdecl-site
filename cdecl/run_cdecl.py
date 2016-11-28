#!/usr/bin/env python

import os
import subprocess
import sys

SYNTAX_ERROR="syntax error"

def exe_path():
    root = os.environ.get('LAMBDA_TASK_ROOT', '.')
    plat = 'darwin' if sys.platform == 'darwin' else 'linux'
    return root + "/exe/cdecl_" + plat

def lambda_handler(event, context):
    query = event['queryStringParameters'].get('q')
    if not query:
        body = 'Bad query: ' + str(event['queryStringParameters'])
        statusCode = 400
    else:
        path = exe_path()
        body = run_3_cdecl(query, path)
        statusCode = 200
    return {'statusCode': statusCode, 'headers': {}, 'body': body }

def run_3_cdecl(query, path):
    queries = [query, 'explain ' + query, 'declare ' + query]
    proc = subprocess.Popen([path], stdin=subprocess.PIPE, stdout=subprocess.PIPE)
    outdata, _ = proc.communicate('\n'.join(queries))
    for line in outdata.split('\n'):
        if line and line != SYNTAX_ERROR:
            return line
    else:
        return SYNTAX_ERROR

if __name__ == '__main__':
    for arg in sys.argv[1:]:
        print run_3_cdecl(arg, exe_path())
