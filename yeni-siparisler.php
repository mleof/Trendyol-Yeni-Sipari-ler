<?php
$username = "API Key KODU";
$password = "API Secret KODU";
$supplierId = "Satıcı ID (Cari ID) KODU";

$auth = base64_encode("$username:$password");

$url = "https://api.trendyol.com/sapigw/suppliers/$supplierId/orders?status=Created";

$headers = [
    "Authorization: Basic $auth",
    "Content-Type: application/json"
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo "cURL Hatası: " . curl_error($ch);
} else {
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpCode == 200) {
        $data = json_decode($response, true);
        if (isset($data['content']) && is_array($data['content']) && count($data['content']) > 0) {
            $orders = $data['content'];
            $orderCount = count($orders);
            echo "<h2>$orderCount adet siparişiniz bulunmakta.</h2>";
            echo "<div class='orders'>";
            foreach ($orders as $order) {
                echo "<div class='order-container'>";
                echo "<h3>Sipariş No: " . htmlspecialchars($order["orderNumber"]) . "</h3>";
                echo "<p><strong>Müşteri:</strong> " . htmlspecialchars($order["customerFirstName"] . " " . $order["customerLastName"]) . "</p>";
                echo "<p><strong>Adres:</strong> " . htmlspecialchars($order["shipmentAddress"]["fullAddress"]) . "</p>";
                echo "<h4>Aldığı Ürünler:</h4><ul>";
                if (isset($order["lines"]) && is_array($order["lines"])) {
                    foreach ($order["lines"] as $item) {
                        echo "<li>" . htmlspecialchars($item["productName"]) . " - " . $item["quantity"] . " adet</li>";
                    }
                } else {
                    echo "<li>Ürün bilgisi bulunamadı.</li>";
                }
                echo "</ul>";
              
                echo "<p><strong>Kargo Takip No:</strong> " . htmlspecialchars($order["cargoTrackingNumber"]) . "</p>";
                echo "<img src='https://barcode.tec-it.com/barcode.ashx?data=" . urlencode($order["cargoTrackingNumber"]) . "&code=Code128' alt='Barkod' />";
               
                echo "</div>";
            }
            echo "</div>";
            echo "<button onclick='window.print()'>Siparişleri Yazdır</button>";
        } else {
            echo "<h2>Yeni bir siparişiniz bulunmuyor.</h2>";
        }
    } else {
        echo "API Hatası! HTTP Kodu: $httpCode <br>";
        echo "Yanıt: " . $response;
    }
}

curl_close($ch);
?>

<style>
body {
    background-color: white;
    font-family: Arial, sans-serif;
}
.orders {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
}
.order-container {
    width: 48%;
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    page-break-inside: avoid;
}
@media print {
    .order-container {
        width: 48%;
    }
    button {
        display: none;
    }
}
</style>
