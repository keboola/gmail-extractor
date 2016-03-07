# Gmail Extractor [WIP]

[![Build Status](https://travis-ci.org/keboola/gmail-extractor.svg?branch=master)](https://travis-ci.org/keboola/gmail-extractor)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](https://github.com/keboola/gmail-extractor/blob/master/LICENSE.md)

Docker application for extracting data from Gmail.

## Configuration


### Sample

```json
{
    "parameters": {
        "queries": [
            {
                "query": "from:some.address@example.com"
            },
            {
                "query": "from:another.address@example.com"
            }
        ]
    }
}
```

## License

MIT. See lincense file.
