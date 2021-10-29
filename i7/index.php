<!DOCTYPE html>
<html>
	<head>
		<title>Регистрация|обновление домена</title>
		<link rel="stylesheet" href="styles/bootstrap.min.css">
		<link rel="stylesheet" href="styles/styles.css">
	</head>
	<body>
		<div class='container container-max'>
<!---------------------------------------------------------------------------------------------------->
<?php
include_once('classes/VrApiClass.php');
include_once('classes/DomainClass.php');

$vrApi = new VrApiClass('demo', 'demo', 'demo');
$domain = new DomainClass();

if(isset($_POST['register'])){
	$domainName = trim($_POST['name']);
	$clientId = (int) $_POST['client_id'];

	$domain->setName($domainName);
	$domain->setClientId($clientId);

	$vrApi->registerDomain($domain);
}

if(isset($_POST['update'])){
	$nservers = trim($_POST['nservers']);
	$nservers = explode(' ', $nservers);
	foreach($nservers as $key => $value){
		$nservers[$key] = '"'.$value.'"';
	}
	$nservers = implode(',', $nservers);

	$domainId = (int) $_POST['domain_id'];

	$domain->setId($domainId);
	$domain->setNserves($nservers);
	$domain->setClientId(585); //этот пользователь выбран в качестве лемонстрационного

	$vrApi->updateDomain($domain);
}

function printClients($vrApi){
	$clientsList = $vrApi->getClients();

	foreach ($clientsList as $key => $value) {
		echo '<option value="'.$value['id'].'">'.$value['name'].'</option>';
	}
}

function printDomens($vrApi){
	$clientsList = $vrApi->getDomains();

	foreach ($clientsList as $key => $value) {
		echo '<option value="'.$value['id'].'">'.$value['name'].'</option>';
	}
}
?>
<!---------------------------------------------------------------------------------------------------->
			<form method='POST'>
				<div class='row'>
					<div class='col-md-offset-3 col-md-6'>
						<div class='block block-shadow'>
						<h4>Регистация домена</h4>
						<form method='POST' action='{$smarty.server.PHP_SELF}'>
							<div class='row bordered'>
								<div class='col-md-4'>
									<input type="text" name="name" class='form-control' required='required' placeholder="Имя домена">
								</div>
								<div class='col-md-4'>
									<select name="client_id" class="form-control" required='required'>
										<option disabled selected>Клиент</option>
										<?php printClients($vrApi); ?>
									</select>
								</div>
								<div class='col-md-4'>
									<button type="submit" name="register" class="btn btn-primary">Зарегистрировать</button>
								</div>
							</div>
						</form>
					</div>
					</div>
				</div>
			</form>

			<form method='POST'>
				<div class='row'>
					<div class='col-md-offset-3 col-md-6'>
						<div class='block block-shadow'>
						<h4>Смена nservers</h4>
						<form method='POST' action='{$smarty.server.PHP_SELF}'>
							<div class='row bordered'>
								<div class='col-md-4'>
									<input type="text" name="nservers" class='form-control' placeholder="nservers">
								</div>
								<div class='col-md-4'>
									<select name="domain_id" class="form-control" required='required'>
										<option disabled selected>Домен</option>
										<?php printDomens($vrApi); ?>
									</select>
								</div>
								<div class='col-md-4'>
									<button type="submit" name="update" class="btn btn-primary">Обновить</button>
								</div>
							</div>
						</form>
					</div>
					</div>
				</div>
			</form>
		</div>
	</body>
</html>