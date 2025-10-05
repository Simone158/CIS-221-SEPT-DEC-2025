<?php
// ===== Fashion Store - Part 1 (Q1) =====

// Connect to MySQL
$host = "localhost";
$user = "root"; // change if needed
$pass = "";
$db   = "fashion_store";

$conn = new mysqli($host, $user, $pass);

// Create database if not exists
$conn->query("CREATE DATABASE IF NOT EXISTS $db");
$conn->select_db($db);

// Create tables
$conn->query("
    CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100),
        price DECIMAL(10,2),
        quantity INT
    )
");

$conn->query("
    CREATE TABLE IF NOT EXISTS sales (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT,
        quantity INT,
        total DECIMAL(10,2),
        sale_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

// Insert sample products (if table empty)
$check = $conn->query("SELECT COUNT(*) AS c FROM products")->fetch_assoc();
if ($check['c'] == 0) {
    $conn->query("
        INSERT INTO products (name, price, quantity) VALUES
        ('T-shirt', 800, 30),
        ('Jeans', 2000, 25),
        ('Sneakers', 3500, 20),
        ('Dress', 2500, 15),
        ('Cap', 500, 40)
    ");
}

// Record a sale (example)
$product_id = 1;   // sold product ID 1
$sold_qty   = 2;   // sold quantity

$p = $conn->query("SELECT * FROM products WHERE id=$product_id")->fetch_assoc();

if ($p && $p['quantity'] >= $sold_qty) {
    $total = $p['price'] * $sold_qty;
    $conn->query("UPDATE products SET quantity = quantity - $sold_qty WHERE id=$product_id");
    $conn->query("INSERT INTO sales (product_id, quantity, total) VALUES ($product_id, $sold_qty, $total)");
    echo "✅ Sale recorded: {$p['name']} × $sold_qty (Total: Ksh $total)<br><br>";
} else {
    echo "❌ Not enough stock for {$p['name']}<br><br>";
}

// Show inventory summary
echo "<b>Inventory Summary:</b><br>";
$res = $conn->query("SELECT * FROM products");
while ($r = $res->fetch_assoc()) {
    echo "{$r['id']}. {$r['name']} — Price: Ksh {$r['price']} — Qty: {$r['quantity']}<br>";
}

echo "<br><b>Sales Summary:</b><br>";
$res2 = $conn->query("
    SELECT s.id, p.name, s.quantity, s.total, s.sale_date
    FROM sales s JOIN products p ON s.product_id = p.id
");
while ($r = $res2->fetch_assoc()) {
    echo "Sale {$r['id']}: {$r['name']} × {$r['quantity']} — Total: Ksh {$r['total']} — Date: {$r['sale_date']}<br>";
}
?>
