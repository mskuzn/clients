<html>
<title>Клиенты</title>
<head>
	<meta charset = "utf-8" />
	<b>Клиенты

	</b>
</head>
<body>
	

<form action="" method="post">
	
<input type="submit" value="Добавить клиента" name="go_to_add_client">
</form>
<table border="1">
   <caption>Список клиентов</caption>
   <tr>
	<th>#</th>
    <th>ID</th>
    <th>Фамилия</th>
    <th>Имя</th>
    <th>Отчество</th>
    <th>Дата рождения</th>
	<th>Пол</th>
	<th>Дата создания</th>
	<th>Дата обновления</th>
	<th>Номера телефонов</th>
   </tr>

	
	<form action="" method="post">

	   <tr>
		   <td align="center"><input type="submit" value="      Найти  >>  " name="search"></td>
		   <td>
			   <input type="text" name="id"
					value="<?php echo htmlspecialchars($_POST['id']);?>"
					pattern="[0-9]+"
					placeholder='Равно!'
				/>
		   </td>
		   <td>
			   <input type="text" name="familyname"
					value="<?php echo htmlspecialchars($_POST['familyname']);?>"
					pattern="[A-Za-zА-Яа-яЁё]+"
					placeholder='содержит...' 
				/>
		   </td>
		   <td>
			   <input type="text" name="name"
					value="<?php echo htmlspecialchars($_POST['name']);?>"
					pattern="[A-Za-zА-Яа-яЁё]+"
					placeholder='содержит...' 
				/>
		   </td>
		   <td>
			   <input type="text" name="fothername"
					value="<?php echo htmlspecialchars($_POST['fothername']);?>"
			form		pattern="[A-Za-zА-Яа-яЁё]+"
					placeholder='содержит...' 
				/>
		   </td>
		   <td>
			   <input type="text" name="birthday"
					value="<?php echo htmlspecialchars($_POST['birthday']);?>"
					placeholder='содержит...' 
				/>
		   </td>
		   <td><p>
	<select size="1" name="male">
		<option value="m">Мужской</option>
		<option value="w">Женский</option>
		<option selected value="">Любой</option>
   </select>
		   </p></td>
		   <td>
			   <input type="text" name="created"
					value="<?php echo htmlspecialchars($_POST['created']);?>"
					placeholder='содержит...' 
				/>
		   </td>
		   <td>
			   <input type="text" name="updated"
					value="<?php echo htmlspecialchars($_POST['updated']);?>"
					placeholder='содержит...' 
				/>
		   </td>
		   <td>
			   <input type="text" name="phones"
					value="<?php echo htmlspecialchars($_POST['phones']);?>"
					placeholder='содержит...' 
				/>
		   </td>
		   <td></td>
		</tr>
		
					</form>
			<?php 

include_once "Db_operations.php"; //подключение класса взаимодействия с  БД
include_once "conn_parameters.php"; //подключение учётных данных для взамиодействия PHP и MySQL


if (isset($_POST['go_to_add_client'])){ // блок перехода к добавлению клиента
	$host  = $_SERVER['HTTP_HOST'];
	$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	$extra = 'add_client.php';
	header("Location: http://$host$uri/$extra");
exit;
}

	
if (isset($_POST['search'])){ // установки переменных фильтра для выборки по нажатию кнопки Найти
	$id= $_POST['id'];
	$familyname= $_POST['familyname'];
	$name= $_POST['name'];
	$fothername= $_POST['fothername'];
	$birthday= $_POST['birthday'];
	$male= $_POST['male'];
	$created= $_POST['created'];
	$modified= $_POST['updated'];
	$phones= $_POST['phones'];	
}

$parametrs_arr = [ // взятие параметров из полей формы поиска клиентов
	 'id'=>  $_POST['id']
	,'familyname'=> $_POST['familyname']
	,'name'=>  $_POST['name']
	,'fothername'=> $_POST['fothername']
	,'birthday'=> $_POST['birthday']
	,'male'=> $_POST['male']
	,'created'=> $_POST['created']
	,'updated'=> $_POST['updated']
	,'phones'=> $_POST['phones']	
];
	
	
	
$sql = // формирование шаблона запроса на выборку
"
select id,familyname,name,fothername,DATE_FORMAT(birthday,'%d.%m.%Y') as birthday,case male when 'm' then 'мужской' when 'w' then 'женский' end  as male,DATE_FORMAT(created,'%d.%m.%Y') as created,DATE_FORMAT(modified,'%d.%m.%Y') as modified,phones from clients cli
	left join (select client_id, GROUP_CONCAT(phone_number SEPARATOR '; ') as phones from phones group by client_id) pho 
			on pho.client_id=cli.id
	where  id like '%$id%' and coalesce(familyname, '') like '%$familyname%' and coalesce(name, '') like '%$name%' and coalesce(fothername, '') like '%$fothername%' 
			and coalesce(DATE_FORMAT(birthday,'%d.%m.%Y'), '') like '%$birthday%' and coalesce(male, '') like '%$male%' and DATE_FORMAT(created,'%d.%m.%Y') like '%$created%' and DATE_FORMAT(modified,'%d.%m.%Y') like '%$modified%' and coalesce(phones, '') like '%$phones%' ; ";
try {
		$db= new Db_operations($servername,$username,$password,$dbname); //создаём объект подключения к БД
		$db->execute("SET NAMES 'utf8'; SET CHARACTER SET 'utf8';SET SESSION collation_connection = 'utf8_general_ci';",$parametrs_arr); //установка кодировки соединения
			foreach ($db->query($sql,$parametrs_arr) as $row) { //создание строк таблицы списка клиента 
				// в первом поле ссылка на редактирование с параметром ID
				echo "<tr><td><a href=\"/edit_client.php?id=". $row['id'] . "\">Редактировать</a> </td> 
			<td>". $row['id'] . "</td><td>". $row['familyname'] . "</td><td>". $row['name'] . "</td><td>". $row['fothername'] . "</td><td>". $row['birthday'] . "</td><td>". $row['male'] . "</td><td>". $row['created'] . "</td><td>". $row['modified'] . "</td><td>". $row['phones'] . "</td>
		</tr>" ;
			}

	}
	catch (PDOException $e) { //поймать и показать ошибки PDO
		echo "Faled: " . $e->getMessage();
	}
	

?>

		
		

	  </table>
	


</body>
</html>

	

