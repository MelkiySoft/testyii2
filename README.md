
Latest Twitter feed API
-----------------------

This is API documentation of a system that returns only the latest tweets from added Twitter
users. Communication is via Rest API with content-type: application/json.

API methods
------------


### Add 
```
GET: {endpoint}/add?id=...&user=..&secret=..
```

Paramaters description:
Id - Random 32-char string used as unique identifier of a request
User - Twitter username of an user that should be added to my list
Secret - Secret parameter to be used as security layer
This method will be used in case I want to add another Twitter user to the feed
In case of successly added user - respond with HTTP status 200 and blank body.
In case of error - respond with error code describe below

### Feed
```
GET: {endpoint}/feed?id=...&secret=..
```

Paramaters description:
Id - Random 32-char string used as unique identifier of a request
Secret - Secret parameter to be used as security layer
Method gets the latest tweets from users that were added and outputs them in specified
format. If user list is empty, response should be blank. If user list is NOT empty, response
needs to look like this:
Success Response
```
{
    "feed": [{
        "user": "",
        "tweet": "",
        "hashtag": ["", "", ""]
    },
    {
        "user": "",
        "tweet": "",
        "hashtag": ["", "", ""]
       }
    ]
}
```
In case of error - respond with error code describe below


### Remove
```
GET: {endpoint}/remove?id=...&user=..&secret=..
```
Paramaters description:
Id - Random 32-char string used as unique identifier of a request
User - Twitter username of an user that should be added to my list
Secret - Secret parameter to be used as security layer
Method removes already added users from the list of users
In case of successly added user - respond with HTTP status 200 and blank body.
In case of error - respond with error code describe below


Secret parameter
----------------

Secret parameter is used as security layer in every request. API must restrict requests that
has wrong secret parameter value and respond with related error.


### Secret calculation

Secret is calculated as hmac_sha1 of concatenated parameter values except secret.
http://php.net/manual/en/function.sha1.php

Example:
```
{endpoint}/add?id=WBYX1TLPRWJ7NSV36LCPP2OZFH6AE6LM&user=elonmusk
sha1(WBYX1TLPRWJ7NSV36LCPP2OZFH6AE6LMelonmusk)=3dfb3e37b62f0f13ceca0df
a87a860b007a29e73
```
```
{endpoint}/add?id=WBYX1TLPRWJ7NSV36LCPP2OZFH6AE6LM&user=elonmusk&secret=
3dfb3e37b62f0f13ceca0dfa87a860b007a29e73
```

Errors
------

In case of any error, API should respond with one of errors below to any API method.

Wrong secret value
```
{"error": "access denied"}
```

Missing parameters
```
{"error": "missing parameter"}
```
Internal error
```
{"error": "internal error"}
```

QUERY EXAMPLES
--------------
```
{endpoint}/api/add?id=12341234123412341234123412341234&user=BarackObama&secret=247e7cd10d6b1e7912f5d0ea24401df1b2e371a6
```
```
{endpoint}/api/feed?id=12341234123412341234123412341234&secret=e4fdc00365cc7b0b700907dded89c981fb0587cb
```
```
{endpoint}/api/remove?id=12341234123412341234123412341234&user=BarackObama&secret=247e7cd10d6b1e7912f5d0ea24401df1b2e371a6
```

Example of the client part for testing
--------------------------------------
```
{endpoint}/client
```
The script itself generates id, and calculates sha1