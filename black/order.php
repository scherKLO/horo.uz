<?php

if (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && filter_var($_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
    $userIp = $_SERVER['HTTP_CF_CONNECTING_IP'];
}
elseif (isset($_SERVER['HTTP_X_REAL_IP']) && filter_var($_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
    $userIp = $_SERVER['HTTP_X_REAL_IP'];
}
else {
    $userIp = $_SERVER['REMOTE_ADDR'];
}

$userAgent = !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'API';
$referer = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : (!empty($_SERVER['HTTP_HOST']) ? ('http://' . $_SERVER['HTTP_HOST']) : '');

$subId1 = !empty($_POST['sub1']) ? $_POST['sub1'] : '';
$subId2 = !empty($_POST['sub2']) ? $_POST['sub2'] : '';
$subId3 = !empty($_POST['sub3']) ? $_POST['sub3'] : '';
$subId4 = !empty($_POST['sub4']) ? $_POST['sub4'] : '';
$subId5 = !empty($_POST['sub5']) ? $_POST['sub5'] : '';

$fbpx = !empty($_POST['fbpx']) ? $_POST['fbpx'] : '';

$name = !empty($_POST['name']) ? $_POST['name'] : '';
$phone = !empty($_POST['phone']) ? $_POST['phone'] : '';

$infoData = [
    'country'    => null,               // страна доставки, если не будет передана - будет определена по IP адресу
    'fio'        => $name,              // Имя
    'phone'      => $phone,             // Телефон
    'user_ip'    => $userIp,            // ip пользователя
    'user_agent' => $userAgent,         // UserAgent пользователя
    'order_time' => time(),             // timestamp времени заказа
];


// id потока, например bakm
$flow = 'zkVz';

// 5 субайди, например subid1:subid2:subid3:subid4:subid5 (не обязательно)
$subid = implode(':', [$subId1, $subId2, $subId3, $subId4, $subId5]);

// ключ
$key = 'b66ff1a7957beafa8b501844e2d57683bcb75fed372264';

// домен API
$domain = 'offerrum.com';

$url = "https://api.{$domain}/webmaster/order/?key={$key}&flow={$flow}&subid={$subid}";

if (function_exists('curl_init') && $ch = curl_init()) {
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $infoData);
    curl_setopt($ch, CURLOPT_REFERER, $referer);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $result = curl_exec($ch);
    curl_close($ch);
}
else {
    $result = file_get_contents(
        $url,
        false,
        stream_context_create(
            [
                'http' => [
                    'method'  => 'POST',
                    'content' => http_build_query($infoData),
                    'header'  => "Content-Type: application/x-www-form-urlencoded\r\n" . "Referer: {$referer}\r\n",
                ],
            ]
        )
    );
}


$to = 'arb.traffic@ya.ru'; //Почта получателя, через запятую можно указать сколько угодно адресов
        $subject = 'AD1.2_Vanga_FB_UZ1'; //Загаловок сообщения
        $message = '
               <html>
                   <head>
                       <title>'.$subject.'</title>
                   </head>
                   <body>
                       <p>Товар:Money Amulet UZ_AD1_https://astral.extrashopping.ru/</p>
                       <p>Имя: '.$_POST['name'].'</p>
                       <p>Телефон: '.$_POST['phone'].'</p>
                       <p>_</p>
                   </body>
               </html>';
        $headers  = "Content-type: text/html; charset=utf-8 \r\n";
        $headers .= "From: Отправитель <from@example.com>\r\n";
        mail($to, $subject, $message, $headers);

//var_dump($result);
header('Location: success.html');

