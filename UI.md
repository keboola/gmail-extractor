# Configuring Gmail Extractor in Keboola Connection

After adding of new extractor and authorizing it to access your account there's a place for
configuration:

Sample:

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

*Tip: Google provides big [manual on Gmail's Help site](https://support.google.com/mail/answer/7190?hl=en) which can help you with query definition.*

Explanation:

- First query will fetch all e-mails matching `from:some.address@example.com` and store only `From`,
`To` and `Subject` headers
- Second query will fetch all e-mails matching `from:another.address@example.com` and all headers
which belongs to filtered messages.


## Important notes

- try to be as much specific as possible while defining queries, it speeds up extraction
- by default, application won't fetch messages in your `spam` and `trash` folders
- inbox is accessed with readonly access `https://www.googleapis.com/auth/gmail.readonly`
- second run with same queries will automatically add `after` part to query, which prevents
application from fetching same e-mail all the time
