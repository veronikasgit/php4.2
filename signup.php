<?php
include "config.php";

$data = $_POST;

if (isset($data['do_signup'])) {

	$errors = array();

	if (trim($data['login']) == '')
	{
		$errors[] = 'Введите логин';
	}
	if ($data['password'] == '') 
	{
		$errors[] = 'Введите пароль';
	}
	if ($data['password_2'] != $data['password']) 
	{
		$errors[] = 'Повторный пароль введен неверно';
	}

	$sql = "SELECT COUNT(*) FROM user  WHERE login LIKE '%{$data['login']}%'";
	if ($res = $pdo->query($sql)) {

	    /* Определим количество строк, подходящих под условия выражения SELECT */
	    if ($res->fetchColumn() > 0) {
	    	$errors[] = 'Пользователь с таким логином уже уществует!';
	       
	    }
	    /* Результатов нет -- делаем что-то другое */
	    
	}
	
	if (empty($errors)) {
		$password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
		$row = $pdo->exec("INSERT INTO user (login, password) VALUES ('{$data['login']}', '$password_hash')");	
		 echo '<div style="color: green;">Вы успешно зарегистрированы! Можете перейти на <a href="/">главную</a> страницу</div><hr>';
		 exit;
	} else {
		echo '<div style="color: red;">'.array_shift($errors).'</div><hr>';
	}

}
?>

<form action="/signup.php" method="POST">
	
	<p>
		<p><strong>Ваш логин</strong></p>
		<input type="text" name="login" value="<?php echo @$data['login']; ?>">
	</p>

		<p>
		<p><strong>Ваш пароль</strong></p>
		<input type="password" name="password" value="<?php echo @$data['password']; ?>">
	</p>

	<p>
		<p><strong>Введите ваш пароль еще раз</strong></p>
		<input type="password" name="password_2" value="<?php echo @$data['password_2']; ?>">
	</p>

	<p>
		<button type="submit" name="do_signup">Зарегистрироваться</button>
	</p>

</form>