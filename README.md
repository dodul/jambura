# Introduction

Jambura is a PHP framework that follows the Model-View-Controller (MVC) architectural pattern. It is designed to simplify the development of web applications by providing a structured and organized way to build and maintain code. The framework is lightweight, making it suitable for small to medium-sized projects.

Key features of Jambura:
- MVC architecture for separation of concerns.
- Lightweight and easy to learn.
- Built-in support for  database operations.

## Getting Started

### Installation

To start using Jambura, follow these steps:

1. Clone the Jambura framework repository to your local machine:

2. Configure your web server to point to the `public` directory of the framework.

3. Create a virtual host or configure your server to handle requests properly.

### Configuration

Configuration settings for your Jambura application can be found in the `configuration.php` file. This file contains settings related to the database connection, directory structure, and default views. Here's an example of a configuration file:

```php
$config = [
 'database' => [
     'host' => 'localhost',
     'db' => 'dbname',
     'user' => 'username',
     'pass' => 'password'
 ],
 'directories' => [
     'models' => 'applications/models/',
     'controllers' => 'applications/controllers/',
     'views' => 'applications/views/',
     'classes' => 'applications/classes/',
     'templates' => 'templates/'
 ],
 'view' => [
     'default_template' => 'default',
     'default_layout' => 'default',
     'default_page' => 'welcome'
 ]
];
```

### Creating Controller

1. Create Controller by extending jController class

```php
class Controller_user extends jController
{
    public function init()
    {
        // Initialization code
    }

    // Controller actions
    public function action_index()
    {
        // Your logic here

        $this->render('view_name');
    }
}
```

### Creating Model

1. Create Model by extending jModel class

```php
class Model_users extends jModel
{
    protected $tableName = 'users';
    
    public function getAll(): array
    {
        //Get all user data in the user table
        return $this->table->find_many();
    }
}
```

2. Set table of the model

```php
class Model_users extends jModel
{
    protected $tableName = 'users';
}
```

3. Add relations to models.
    Loads ORM objects based on configuration. There can have two types of
    directions :
    1. p2c (parent to child): The primary key of the current row is used as a foriegn
    key in another table. p2c can be of two types:
        * 1-1 (one to one)  : loads only one column of destination table. 
        * 1-M (one to many ): loads all matching rows in destination column.
    2. c2p (child to parent): This table is using the primary key of of another table.

```php
class Model_users extends jModel
{
    protected $relations = [
        'company' => [
            'direction' => 'c2p',
            'table' => 'companies',
            'column' => 'company_id'
        ],
        'group' => [
            'direction' => 'p2c',
            'type' => '1-1',
            'table' => 'groups',
            'column' => 'group_id'
        ],
    ];
}
```

4. Add validations to models

```php
class Model_users extends jModel
{
    protected function validation()
    {
        return [
            'email' => ['regex', "/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/", 'Invalid email pattern']
        ];
    }
}
```

### Using Models into Controllers


1. Retrieve data from models in your controller actions:

```php
class Controller_company extends jController
{
    public function init()
    {

    }

    public function action_index()
    {
        $companyData = jModel::factory('groups')
            ->findAll();
    }
}
```

2. Save values to database by using models:

```php
class Controller_your_controller extends jController
{
    public function action_store()
    {
        $model = jModel::factory('your_model');
        $model->you_attribute = 'your_value';
        $model->save();
    }
}
```

3. Delete values from database by using models:

```php
class Controller_your_controller extends jController
{
    public function action_delete()
    {
        $model = jModel::factory('your_model', $table_id);
        $model->delete();
    }
}
```

4. update values to database by using models

```php
class Controller_your_controller extends jController
{
    public function action_your_action()
    {
        $model = jModel::factory('your_model', $table_id);
        $model->your_attribute = 'your_value';
        $model->update();
    }
}
```

### Passing data to Views
You can pass data to your views in the following ways:

1. Using an associative array:

```php
class Controller_your_controller extends jController
{
    public function action_your_action()
    {
        $data['title'] = 'index';
        $this->render('your_view', $data);
    }
}
```

Then you can access inside view file like this

```php
<div class="body flex-grow-1 px-3">
    <p>$title</p>
</div>
```

2. Using object properties:

```php
class Controller_your_controller extends jController
{
    public function action_your_action()
    {
        $this->your_data = 'your_data';
        $this->render('your_view');
    }
}
```

Then you can access inside view file like this

```php
<div class="body flex-grow-1 px-3">
    <p>$your_data</p>
</div>
```

## Using Idiorm ORM

Jambura uses the [Idiorm ORM](https://idiorm.readthedocs.io/en/latest/querying.html) for database operations. You can refer to the Idiorm documentation for detailed information on how to perform database queries and interact with your database.

Here's the link to the Idiorm documentation for your convenience: [Idiorm Documentation](https://idiorm.readthedocs.io/en/latest/querying.html)

## Contact Information

If you have any questions or need further assistance with Jambura or any related inquiries, please feel free to contact us via email:

- **Email**: [support@prepmock.com](mailto:support@prepmock.com)

We are here to help and support your web development projects.
