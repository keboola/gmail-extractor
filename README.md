# Gmail Extractor [WIP]

[![Build Status](https://travis-ci.org/keboola/gmail-extractor.svg?branch=master)](https://travis-ci.org/keboola/gmail-extractor)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](https://github.com/keboola/gmail-extractor/blob/master/LICENSE.md)

Docker application for extracting data from Gmail. Application simply iterates through specified
queries and downloads matching e-mails.

Important notes:

- try to be as much specific as possible while defining queries, it speeds up extraction
- by default, application won't fetch messages in your `spam` and `trash` folders
- we're accessing inbox with readonly access `https://www.googleapis.com/auth/gmail.readonly`


## Configuration

- `parameters`
    - `queries`: array of queries used to fetch messages
        - `query`: query to execute, same query format as in the Gmail search box
        - `headers`: (optional) array of header names which you want to save

### Sample

```json
{
    "parameters": {
        "queries": [
            {
                "query": "from:some.address@example.com",
                "headers": [
                    "From",
                    "To",
                    "Subject"
                ]
            },
            {
                "query": "from:another.address@example.com"
            }
        ]
    }
}
```

## License

MIT. See license file.
