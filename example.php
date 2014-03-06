$connection_information = array(
	'host' => 'localhost',
	'user' => 'root',
	'pass' => '',
	'db' => 'test'
);
$m = new mysql($connection_information);
 
//simple and complex query (I recommend you use the select method of the class rather than this)
$result = $m->query('SELECT * FROM `users`');
var_dump($result);
//this will output an array like this: $array[$count][$table][$field]
 
//simple execute command (I recommend using the delete and the insert and the update methods of the class)
$result = $m->execute('DELETE FROM `pages` WHERE `id`=5');
var_dump($result); //returns true if ok and false if not
 
//method to select (clean and beautiful)
$result = $m->select(array(
	'table' => 'users',
	'condition' => 'active=1 AND type=1'
));
var_dump($result);
// Will output something like this:
/*
array (3) {
	[0] => array (1) {
		['users'] => array (4) {
			['id'] => int (5),
			['name'] => string (7) "ciprian",
			['active'] => int (1),
			['type'] => int (1),
			['email'] => string (14) "ciprian@mbe.ro"
		}
	},
	[1] => ...
}
*/
 
//you can also get only one row (and a simple array like this $array[$table_field])
$result = $m->row(array(
	'table' => 'users',
	'condition' => 'active=1 AND type=1'
));
var_dump($m); //returns only the first row in an array arranged like this $array[$table_field]
 
//or you can get only a field (for example you need the name of the user with id = 5)
$name=$m->get('users','name','id=5');
var_dump($name);
// Will output something like this:
/*
string (7) "ciprian"
*/
 
//you can also insert into the table
$data = array(
	'name' => 'ion',
	'active' => 0,
	'email' => 'ion@mbe.ro'
);
$result = $m->insert('users',$data);
var_dump($result); //returns true if ok and false if not
 
//update the table (let's assume we have a pages table and we need to set the views of the page with + 1
$data = array(
	'views' => array( 'views+1' )
);
$result = $m->update('pages',$data,'id='.$current_page_id);
var_dump($result); //returns true if ok and false if not
//PS: Notice I put the value of the views in another array. You can do that in the update as well as in the insert. If you put it like that, no mysql_real_escape_string will be called for that value when updating / inserting.
 
//deletion is also possible
$result = $m->delete('pages','id=5'); //deletes page with id 5
var_dump($result); //returns true if ok and false if not
