<?php
header('Content-Type: text/html; charset=utf-8');


/*
API ключ с GetCourse доступен владельцу аккаунта или администратору с дополнительным правом настройки аккаунта.
Ссылка на ключ: https://ИМЯ_АККАУНТА_ГЕТКУРС.getcourse.ru/saas/account/api

Ссылка для постбека ZVONOK.COM: https://ВАШ_ДОМЕН_ДЛЯ_ИНТЕГРАЦИЙ/integrations/ИМЯ_ФАЙЛА.php?ct_phone={ct_phone9}&ct_phone_standart={ct_phone}&ct_button_num={ct_button_num}&utm_source=autocall&utm_medium=ГРУППА_В_ГК

*/


function to_gk(){
    //Собираем массив информации о пользователе
    if ($_GET['ct_phone'] != '') $user["phone"] = $_GET['ct_phone'];
    
    //Добавление в группу
    if (isset($_GET['utm_medium'])){
        $user_group = $_GET['utm_medium'];
        $user['group_name'] = [$user_group];
    } 
    
    //Отправка СМС напрямую с SMSC.RU
    if (isset($_GET['utm_medium']) && $_GET['utm_medium'] == "autocall_garage_mes"){
        
        $ch1 = curl_init("https://smsc.ru/sys/send.php?login=ЛОГИН&psw=ПАРОЛЬ&phones={$_GET['ct_phone_standart']}&mes=ТЕКСТ");
        curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch1, CURLOPT_HEADER, 0);
        $data1 = curl_exec($ch1);
        curl_close($ch1);
        
    } 

    //Собираем массив данных о пользователе для отправки в геткурс
    $dataArray = array(
        "user"    => $user,
        
        "system"  => array (
                "refresh_if_exists" => 0, // обновлять ли существующего пользователя 1/0 да/нет
                "partner_email" => ""),

        
        "session" => array (
                "utm_source"     => (isset($_GET['utm_source'])) ? $_GET['utm_source'] : '',
                "utm_medium"     => (isset($_GET['utm_medium'])) ? $_GET['utm_medium'] : '',
                "utm_content"    => (isset($_GET['utm_content'])) ? $_GET['utm_content'] : '',
                "utm_campaign"   => (isset($_GET['utm_campaign'])) ? $_GET['utm_campaign'] : '',
                            )
        );	
    $mas = base64_encode(json_encode($dataArray));

//Собираем массив с информацией о методе, ключом и инфы о пользователе
$mas1 = array (
	    "action" => "add",
	    "key" => "API ключ с GetCourse",
	    "params" => "$mas"
	);
	
//Отправляем запрос в getcourse
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://ИМЯ_АККАУНТА_ГЕТКУРС.getcourse.ru/pl/api/users");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $mas1);
$output = curl_exec($ch);
curl_close($ch);

$demas = json_decode($output, true);
}


if(isset($_GET['ct_phone']) && isset($_GET['ct_button_num']) && $_GET['ct_button_num'] == '1') {
    to_gk();
}