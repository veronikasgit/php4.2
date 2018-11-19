<?php
include "config.php";
$data = $_POST;

if (isset($data['do_login'])) {

	$sth = $pdo->prepare("SELECT * FROM user WHERE login LIKE '%{$data['login']}%'");
	$sth->execute();

	$result = $sth->fetchAll();

	$errors = array();

	if (trim($data['login']) == '')
	{
		$errors[] = 'Введите логин';
	}

	if ($data['login'] === $result['0']['login']) {
		//var_dump($result['0']);

		if (password_verify($data['password'], $result['0']['password'])) {
    		$_SESSION['user_id'] = $result['0']['id'];
    		$_SESSION['user_name'] = $result['0']['login'];
    		echo '<div style="color: green;">Вы успешно авторизованы! Можете перейти на <a href="/">главную</a> страницу</div><hr>';
    		exit;
		} else {
	     $errors[] = 'Пароль не введен или введен неправильно!';
		}
		
	} else {
		$errors[] = 'Пользователь с таким логином не найден!';
	}

	if ( ! empty($errors)) {
		echo '<div style="color: red;">'.array_shift($errors).'</div><hr>';
	}
}
	
?>

<form action="/login.php" method="POST">
	
	<p>
		<p><strong>Ваш логин</strong></p>
		<input type="text" name="login" value="<?php echo @$data['login']; ?>">
	</p>

		<p>
		<p><strong>Ваш пароль</strong></p>
		<input type="password" name="password" value="<?php echo @$data['password']; ?>">
	</p>

	<p>
		<button type="submit" name="do_login">Войти</button>
	</p>

</form>