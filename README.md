# Todo List

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/02248dc5c8e543e9adb4001ec0cefad8)](https://www.codacy.com/app/nverjus/TodoList?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=nverjus/TodoList&amp;utm_campaign=Badge_Grade)

Tests accounts :

<ul>
  <li>Role Admin
    <ul>
      <li>username : admin</li>
      <li>password : admin</li>
    </ul>
  </li>
  <li>Role user
    <ul>
      <li>username : user1</li>
      <li>password : user1</li>
    </ul>
    <ul>
      <li>username : user2</li>
      <li>password : user2</li>
    </ul>
  </li>
</ul>

### Installation

#### Requirements

-   Docker (1.12+)
-   docker-compose (1.10+)
-   GNU make

#### Clone the project

    $ git clone git@github.com:nverjus/TodoList.git
    $ cd TodoList

#### Configuration

Duplicate `docker-compose.override.yml.dist` and name the copy `docker-compose.override.yml`. In this file you can choose the port and the database informations for the application.

Duplicate `app/config/parameters.yml.dist` and name the copy `app/config/parameters.yml`, edit it with your database informations, default will work with default docker-compose.overrive.yml

Use `make` to see the Makefile help :

    $ make
    start                          Install and start the project
    stop                           Remove docker containers
    clear                          Remove all the cache, the logs and the sessions
    clean                          Clear and remove dependencies
    cc                             Clear the cache in dev and prod env
    tty                            Run app container in interactive mode
    db                             Reset the database
    fixtures                       Load the test fixtures in database
    tests                          Run the tests
    coverage                       Run the tests and generate a coverage report

Run `make start` to start the app.
Run `make fixtures` to load the tests fixtures

By default, the documentation is reachable at `http://localhost/`
