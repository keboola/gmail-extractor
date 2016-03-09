# Configuring Gmail Extractor in Keboola Connection

Configuration sample:

```json
{
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
```
