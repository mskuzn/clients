<html>
<title>Данные по клиентам</title>
<head>
	<meta charset = "utf-8" />
	<b>Добавление нового клиента</b>
</head>
<body>
<form action="" method="post"> 	<p><input type="submit" value="На главную" name="to_main"></p> </form>
<form action="" method="post">

	<p>Фамилия: <br /><input type="text" name="familyname" required
		value="<?php echo htmlspecialchars($_POST['familyname']);?>"
		<?php if (isset($_POST['add_client'])){echo 'disabled';} ?>
		pattern="[A-Za-zА-Яа-яЁё]+"
	/></p>
	<p>Имя: <br /><input type="text" name="name" required value="<?php echo htmlspecialchars($_POST['name']);?>"
		<?php if (isset($_POST['add_client'])){echo 'disabled';} ?>
		pattern="[A-Za-zА-Яа-яЁё]+"/></p>
	<p>Отчество: <br /><input type="text" name="fothername" required value="<?php echo htmlspecialchars($_POST['fothername']);?>"
		<?php if (isset($_POST['add_client'])){echo 'disabled';} ?>
		pattern="[A-Za-zА-Яа-яЁё]+"
	/></p>
	<p>Дата рождения:<input type=text name="birthday"  required
		placeholder="dd.mm.yyyy"
		<?php if (isset($_POST['add_client'])){echo 'disabled';} ?>
		pattern="^(0[1-9]|1\d|2\d|30|31)(\.)(0[1-9]|1[0-2])(\.)(18|19|20|21|22)([0-9][0-9])$"
		value="<?php echo htmlspecialchars($_POST['birthday']);?>"> 
	</p>
	<p>Пол: <input name="male" <?php if (isset($_POST['add_client'])){echo 'disabled';} ?> type="radio" required value="m" <?php if (htmlspecialchars($_POST['male'])=='m') echo 'checked';?>>Мужской 
			<input name="male" <?php if (isset($_POST['add_client'])){echo 'disabled';} ?> type="radio" required value="w" <?php if (htmlspecialchars($_POST['male'])=='w') echo 'checked';?>> Женский</p>
	<p>Телефоны клиента:</p>
	
	<input  type="hidden" name="counter_phones" value="<?php
			$i=1;
			$num_of_phones = htmlspecialchars($_POST['counter_phones']);
			if (is_null($num_of_phones)){
				$num_of_phones=1;
			}
				if (isset($_POST['add_phone'])){$num_of_phones++;}
			echo $num_of_phones;?>">
	
		<?php
			$status="";
			if (isset($_POST['add_client'])){$status=" disabled ";}
			do {
				echo "<p> Телефон №" . $i . " <input type=\"text\" name=\"phone" . $i .  "\" value=\"" . htmlspecialchars($_POST['phone' . $i]) . "\" placeholder=\"Вводите только цифры\"". $status ." ></p> ";

				$i++;
			} while ($i-1 <= $num_of_phones);

	?>
	<p><input type="submit" <?php if (isset($_POST['add_client'])){echo 'disabled';} ?> value="+номер" name="add_phone"></p>
	<p><input type="submit" <?php if (isset($_POST['add_client'])){echo 'disabled';} ?> value="Сохранить" name="add_client"></p>


	
</form>

<?php
include_once "Db_operations.php";
include_once "conn_parameters.php";

$parametrs_arr = ['name'=>  $_POST['name']
	,'familyname'=> $_POST['familyname']
	,'fothername'=> $_POST['fothername']
	,'birthday'=> $_POST['birthday']
	,'male'=> $_POST['male']];
$parametrs_arr_for_check = $parametrs_arr;
$sql ="
	SET NAMES 'utf8';
	SET CHARACTER SET 'utf8';
	SET SESSION collation_connection = 'utf8_general_ci';
	Start transaction;
	LOCK TABLES clients.clients WRITE, clients.phones WRITE;
	insert into clients (name 
		,familyname 
		,fothername 
		,birthday 
		,male 
		,created 
		,modified)  VALUES (:name,:familyname,:fothername,STR_TO_DATE(:birthday, '%d.%m.%Y'),:male,NOW(),NOW()); ";
for ($i = 1; $i <= $num_of_phones+1; $i++) { //набиваем запрос номерами телефонов и добавляем для них сответствия с именами полей формы
	$sql .="
	insert into phones values (NULL, :phone" . $i . ", (select max(id) from clients)); ";
		$parametrs_arr['phone' . $i] = $_POST['phone' . $i ];	
	}

$sql .= "UNLOCK TABLES;
	commit; ";
if (isset($_POST['add_client'])){
	try {
		$db= new Db_operations($servername,$username,$password,$dbname);

		$db-> execute($sql,$parametrs_arr);

		$sql_get_last_id = "SELECT id as last_id FROM clients 
			where name = :name and familyname = :familyname and fothername = :fothername and birthday = STR_TO_DATE(:birthday, '%d.%m.%Y') and male = :male;";
			foreach ($db->query($sql_get_last_id,$parametrs_arr_for_check) as $row) {
				$last_id= $row['last_id'];
			}

				echo "Клиент добавлен, присвоен ID: " . $last_id ;


	}
	catch (PDOException $e) {
		echo "Faled: " . $e->getMessage();
	}
}
	
	
if (isset($_POST['to_main'])){
	
	$host  = $_SERVER['HTTP_HOST'];
	$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

	header("Location: http://$host$uri/");
}

?>
</body>
</html>
