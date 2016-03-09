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
- inbox is accessed with readonly access `https://www.googleapis.com/auth/gmail.readonly`


## Configuration

- `parameters`
    - `queries`: array of queries used to fetch messages
        - `query`: query to execute, same query format as in the Gmail search box
        - `headers`: (optional) array of header names which you want to save

Example:

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

## Development

Since application is prepared for running in container, you can start development same way.

1. Clone this repository: `git clone git@github.com:keboola/gmail-extractor.git`
2. Change directory `cd gmail-extractor`
3. Build an image: `docker build -t keboola/gmail-extractor .`
4. Create data dir: `mkdir -p data`
5. Create `config.yml` file and place it to your data directory (e.g. `data/config.yml`):

    ```yaml
    parameters:
      queries:
        - query: 'from:some.address@example.com'
          headers:
            - 'Date'
            - 'From'
            - 'Subject'
    authorization:
      oauth_api:
        credentials:
          '#data': '{"access_token":"access-token","token_type":"Bearer","expires_in":3600,"refresh_token":"refresh-token","created":1457455916}'
          'appKey': 'application-key'
          '#appSecret': 'application-secret'
    ```
6. Run container: `docker run -i -t --rm -v "$PWD:/code" -v "$PWD/data:/data" keboola/gmail-extractor bash`
7. Run application `php src/run.php --data=/data/`

### Tests

There are two ways of running tests:

1. From inside of container (preferred way for development)
2. From outside, using `docker-compose` (like TravisCI)

#### 1. Inside

Create bash script `vars` with similar content:

```bash
#!/bin/bash
export ENV_GMAIL_EXTRACTOR_APP_KEY='application-key'
export ENV_GMAIL_EXTRACTOR_APP_SECRET='application-secret'
export ENV_GMAIL_EXTRACTOR_ACCESS_TOKEN_JSON='{"access_token":"access-token","token_type":"Bearer","expires_in":3600,"refresh_token":"refresh-token","created":1457455916}'
```

*Note: These values can be same as those in `config.yml`.*

Source these environment variables and run `tests.sh` command:

```bash
source ./vars
./tests.sh
```

#### 2. Outside

Create similar `vars` file as in previous section.

Source environment variables and run `docker-compose` command:

```bash
source ./vars
docker-compose run \
  --rm \
  -e ENV_GMAIL_EXTRACTOR_APP_KEY=$ENV_GMAIL_EXTRACTOR_APP_KEY \
  -e ENV_GMAIL_EXTRACTOR_APP_SECRET=$ENV_GMAIL_EXTRACTOR_APP_SECRET \
  -e ENV_GMAIL_EXTRACTOR_ACCESS_TOKEN_JSON=$ENV_GMAIL_EXTRACTOR_ACCESS_TOKEN_JSON \
  tests
```

## License

MIT. See license file.
