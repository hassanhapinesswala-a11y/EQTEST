?php
// db.php
// Database connection using PDO. Update if needed.
$DB_HOST = 'localhost';
$DB_NAME = 'dbfwbcq8tyu1ba';
$DB_USER = 'uyhezup6l0hgf';
$DB_PASS = 'pr634bpk3knb';
 
try {
    $dsn = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4";
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    // friendly error
    echo "<h2>Database connection failed.</h2><p>Check credentials and that the DB exists.</p>";
    // for development you can uncomment the next line:
    // echo "<pre>" . $e->getMessage() . "</pre>";
    exit;
}
