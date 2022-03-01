#!/usr/bin/python3

import sys
import requests
import json
import base64
import ddddocr

ocr = ddddocr.DdddOcr()
sum = 100
count = 100
right = 0

if sys.argv[1] :
    sum = int(sys.argv[1])
    count = int(sys.argv[1])

while (count > 0):
    response = requests.get('https://10.5.6.99/admin/test/', verify=False)
    #print(response.content)
    image = base64.b64decode(json.loads(response.content)['img'])
    code = json.loads(response.content)['code']

    # ocr = ddddocr.DdddOcr(old=True)
    res = ocr.classification(image)
    rescode = res.upper()
    
    # print(res, end="")
    if rescode == code :
        right = right + 1
        print(rescode, '--', code)
    
    count = count - 1

rate = right/sum
print('sum: ', sum, 'right: ', right, 'Correct-Rate:', rate)
