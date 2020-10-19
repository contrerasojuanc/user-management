User Management Application
========================

This application manage users, and groups.

Requirements
------------

  * PHP 7.1.3 or higher;
  * PDO-SQLite PHP extension enabled;
  * and the [usual Symfony application requirements][1].

Installation
------------

Execute this command to install the project:

```bash
$ composer update
```

[![Deploy](https://www.herokucdn.com/deploy/button.png)](http://jcusermanagement.herokuapp.com/index.php)

Usage
-----

There's no need to configure anything to run the application. Just execute this
command to run the built-in web server and access the application in your
browser at <http://localhost:8000>:

```bash
$ cd user-management/
$ php bin/console doctrine:fixtures:load
$ php bin/console server:run
```

Design
-----

In project's root there are two diagrams DatabaseDesign.png and DomainDesign.png

These diagrams can be updated using www.draw.io

The entity relationship database model can be get it using draw.io open a project from github: 

```contrerasojuanc/user-management/DatabaseDesign.xml```

Also the domain diagram can be get it using draw.io and open a project from github: 

```contrerasojuanc/user-management/DomainDesign.xml```

API
---

Following restful API endpoints are available to be used. 

```
  get_users                  GET        ANY      ANY    /api/users                          
  get_user                   GET        ANY      ANY    /api/users/{id}                     
  post_user                  POST       ANY      ANY    /api/users                          
  put_user                   PUT        ANY      ANY    /api/users/{id}                     
  delete_user                DELETE     ANY      ANY    /api/users/{id}                     
  get_groups                 GET        ANY      ANY    /api/groups                         
  get_group                  GET        ANY      ANY    /api/groups/{id}                    
  post_group                 POST       ANY      ANY    /api/groups                         
  put_group                  PUT        ANY      ANY    /api/groups/{id}                    
  delete_group               DELETE     ANY      ANY    /api/groups/{id} 
```
