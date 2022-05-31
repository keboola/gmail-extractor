# Gmail Extractor

[![Build Status](https://travis-ci.com/keboola/gmail-extractor.svg?branch=master)](https://travis-ci.com/keboola/gmail-extractor)
[![Code Climate](https://codeclimate.com/github/keboola/gmail-extractor/badges/gpa.svg)](https://codeclimate.com/github/keboola/gmail-extractor)
[![Test Coverage](https://codeclimate.com/github/keboola/gmail-extractor/badges/coverage.svg)](https://codeclimate.com/github/keboola/gmail-extractor/coverage)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](https://github.com/keboola/gmail-extractor/blob/master/LICENSE.md)

Docker application for extracting data from Gmail. Application simply iterates through specified
queries and downloads matching messages.

**[For documentation about configuring in Keboola Connection follow this link](https://help.keboola.com/extractors/communication/gmail/).**

## Configuration

```yaml
parameters:
  queries: # array of queries used to fetch messages
    - query: 'from:some.address@example.com' # query to execute, same query format as in the Gmail search box
      headers: # (optional) array of header names which you want to save
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

## Output

1. `stdout`: Application informs you about extraction process.
2. files: `queries.csv`, `messages.csv`, `headers.csv` and `parts.csv` and related manifest files
(see [https://help.keboola.com/extractors/communication/gmail/#produced-tables](https://help.keboola.com/extractors/communication/gmail/#produced-tables))

### State

State file `state.yml` is saved after first run of application. It helps with query creation by adding
additional date (`after`) part which prevent from downloading same messages.

```yaml
query-dates:
    'from:some.address@example.com': '2016-03-10 13:20:24'
    'from:another.address@example.com': '2016-03-10 13:20:24'
```

## Development

Requirements:

- Docker Engine ~1.10.0
- Docker Compose ~1.6.0

Since application is prepared for running in container, you can start development same way.

1. Clone this repository: `git clone git@github.com:keboola/gmail-extractor.git`
2. Change directory `cd gmail-extractor`
3. Build services: `docker-compose build`
4. Create data dir: `mkdir -p data`
5. Create `config.yml` file and place it to your data directory (`data/config.yml`):
6. Run container: `docker-compose run --rm php`
7. Run application `php src/run.php --data=/data/`

### Tests

Create bash script `set-env.sh` with similar content:

```bash
#!/bin/bash

export ENV_GMAIL_EXTRACTOR_APP_KEY='application-key'
export ENV_GMAIL_EXTRACTOR_APP_SECRET='application-secret'
export ENV_GMAIL_EXTRACTOR_ACCESS_TOKEN_JSON='{"access_token":"access-token","token_type":"Bearer","expires_in":3600,"refresh_token":"refresh-token","created":1457455916}'
```

Run test script in container (environment variables will be sourced automatically):

```console
./tests.sh
```

## License

MIT. See license file.

## License

MIT licensed, see [LICENSE](./LICENSE) file.
