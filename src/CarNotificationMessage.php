<?php

use function Mysql\select;

class CarNotificationMessage extends MailMessage{


    public function __construct(){}


    
	public function getHtml($params) {

		return $this->getRecentCarList($params->court, $startDate, $endDate);
	}


	public function getRecentCarList($court = 'Oregon Appellate Court', DateTime $begin = null, DateTime $end = null) {
		$begin = null == $begin ? new DateTime() : $begin;
		
		$beginMysql = $begin->format('Y-m-j');

		if(null == $end) {
			$query = "SELECT * FROM car WHERE decision_date = '{$beginMysql}'";
			$query .= " AND court = '{$court}'";
		} else {
			$endMysql = $end->format('Y-m-j');
	
			$query = "SELECT * FROM car WHERE decision_date >= '{$beginMysql}'";
			$query .= " AND decision_date <= '{$endMysql}'";
			$query .= " AND court = '{$court}'";
		}



		// print $query;exit;
		// ORDER BY year DESC, month DESC, day DESC";
		$cars = select($query);
		
		// var_dump($cars);exit;

		$list = new Template("email-list");
		$list->addPath(path_to_modules() . "/car/templates");

		$listHtml = $list->render(["cars" => $cars]);

		$body = new Template("email-body");
		$body->addPath(path_to_modules() . "/car/templates");

		$params = [
			"year" => $begin->format('Y'),
			"month" => $begin->format('m'),
			"day" => $begin->format('j'),
			"date" => $begin->format('l, M j  Y'),
			"carList" => $listHtml 
		];

	
		return $body->render($params);
	}

}