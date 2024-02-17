import os
import re
import tempfile

import requests
from PyPDF2 import PdfReader
from fastapi import FastAPI
from goose3 import Goose
from pydantic import BaseModel

app = FastAPI()

goose = Goose({
    'browser_user_agent': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:122.0) Gecko/20100101 Firefox/122.0',
    'parser_class': 'soup',
})


class ExtractRequest(BaseModel):
    url: str


@app.post('/html')
async def html(req: ExtractRequest):
    data = goose.extract(req.url)

    title = data.title
    text = re.sub(r'\n{2,}', '\n', data.cleaned_text)

    return {'title': title, 'text': text}


@app.post('/pdf')
async def pdf(req: ExtractRequest):
    file = tempfile.NamedTemporaryFile(delete=False)

    with open(file.name, 'wb') as handle:
        res = requests.get(req.url, stream=True)
        handle.write(res.content)

    reader = PdfReader(file.name)
    os.unlink(file.name)

    title = reader.metadata['/Title']
    text = ''

    for page in reader.pages:
        text += re.sub(r'\s{2,}', ' ', page.extract_text()) + '\n'

    return {'meta': reader.metadata, 'title': title, 'text': text}
