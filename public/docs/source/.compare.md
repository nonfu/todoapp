---
title: API Reference

language_tabs:
- bash

- javascript


includes:

search: true

toc_footers:
- <a href='http://github.com/mpociot/documentarian'>Documentation Powered by Documentarian</a>

---
<!-- START_INFO -->
# Info

Welcome to the generated API reference.
[Get Postman Collection](http://todo.test/docs/collection.json)

<!-- END_INFO -->

#任务管理
<!-- START_1ba81f0d6f6938a5ba074f02eba8d3b7 -->
## Task List

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```bash
curl -X GET -G "http://todo.test/dingoapi/tasks" 
```

```javascript
const url = new URL("http://todo.test/dingoapi/tasks");

    let params = {
            "page": "1",
            "limit": "8",
        };
    Object.keys(params).forEach(key => url.searchParams.append(key, params[key]));

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):


```json
{
    "data": [
        {
            "id": 1,
            "text": "Test Task 1",
            "completed": "no",
            "link": "http:\/\/todo.test\/dingoapi\/task\/1"
        }
    ],
    "meta": {
        "pagination": {
            "total": 4,
            "count": 1,
            "per_page": 1,
            "current_page": 1,
            "total_pages": 4,
            "links": {
                "next": "http:\/\/todo.test\/dingoapi\/tasks?page=2"
            }
        }
    }
}
```

### HTTP Request
`GET /dingoapi/tasks`

#### Query Parameters

Parameter | Status | Description
--------- | ------- | ------- | -----------
    page |  required  | The number of the page.
    limit |  required  | Task items per page.Example:10

<!-- END_1ba81f0d6f6938a5ba074f02eba8d3b7 -->

<!-- START_15e8e4eeaa5a2b603998544bf17cd01f -->
## New Task

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```bash
curl -X POST "http://todo.test/dingoapi/tasks" \
    -H "Content-Type: application/json" \
    -d '{"text":"Test Task","is_completed":false}'

```

```javascript
const url = new URL("http://todo.test/dingoapi/tasks");

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
}

let body = {
    "text": "Test Task",
    "is_completed": false
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):


```json
{
    "data": {
        "id": 1,
        "text": "Test Task 1",
        "completed": "no",
        "link": "http:\/\/todo.test\/dingoapi\/task\/1"
    }
}
```

### HTTP Request
`POST /dingoapi/tasks`

#### Body Parameters

Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    text | string |  required  | the body of task.
    is_completed | boolean |  required  | task is completed or not.

<!-- END_15e8e4eeaa5a2b603998544bf17cd01f -->

<!-- START_6da1d67bea29df43a9fe53c1e57f899c -->
## Task Detail

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```bash
curl -X GET -G "http://todo.test/dingoapi/tasks/1" 
```

```javascript
const url = new URL("http://todo.test/dingoapi/tasks/1");

    let params = {
            "id": "1",
        };
    Object.keys(params).forEach(key => url.searchParams.append(key, params[key]));

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):


```json
{
    "data": {
        "id": 1,
        "text": "Test Task 1",
        "completed": "no",
        "link": "http:\/\/todo.test\/dingoapi\/task\/1"
    }
}
```
> Example response (404):


```json
{
    "message": "404 not found",
    "status_code": 404
}
```

### HTTP Request
`GET /dingoapi/tasks/{task}`

#### Query Parameters

Parameter | Status | Description
--------- | ------- | ------- | -----------
    id |  required  | The id of the task.

<!-- END_6da1d67bea29df43a9fe53c1e57f899c -->

<!-- START_a214c673e4ff2890b63757b141a1df64 -->
## Update Task

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```bash
curl -X PUT "http://todo.test/dingoapi/tasks/1" \
    -H "Content-Type: application/json" \
    -d '{"text":"Test Task","is_completed":true}'

```

```javascript
const url = new URL("http://todo.test/dingoapi/tasks/1");

    let params = {
            "id": "1",
        };
    Object.keys(params).forEach(key => url.searchParams.append(key, params[key]));

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
}

let body = {
    "text": "Test Task",
    "is_completed": true
}

fetch(url, {
    method: "PUT",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):


```json
{
    "data": {
        "id": 1,
        "text": "Test Task 1",
        "completed": "no",
        "link": "http:\/\/todo.test\/dingoapi\/task\/1"
    }
}
```
> Example response (404):


```json
{
    "message": "404 not found",
    "status_code": 404
}
```

### HTTP Request
`PUT /dingoapi/tasks/{task}`

`PATCH /dingoapi/tasks/{task}`

#### Body Parameters

Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    text | string |  required  | the body of task.
    is_completed | boolean |  required  | task is completed or not.
#### Query Parameters

Parameter | Status | Description
--------- | ------- | ------- | -----------
    id |  required  | The id of the task.

<!-- END_a214c673e4ff2890b63757b141a1df64 -->

<!-- START_2c8f900020c95d693e91f08a7bc23d2b -->
## Delete Task

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```bash
curl -X DELETE "http://todo.test/dingoapi/tasks/1" 
```

```javascript
const url = new URL("http://todo.test/dingoapi/tasks/1");

    let params = {
            "id": "1",
        };
    Object.keys(params).forEach(key => url.searchParams.append(key, params[key]));

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):


```json
{
    "message": "Task deleted"
}
```
> Example response (404):


```json
{
    "message": "404 not found",
    "status_code": 404
}
```

### HTTP Request
`DELETE /dingoapi/tasks/{task}`

#### Query Parameters

Parameter | Status | Description
--------- | ------- | ------- | -----------
    id |  required  | The id of the task.

<!-- END_2c8f900020c95d693e91f08a7bc23d2b -->

#用户认证
<!-- START_8f7432457502b0668e2ec8596937d33b -->
## 获取 Json Web Token

> Example request:

```bash
curl -X POST "http://todo.test/dingoapi/user/auth" \
    -H "Content-Type: application/json" \
    -d '{"email":"ipsam","password":"qui"}'

```

```javascript
const url = new URL("http://todo.test/dingoapi/user/auth");

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
}

let body = {
    "email": "ipsam",
    "password": "qui"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):


```json
{
    "token": "Access Token"
}
```

### HTTP Request
`POST /dingoapi/user/auth`

#### Body Parameters

Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    email | string |  required  | email
    password | string |  required  | password

<!-- END_8f7432457502b0668e2ec8596937d33b -->

<!-- START_59401a37114b52d1510a56b68951bcd9 -->
## 通过 OAuth 密码授权获取令牌

> Example request:

```bash
curl -X POST "http://todo.test/dingoapi/user/token" \
    -H "Content-Type: application/json" \
    -d '{"email":"omnis","password":"inventore"}'

```

```javascript
const url = new URL("http://todo.test/dingoapi/user/token");

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
}

let body = {
    "email": "omnis",
    "password": "inventore"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):


```json
{
    "token_type": "Bearer",
    "expires_in": 31622400,
    "access_token": "Access Token Value",
    "refresh_token": "Refresh Token Value"
}
```

### HTTP Request
`POST /dingoapi/user/token`

#### Body Parameters

Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    email | string |  required  | email
    password | string |  required  | password

<!-- END_59401a37114b52d1510a56b68951bcd9 -->


