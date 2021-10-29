<?php

class VrApiClass {
	private $installationId = '';
	private $login = '';
	private $password = '';
	private $token = '';

	public function __construct($installationId, $login, $password){
		$this->installationId = $installationId;
		$this->login = $login;
		$this->password = $password;

		$this->authorizeUser();
	}

	private function getConnectionontext(){
		return stream_context_create(array('http' => array('proxy' => '000.0.0.000:0000', 'request_fulluri' => true)));
	}

	private function getApiAnswer($query){
		//$apiAnswer = file_get_contents($query, false, $this->getConnectionontext());
		$apiAnswer = file_get_contents($query);
		$apiAnswer = json_decode($apiAnswer, TRUE);

		//проверка актуальности токена и последующая реавторизация -
		//была бы нужна при условии хранения токена в сессии и использовании его, пока не истечет.
		//в этой реализации при каждом запуске скрипта авторизация происходит вновь
		/*if(isset($apiAnswer['error']['code']) && $apiAnswer['error']['code'] == 10){
			$this->authorizeUser();
			return $this->getApiAnswer($query);
		}*/

		return $apiAnswer;
	}

	//авторизуем пользователя
	public function authorizeUser(){
		$query = 'https://vr'.$this->installationId.'.virtreg.ru/vr-api?method=authLogin&params={"login":"'.$this->login.'","password":"'.$this->password.'"}';
		$apiAnswer = $this->getApiAnswer($query);

		$this->token = $apiAnswer['result']['token'];
	}

	//получаем часть списка "живых" пользователей для вывода
	public function getClients(){
		$query = 'https://vr'.$this->installationId.'.virtreg.ru/vr-api?method=clientEnum&params={"auth":{"token":"'.$this->token.'"},"query":{"filter":[["isAlive","=",true]],"length":10,"order":["name"]}}';
		$apiAnswer = $this->getApiAnswer($query);

		if(isset($apiAnswer['result']['clients']) && !empty($apiAnswer['result']['clients'])){
			return $apiAnswer['result']['clients'];
		}

		return array();
	}

	//получаем часть списка доменов для клиента 585
	public function getdomains(){
		$query = 'https://vr'.$this->installationId.'.virtreg.ru/vr-api?method=domainEnum&params={"auth":{"token":"'.$this->token.'"},"query":{"filter":[["isAlive","=",true],["clientId","=",585]],"length":10,"order":["name"]}}';
		$apiAnswer = $this->getApiAnswer($query);

		if(isset($apiAnswer['result']['domains']) && !empty($apiAnswer['result']['domains'])){
			return $apiAnswer['result']['domains'];
		}

		return array();
	}

	//регистрируем домен
	public function registerDomain($domain){
		$query = 'https://vr'.$this->installationId.'.virtreg.ru/vr-api?method=domainCreate&params={"auth":{"token":"'.$this->token.'"},"clientId":'.$domain->getClientId().',"domain":{"name":"'.$domain->getName().'"}}';
		$apiAnswer = $this->getApiAnswer($query);;

		if(isset($apiAnswer['error'])){
			if($apiAnswer['error']['code'] == 37){
				echo '<div class="row"><div class="col-md-offset-3 col-md-6"><h4 style="color: red">Данный домен уже занят</h4></div></div>';
				return null;
			}
		}
		if(isset($apiAnswer['result']['id']) && $apiAnswer['result']['id'] > 0){
			echo '<div class="row"><div class="col-md-offset-3 col-md-6"><h4 style="color: green">Домен "'.$domain->getName().'" зарегистрирован</h4></div></div>';

			$domain->setId($apiAnswer['result']['id']);
			return $domain;
		}

		return null;
	}

	//обновляем данные о домене.
	//в этой реализации обновляется значение nservers
	public function updateDomain($domain){
		$query = 'https://vr'.$this->installationId.'.virtreg.ru/vr-api?method=domainUpdate&params={"auth":{"token":"'.$this->token.'"},"id":'.$domain->getId().',"clientId":'.$domain->getClientId().',"domain":{"nservers":['.$domain->getNserves().'],"delegated":false}}';
		$apiAnswer = $this->getApiAnswer($query);;

		if(isset($apiAnswer['result']['id']) && $apiAnswer['result']['id'] > 0){
			echo '<div class="row"><div class="col-md-offset-3 col-md-6"><h4 style="color: green">Обновлено</h4></div></div>';

			$domain->setId($apiAnswer['result']['id']);
			return $domain;
		}

		return null;
	}
}