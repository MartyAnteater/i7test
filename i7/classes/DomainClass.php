<?php

class DomainClass{
	private $id = 0;
	private $name = '';
	private $clientId = 0;
	private $nserves = '';

	public function setId($id){
		$this->id = $id;
	}

	public function getId(){
		return $this->id;
	}

	public function setName($name){
		$this->name = $name;
	}

	public function getName(){
		return $this->name;
	}

	public function setClientId($clientId){
		$this->clientId = $clientId;
	}

	public function getClientId(){
		return $this->clientId;
	}

	public function setNserves($nserves){
		$this->nserves = $nserves;
	}

	public function getNserves(){
		return $this->nserves;
	}
}