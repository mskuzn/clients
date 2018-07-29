<html>
<title> Данные по клиентам </title>
<head>
	<meta charset = "utf-8" />
	<b>Редактирование данных клиента ID = <?php echo $_GET['id'];?></b>
</head>
<body>
<?php
	include_once "Db_operations.php";
	include_once "conn_parameters.php";
	$num_of_phones = htmlspecialchars($_POST['counter_phones']);
	if (is_null($num_of_phones) ||  $num_of_phones<=0){$num_of_phones=1;}
	$parametrs_arr_id = ['id'=>  $_GET['id']];
	$parametrs_arr_update_data=['id'=>  $_GET['id']];
	$parametrs_arr_update_data['name'] = $_POST['name'];	
	$parametrs_arr_update_data['familyname'] = $_POST['familyname'];	
	$parametrs_arr_update_data['fothername'] = $_POST['fothername'];	
	$parametrs_arr_update_data['birthday'] = $_POST['birthday'];	
	$parametrs_arr_update_data['male'] = $_POST['male'];	

	

	$sql_phones= "select phone_number from phones where client_id = :id;";
	$sql_clients = "
		select id,familyname,name,fothername,DATE_FORMAT(birthday,'%d.%m.%Y') as birthday, male,DATE_FORMAT(created,'%d.%m.%Y') as created,DATE_FORMAT(modified,'%d.%m.%Y') as modified from clients where id= :id;";

	$sql_delete_client="
						DELETE FROM clients WHERE id=:id;
						DELETE FROM phones WHERE client_id=:id;";
	
	$sql_update_data="Start transaction;
						LOCK TABLES clients.clients WRITE, clients.phones WRITE;
						DELETE FROM phones WHERE client_id=:id; ";
	$sql_update_data .= "UPDATE clients SET 
						 name = :name
						,familyname = :familyname
						,fothername = :fothername
						,birthday = STR_TO_DATE(:birthday, '%d.%m.%Y')
						,male = :male
						,modified = NOW()
						WHERE id=:id; ";
	for ($i = 1; $i <= $num_of_phones+1; $i++) { //набиваем запрос номерами телефонов и добавляем для них сответствия с именами полей формы
		$sql_update_data .="insert into phones values (NULL, :phone" . $i . ", :id); ";
		$parametrs_arr_update_data['phone' . $i] = $_POST['phone' . $i ];	
	}
	$sql_update_data .= "UNLOCK TABLES; COMMIT; ";

	

	try {
		$db= new Db_operations($servername,$username,$password,$dbname);
		$db->execute("SET NAMES 'utf8'; SET CHARACTER SET 'utf8'; SET SESSION collation_connection = 'utf8_general_ci';",$parametrs_arr_id);
			foreach ($db->query($sql_clients,$parametrs_arr_id) as $row) { //цикл по одному элементу так проще записать)))))
				$familyname= $row['familyname'];
				$name= $row['name'];
				$fothername= $row['fothername'];
				$birthday= $row['birthday'];
				$male= $row['male'];
				$created= $row['created'];
				$modified= $row['modified'];
		}
		$phones_arr =array();
		foreach ($db->query($sql_phones,$parametrs_arr_id) as $row) {
			array_push($phones_arr,$row['phone_number']);
		}
			
		if(isset($_POST['delete_client'])){$db->execute($sql_delete_client,$parametrs_arr_id);}
		elseif(isset($_POST['save_client'])){$db->execute($sql_update_data,$parametrs_arr_update_data);}
	}
	catch (PDOException $e) {
		echo "Faled: " . $e->getMessage();
	}
	

	?>
	
<form action="" method="post"> 	
	<p><input type="submit" value="На главную" name="to_main"></p> 
	<p> </p> 
	<p><input type="submit" value="Удалить клиента" name="delete_client"></p>
	</form>
<form action="" method="post">

	<p>Фамилия: <br /><input type="text" name="familyname" required
		value="<?php echo $familyname;?>"
		pattern="[A-Za-zА-Яа-яЁё]+"
	/></p>
	<p>Имя: <br /><input type="text" name="name" required 
		value="<?php echo $name;?>"
		pattern="[A-Za-zА-Яа-яЁё]+"/></p>
	<p>Отчество: <br /><input type="text" name="fothername" required 
		value="<?php echo $fothername;?>"
		pattern="[A-Za-zА-Яа-яЁё]+"
	/></p>
	<p>Дата рождения:<input type=text name="birthday"  required
		placeholder="dd.mm.yyyy"
		pattern="^(0[1-9]|1\d|2\d|30|31)(\.)(0[1-9]|1[0-2])(\.)(18|19|20|21|22)([0-9][0-9])$"
		value="<?php echo $birthday;?>"> 
	</p>
	<p>Пол: <input name="male" type="radio" required value="m" <?php if ($male =='m') echo 'checked';?>>Мужской 
			<input name="male" type="radio" required value="w" <?php if ($male =='w') echo 'checked';?>> Женский</p>
	<p>Телефоны клиента:</p>
	
	<?php
			
			$is_first_come = (  
					!isset($_POST['add_phone'])
				 && !isset($_POST['save_client']) 
				 && !isset($_POST['rem_phone']) 
				 && !isset($_POST['to_main']) 
				 && !isset($_POST['delete_client']));
			
				function phones_get_value($n,$is_first,$values_arr)
				{
					if ($is_first){
						$phone_value=$values_arr[$n-1];
					}else{
						$phone_value=htmlspecialchars($_POST['phone' . $n]);
					}
					return $phone_value;
				}
								

				if (isset($_POST['add_phone'])){$num_of_phones++;}
				elseif(isset($_POST['rem_phone']) && $num_of_phones>=1){$num_of_phones--; } 
				//if (($_POST['rem_phone']) && $num_of_phones==2){$num_of_phones=1;}

			if($is_first_come){$num_of_phones=count($phones_arr)+1;}
					$i=1;
				while ($i <= $num_of_phones-1) {
					echo "<p> Телефон №" . $i . " <input type=\"text\" name=\"phone" . $i .  "\" value=\"" . phones_get_value($i,$is_first_come,$phones_arr) . "\" placeholder=\"Вводите только цифры\" ></p> ";
					$i++;
				} 
	
	
	
	?>
	<p><input type="submit" value="+номер" name="add_phone">   <input type="submit" value="убрать номер" name="rem_phone"></p>
	<p><input type="submit" value="Сохранить" name="save_client"></p>


		
		<input  type="hidden" name="counter_phones" value="<?php echo $num_of_phones;?>">
	
		<?php 
			  if (isset($_POST['to_main']) || isset($_POST['delete_client']) || isset($_POST['save_client'])){
				$host  = $_SERVER['HTTP_HOST'];
				$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
				header("Location: http://$host$uri/");
			  }
	?>
	
</form>

</body>
</html>
