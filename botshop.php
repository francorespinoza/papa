<?php
define('TELEGRAM_TOKEN', '7864870582:AAEvnEOymD2raTaxsVEnR3q_9cqQO6l5jDI'); // Reemplaza 'YOUR_TOKEN' con el token de tu bot

// Direcciones de wallet
define('WALLET_USDT', 'TMrnqWzmTJ2hCpzvRxtrxs8dV9d3gRJniH'); // Reemplaza 'TU_DIRECCION_BTC' con tu dirección de usdt
define('WALLET_ETH', '0xB6a26F64F9c566Be6CdC0d89AE8C68A108Efca9C'); // Reemplaza 'TU_DIRECCION_ETH' con tu dirección de Ethereum

// Tu ID de usuario para recibir imágenes
define('YOUR_USER_ID', '2130657290'); // Reemplaza 'TU_USER_ID' con tu ID de usuario de Telegram

// Lista de productos
$products = [
    '1' => ['name' => 'PACK COOKIES AND ACCESS BANK AND PAYPAL', 'price' => 120, 'payment_info' => 'Detalles de pago para PACK COOKIES AND ACCESS BANK AND PAYPAL'],
    '2' => ['name' => 'Scam Alquiler Jetsmart | Jetsmart | BANCOS', 'price' => 150, 'payment_info' => 'Detalles de pago para Scam Alquiler DGT | Jetsmart | BANCOS'],
    '3' => ['name' => 'Leads DGT | Jetsmart | BANCOS', 'price' => 50, 'payment_info' => 'Detalles de pago para Leads DGT | Jetsmart | BANCOS'],
    '4' => ['name' => 'Spam DGT | Jetsmart | BANCOS', 'price' => 150, 'payment_info' => 'Detalles de pago para Spam DGT | Jetsmart | BANCOS'],
    '5' => ['name' => 'Esim O2', 'price' => 30, 'payment_info' => 'Detalles de pago para Esim O2'],
    '6' => ['name' => 'Esim O2 x3 unid', 'price' => 50, 'payment_info' => 'Detalles de pago para Esim O2 x3'],
    '7' => ['name' => 'Esim O2 x12 unid', 'price' => 120, 'payment_info' => 'Detalles de pago para Esim O2 x12'],
    '8' => ['name' => 'CC x1 unid', 'price' => 14, 'payment_info' => 'Detalles de pago para CC x1 unid'],
    '9' => ['name' => 'CC x3 unid', 'price' => 30, 'payment_info' => 'Detalles de pago para CC x3 unid'],
    '10' => ['name' => 'Pack 10 bank logs', 'price' => 50, 'payment_info' => 'Detalles de pago para 10 accesos bancarios españa fresh'],
    '11' => ['name' => 'Pack 30 bank logs', 'price' => 90, 'payment_info' => 'Detalles de pago para 10 accesos bancarios españa fresh'],
    '12' => ['name' => 'Proxys Pack Socks5', 'price' => 100, 'payment_info' => 'Detalles de pago para pack 10.000 proxys'],
    '13' => ['name' => 'GLOVO METODO', 'price' => 50, 'payment_info' => 'Detalles de pago GLOVO METODO'],
    '14' => ['name' => 'ZALANDO METODO', 'price' => 50, 'payment_info' => 'Detalles de pago ZALANDO METODO'],
    '15' => ['name' => 'Checker live|dead|balance', 'price' => 60, 'payment_info' => 'Checker live|dead|balance'],
    '16' => ['name' => 'Darkgpt', 'price' => 30, 'payment_info' => 'Darkgpt'],
    '17' => ['name' => 'Scam Zip Jetsmart | Jetsmart | BANCOS', 'price' => 300, 'payment_info' => 'Detalles de pago para Scam DGT | Jetsmart | BANCOS'],
    '18' => ['name' => 'GOOGLE DEVELOPER ACCOUNT', 'price' => 55, 'payment_info' => 'Detalles de pago para GOOGLE DEVELOPER ACCOUNT'],
    '19' => ['name' => 'GIFT CARD REPSOL', 'price' => 100, 'payment_info' => 'Detalles de pago para GIFCARD REPSOL'],
    '20' => ['name' => 'DOX ANY WEB', 'price' => 100, 'payment_info' => 'Detalles de pago para DOX ANY WEB'],
    '21' => ['name' => 'Proxys Pack RESIDENCIAL', 'price' => 100, 'payment_info' => 'Detalles de pago para MEMBRESIA 1 MES proxys'],
    '22' => ['name' => 'BOT BYPASS OTP', 'price' => 100, 'payment_info' => 'Detalles de pago para BOT BYPASS OTP'],

];

// Almacenar los tiempos de los últimos mensajes
$userLastMessageTime = [];

// Conexión a la base de datos
$servername = "localhost"; // Cambia esto si es necesario
$username = "root"; // Cambia esto por tu usuario de MySQL
$password = ""; // Cambia esto por tu contraseña de MySQL
$dbname = "teleg"; // Cambia esto por el nombre de tu base de datos

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener actualizaciones
$update = json_decode(file_get_contents('php://input'), true);

if (isset($update['message'])) {
    $chatId = $update['message']['chat']['id'];
    $username = isset($update['message']['chat']['username']) ? $update['message']['chat']['username'] : 'Sin nombre de usuario';
    $text = $update['message']['text'];

    // Verificar si el usuario está registrado
    if (!isUserRegistered($chatId, $conn)) {
        if ($text === '/register') {
            registerUser($chatId, $username, $conn);
        } else {
            sendMessage($chatId, "Por favor, regístrate primero usando el comando /register.");
            return;
        }
    }

    // Control de antispam
    $currentTime = time();
    if (isset($userLastMessageTime[$chatId]) && ($currentTime - $userLastMessageTime[$chatId]) < 10) { // 10 segundos de espera
        sendMessage($chatId, "Por favor, espera un momento antes de enviar otro mensaje.");
        return;
    }
    $userLastMessageTime[$chatId] = $currentTime;

    if ($text === '/start') {
        displayProductList($chatId);
    } elseif ($text === '🔙 Regresar') {
        displayProductList($chatId);
    } elseif (array_search($text, array_column($products, 'name')) !== false) {
        $productId = array_search($text, array_column($products, 'name'));
        $product = $products[$productId + 1]; // +1 porque las claves comienzan en 1

        $message = "Has seleccionado: {$product['name']}\nPrecio: {$product['price']} €\n{$product['payment_info']}\nElige tu método de pago:";
        $keyboard = [
            [['text' => 'Pagar con WALLET_ETH'], ['text' => 'Pagar con WALLET_USDT']],
            [['text' => '🔙 Regresar']]
        ];
        $replyMarkup = json_encode([
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true,
        ]);

        sendMessage($chatId, $message, $replyMarkup);
    } elseif ($text === 'Pagar con WALLET_ETH') {
        $message = "Has elegido pagar con WALLET_ETH.\nPor favor, envía el pago a la siguiente dirección ERC20:\n" . WALLET_ETH;
        $keyboard = [
            [['text' => 'Adjuntar Voucher'], ['text' => '🔙 Regresar']]
        ];
        $replyMarkup = json_encode([
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true,
        ]);
        sendMessage($chatId, $message, $replyMarkup);
    } elseif ($text === 'Pagar con WALLET_USDT') {
        $message = "Has elegido pagar con WALLET_USDT.\nPor favor, envía el pago a la siguiente dirección TRC20:\n" . WALLET_USDT;
        $keyboard = [
            [['text' => 'Adjuntar Voucher'], ['text' => '🔙 Regresar']]
        ];
        $replyMarkup = json_encode([
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true,
        ]);
        sendMessage($chatId, $message, $replyMarkup);
    } elseif (isset($update['message']['photo'])) {
        // Manejar la foto del voucher
        $photo = $update['message']['photo'];
        $fileId = end($photo)['file_id']; // Obtener el ID del archivo de la última foto enviada
    
        // Obtener el username del usuario
        $username = isset($update['message']['chat']['username']) ? $update['message']['chat']['username'] : 'Sin nombre de usuario';
    
        // Enviar la foto al usuario específico
        sendPhotoToUser(YOUR_USER_ID, $fileId);
        
        // Mensaje de agradecimiento incluyendo el username
        sendMessage($chatId, "¡Gracias, @$username! He recibido tu voucher.");
        
        // Agregar el botón "He Pagado"
        $keyboard = [
            [['text' => 'He Pagado']],
            [['text' => '🔙 Regresar']]
        ];
        $replyMarkup = json_encode([
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true,
        ]);
        sendMessage($chatId, "¿Has pagado?", $replyMarkup);
    }
    
}


function isUserRegistered($chatId, $conn) {
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE chat_id = ?");
    $stmt->bind_param("i", $chatId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0; // Retorna verdadero si el usuario está registrado
}

function registerUser($chatId, $username, $conn) {
    $stmt = $conn->prepare("INSERT INTO usuarios (chat_id, username) VALUES (?, ?)");
    $stmt->bind_param("is", $chatId, $username);
    if ($stmt->execute()) {
        sendMessage($chatId, "¡Registro exitoso! Ahora puedes usar el bot. Envia /start");
    } else {
        sendMessage($chatId, "Error al registrarte. Por favor, intenta de nuevo.");
    }
}

function displayProductList($chatId) {
    global $products;

    $keyboard = [];
    foreach ($products as $product) {
        $keyboard[] = [$product['name']];
    }

    $keyboard[] = [['text' => '🔙 Regresar']]; // Botón de regresar

    $replyMarkup = json_encode([
        'keyboard' => $keyboard,
        'resize_keyboard' => true,
        'one_time_keyboard' => true,
    ]);

    sendMessage($chatId, 'Selecciona un producto:', $replyMarkup);
}

function sendMessage($chatId, $message, $replyMarkup = null) {
    $url = "https://api.telegram.org/bot" . TELEGRAM_TOKEN . "/sendMessage";

    $data = [
        'chat_id' => $chatId,
        'text' => $message,
        'reply_markup' => $replyMarkup,
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ],
    ];
    $context  = stream_context_create($options);
    file_get_contents($url, false, $context);
}

function sendPhotoToUser($userId, $fileId) {
    $url = "https://api.telegram.org/bot" . TELEGRAM_TOKEN . "/sendPhoto";

    $data = [
        'chat_id' => $userId,
        'photo' => $fileId,
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ],
    ];
    $context  = stream_context_create($options);
    file_get_contents($url, false, $context);
}
