# Gmail Extractor [WIP]

[![Build Status](https://travis-ci.org/keboola/gmail-extractor.svg?branch=master)](https://travis-ci.org/keboola/gmail-extractor)
[![Code Climate](https://codeclimate.com/github/keboola/gmail-extractor/badges/gpa.svg)](https://codeclimate.com/github/keboola/gmail-extractor)
[![Test Coverage](https://codeclimate.com/github/keboola/gmail-extractor/badges/coverage.svg)](https://codeclimate.com/github/keboola/gmail-extractor/coverage)
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

## Output

### `stdout`

Application informs you about extraction process. For example:

```
Queries: 1
Processing query: from:some.address@example.com
Processed results: 100
Processed results: 200
Processed results: 300
Processed results: 340
Done.
```

### Files

After successful extraction there are several files, which contains data about downloaded e-mails.

#### `messages.csv`

Base table of messages:

| id | threadId |
| --- | --- |
| `9876cbd54bd215a6` | `1234abcd2ffdc1d6` |
| `1234abcd2ffdc1d6` | `1234abcd2ffdc1d6` |

*Tip: You can group your messages to conversations with `GROUP BY threadId`*

#### `headers.csv`

Contains all headers:

| messageId | name | value |
| --- | --- | --- |
| `1234abcd2ffdc1d6` | `From` | `News <some.address@example.com>` |
| `1234abcd2ffdc1d6` | `Subject` | `Trending News` |

#### `parts.csv`

All downloaded message parts  

| messageId | partId | mimeType | bodySize | bodyData |
| --- | --- | --- | --- | --- |
| `1234abcd2ffdc1d6` | `0` | `text/plain` | `26` | `Lorem ipsum dolor sit amet` |
| `1234abcd2ffdc1d6` | `1` | `text/html` | `33` | `<p>Lorem ipsum dolor sit amet</p>` |

*Note: Only parts with `text/plain` and `text/html` mime types are downloaded.*

## License

MIT. See license file.
