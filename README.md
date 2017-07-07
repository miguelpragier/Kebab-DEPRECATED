# Kebab
PHP-Kebab is an opensource set of useful classes that solves common tasks, without to force the coder to learn or to fit in complex frameworks.
It's made according the KISS principle: Keep it short and simple.

Requirements: PHP >= 7.x

Bellow you can see all you can do with Kebab:

## Examples
```php 
require 'kebab.database.php';
require 'kebab.request.php';
require 'kebab.html.select.php';

# The tests consider the following table:
# CREATE TABLE users (
# id INTEGER,
# name VARCHAR(50),
# birthdate DATE
# );

$db = new \Kebab\KebabDatabase();

$twoHundred = $db->getInteger('SELECT 2*100');

# You can use the form getInt() or getInteger(), as you prefer.
# The second parameter works like COALESCE function; the value is 
# returned when the main searched value is null or not found.
$notFound = $db->getInt("SELECT id FROM users WHERE name='horatius'",-1);

$userBirthday = $db->getString("SELECT DATE_FORMAT('MMM, DD',birthdate) FROM users");

# method for free-hand queries 
$affectedRows = $db->exec('UPDATE users SET name=UPPER(name)');

# exec() or execute() are the very same.
$affectedRows = $db->exec("INSERT INTO users (name,birthday) VALUES ('victor frankenstein','1885-04-01')");

$lastInsertedId = $db->getLastInsertId();

$arrayOfUserNames = $db->getArrayAssociative('SELECT name FROM users ORDER BY name');

$associativeUniqueRow = $db->getRowAssociative('SELECT*FROM users WHERE id=15');

$arrayOfUserIds = $db->getArray('SELECT id FROM users ORDER BY id');

$unidimensionalArrayOfUppercaseNames = $db->getRow('SELECT*FROM users');

$howManyUsers = $db->getTableCount('users');

# For simple inserts, a straightforward method
$newUser = [
    'name'=>'nikolas testa',
    'birthdate'=>'1856-07-11'
];

$newUserId = $db->insert('users', $newuser);

# And this method is intended for very simple "update" operations
$newDataForUser = [
    'name'=>'nikolas tesla',
    'birthdate'=>'1856-07-10'
];

$where = ['id'=>$newUserId];

$db->update('users',$newDataForUser,$where);

# HTML SELECT Example
$arrayOfUsers = $db->getArray('SELECT id,name FROM users ORDER BY name');

# The construct requires an id for the html element
$select = new KebabHtmlSelect('selectUsers');

$select->addClass('big-and-beautiful');

$select->setMultiple();     # Now you can select multiple options

$select->addStyle('background-color','#f5f5f5');

$select->setAutoFocus();

$select->setRequired();

$select->addBehaviour('onclick', 'recordData()');

$select->addOption('0', 'Choose a user');

# It's possible to add groups
$select->addGroup('chubby users');
$select->setSelectedOption(8);

# You can add simple numerical ranges
$select->AddRange(15, 145);

# Add the previously generated array of users, and set 1st column as value and 2nd column as text.
$select->addArray($arrayOfUsers);

# Week days, in spanish, portuguese or english
$select->addWeekRange('es');

# Months, in spanish, portuguese or english:
$select->addMonthRange('en');

# Months, starting from current month
$startFromCurrentMonth = true;
$select->addMonthRange('en', $startFromCurrentMonth);

# Print full select element
echo $select->output();

# or only its options:
echo $select->output(true);

# Print the same select, but with another id
echo $select->outputWithNewId();
```