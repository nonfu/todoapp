FORMAT: 1A

# TodoApp

# Tasks [/tasks]
Task Resource Controller

## Display a listing of the resource. [GET /tasks{?page,limit}]


+ Parameters
    + page: (integer, optional) - page number
        + Default: 1
    + limit: (integer, optional) - task item number per page
        + Default: 10

+ Request (application/json)
    + Headers

            Authorization: Bearer {API Access Token}

+ Response 200 (application/json)
    + Body

            {
                "data": [
                    {
                        "id": 1,
                        "text": "Test Task 1",
                        "completed": "no",
                        "link": "http://todo.test/dingoapi/task/1"
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
                            "next": "http://todo.test/dingoapi/tasks?page=2"
                        }
                    }
                }
            }

## Store a newly created resource in storage. [POST /tasks]


+ Request (application/json)
    + Headers

            Authorization: Bearer {API Access Token}

    + Attributes
        + text: test task (string, required) - the body of task
        + is_completed (boolean, required) - task is completed or not
    + Body

            {
                "text": "test task",
                "is_completed": 0
            }

+ Response 200 (application/json)

    + Attributes
        + id: 1 (integer, optional) - the id of task
        + text: test task (string, optional) - the body of task
        + completed: no (string, optional) - task is completed or not
        + link: http://todo.test/dingoapi/task/1 (string, optional) - task link
    + Body

            {
                "data": {
                    "id": 1,
                    "text": "Test Task 1",
                    "completed": "no",
                    "link": "http://todo.test/dingoapi/task/1"
                }
            }

## Display the specified resource. [GET /tasks/{id}]


+ Parameters
    + id: (integer, required) - the ID of the task

+ Request (application/json)
    + Headers

            Authorization: Bearer {API Access Token}

+ Response 200 (application/json)

    + Attributes
        + id: 1 (integer, optional) - the id of task
        + text: test task (string, optional) - the body of task
        + completed: no (string, optional) - task is completed or not
        + link: http://todo.test/dingoapi/task/1 (string, optional) - task link
    + Body

            {
                "data": {
                    "id": 1,
                    "text": "Test Task 1",
                    "completed": "no",
                    "link": "http://todo.test/dingoapi/task/1"
                }
            }

+ Response 404 (application/json)
    + Body

            {
                "message": "404 not found",
                "status_code": 404
            }

## Update the specified resource in storage. [PUT /tasks/{id}]


+ Parameters
    + id: (integer, required) - the ID of the task

+ Request (application/json)
    + Headers

            Authorization: Bearer {API Access Token}

    + Attributes
        + text: test task (string, required) - the body of task
        + is_completed: 1 (boolean, required) - task is completed or not
    + Body

            {
                "text": "test task",
                "is_completed": 1
            }

+ Response 200 (application/json)

    + Attributes
        + id: 1 (integer, optional) - the id of task
        + text: test task (string, optional) - the body of task
        + completed: no (string, optional) - task is completed or not
        + link: http://todo.test/dingoapi/task/1 (string, optional) - task link
    + Body

            {
                "data": {
                    "id": 1,
                    "text": "Test Task 1",
                    "completed": "no",
                    "link": "http://todo.test/dingoapi/task/1"
                }
            }

+ Response 404 (application/json)
    + Body

            {
                "message": "404 not found",
                "status_code": 404
            }

## Remove the specified resource from storage. [DELETE /tasks/{id}]


+ Parameters
    + id: (integer, required) - the ID of the task

+ Request (application/json)
    + Headers

            Authorization: Bearer {API Access Token}

+ Response 200 (application/json)
    + Body

            {
                "message": "Task deleted"
            }

+ Response 404 (application/json)
    + Body

            {
                "message": "404 not found",
                "status_code": 404
            }