# Instagram API usage

The instagram API is really a pain. Here, I tried to document the needed steps to get to an image from a feed. In short:
* Create a "facebook app"
* Configure "Instagram Basic Display"
* Add test user to Instagram "app"
* Get authentication code (only with a browser - no automatic here)
* Convert authentication code into a short-live token
* Convert short-live token into a long-live token (from 1h to 60days)
* Refresh a long-live token (optional - only if expire date is near)
* Request the feed from instagram with it's posts

In detail:

## Preperations

First of all follow steps 1-3 of the officially documenation: 
https://developers.facebook.com/docs/instagram-basic-display-api/getting-started

Write down this values (needed in the next steps):
* Instagram App ID (client id)
* Instagram App secret
* Return/Redirect URL (important: every character counts - even the trailing slash!)


## The interface


### 1. Get authentication code (Browser needed for this action)

Usage in browser like:
`https://api.instagram.com/oauth/authorize?client_id=123456&redirect_uri=https://www.in2code.de/&scope=user_profile,user_media&response_type=code`

This will redirect the browser to something like:
`https://www.in2code.de/?code=codeABC1234#_`

The extracted code is the GET param - so:
`codeABC1234`

Note: Code is needed for next step


### 2. Get short-live access Token (valid for 60min)

Usage via CURL on cli:
`curl -X POST https://api.instagram.com/oauth/access_token -F client_id=123456 -F client_secret=123secret -F grant_type=authorization_code -F redirect_uri=https://www.in2code.de/ -F code=codeABC1234`
 
Example return value as JSON:
`{"access_token": "token1234567", "user_id": 98765432}`

Note: User ID is important for step 4, access_token for the next step


### 3. Change short-live to long-live Token (valid for 60days)

Usage via CURL on cli:
`curl -i -X GET "https://graph.instagram.com/access_token?grant_type=ig_exchange_token&client_secret=123secret&access_token=token1234567"`

Example return value as JSON:
`{"access_token":"token987654","token_type":"bearer","expires_in":5184000}`

Note: access_token is renewed here


### 3a. Refresh a long-live token (needs a valid long-live token)

Usage via CURL on cli:
`curl -i -X GET "https://graph.instagram.com/refresh_access_token?grant_type=ig_refresh_token&access_token=token987654"`

Example return value as JSON:
`{"access_token":"newtoken987654","token_type":"bearer","expires_in":5184000}`

Note: access_token is renewed here


### 4. Request a feed from instagram by user

Usage via CURL on cli:
`curl -X GET 'https://graph.instagram.com/98765432/media/?fields=id,username,media,caption,id,media_type,media_url,permalink,thumbnail_url,timestamp,username,children&access_token=newtoken987654'`

Example return value as JSON:
`{"data":[{"id":"17883083885121876","username":"in2code.de","caption":"Nice image","media_type":"IMAGE","media_url":"https:\/\/scontent-frt3-1.cdninstagram.com...","permalink":"https:\/\/www.instagram.com\/p\/CMOnBpsM0Rn\/","timestamp":"2021-03-12T14:27:57+0000"},{...}],"paging":{...}}`
