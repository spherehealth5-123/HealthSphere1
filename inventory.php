<?php
header("Content-Type: application/json");
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = "localhost";
$user = "root";
$pass = "";
$db   = "healthsphere";

try {
    $conn = new mysqli($host, $user, $pass, $db);
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => "Connection failed"]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    try {
        $result = $conn->query("SELECT * FROM inventory ORDER BY id DESC");
        $rows = [];
        while($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        echo json_encode($rows);
    } catch (Exception $e) {
        echo json_encode([]);
    }
    exit;
}

if ($method === 'POST') {
    $rawInput = file_get_contents("php://input");
    $data = json_decode($rawInput, true);

    $required = ['productName', 'itemNo', 'manufacturer', 'category', 'price', 'quantity', 'expiryDate'];
    foreach ($required as $field) {
        if (!isset($data[$field]) || $data[$field] === '') {
            echo json_encode(["success" => false, "error" => "Missing field: $field"]);
            exit;
        }
    }

    try {
        $stmt = $conn->prepare("INSERT INTO inventory (product_name, item_no, manufacturer, category, price, quantity, expiry_date) VALUES (?, ?, ?, ?, ?, ?, ?)");

        $pName = (string)$data['productName'];
        $iNo   = (string)$data['itemNo'];
        $mfg   = (string)$data['manufacturer'];
        $cat   = (string)$data['category'];
        $prc   = (float)$data['price'];
        $qty   = (int)$data['quantity'];
        $exp   = (string)$data['expiryDate'];

        $stmt->bind_param("ssssdis", $pName, $iNo, $mfg, $cat, $prc, $qty, $exp);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => $stmt->error]);
        }

        $stmt->close();
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            echo json_encode(["success" => false, "error" => "This Item Number/SKU already exists."]);
        } else {
            echo json_encode(["success" => false, "error" => $e->getMessage()]);
        }
    }
}
$conn->close();
?>