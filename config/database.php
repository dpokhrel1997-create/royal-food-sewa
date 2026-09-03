<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'royal_food_sewa');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");

// Function to execute queries
function executeQuery($query, $params = []) {
    global $conn;
    
    if (!empty($params)) {
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        
        $types = '';
        foreach ($params as $param) {
            if (is_int($param)) $types .= 'i';
            elseif (is_float($param)) $types .= 'd';
            else $types .= 's';
        }
        
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt;
    } else {
        return $conn->query($query);
    }
}

// Function to fetch single row
function fetchOne($query, $params = []) {
    $result = executeQuery($query, $params);
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

// Function to fetch multiple rows
function fetchAll($query, $params = []) {
    $result = executeQuery($query, $params);
    $rows = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    }
    return $rows;
}

// Function to insert data
function insertData($table, $data) {
    global $conn;
    
    $columns = implode(',', array_keys($data));
    $values = implode(',', array_fill(0, count($data), '?'));
    
    $query = "INSERT INTO $table ($columns) VALUES ($values)";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    
    $types = '';
    $params = [];
    foreach ($data as $key => $value) {
        if (is_int($value)) $types .= 'i';
        elseif (is_float($value)) $types .= 'd';
        else $types .= 's';
        $params[] = $value;
    }
    
    $stmt->bind_param($types, ...$params);
    
    if ($stmt->execute()) {
        return $conn->insert_id;
    }
    return false;
}

// Function to update data
function updateData($table, $data, $where) {
    global $conn;
    
    $set = '';
    $params = [];
    
    foreach ($data as $key => $value) {
        $set .= "$key = ?, ";
        $params[] = $value;
    }
    
    $set = rtrim($set, ', ');
    
    $whereClause = '';
    foreach ($where as $key => $value) {
        $whereClause .= "$key = ? AND ";
        $params[] = $value;
    }
    
    $whereClause = rtrim($whereClause, ' AND ');
    
    $query = "UPDATE $table SET $set WHERE $whereClause";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    
    $types = '';
    foreach ($params as $param) {
        if (is_int($param)) $types .= 'i';
        elseif (is_float($param)) $types .= 'd';
        else $types .= 's';
    }
    
    $stmt->bind_param($types, ...$params);
    return $stmt->execute();
}

// Function to delete data
function deleteData($table, $where) {
    global $conn;
    
    $whereClause = '';
    $params = [];
    
    foreach ($where as $key => $value) {
        $whereClause .= "$key = ? AND ";
        $params[] = $value;
    }
    
    $whereClause = rtrim($whereClause, ' AND ');
    
    $query = "DELETE FROM $table WHERE $whereClause";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    
    $types = '';
    foreach ($params as $param) {
        if (is_int($param)) $types .= 'i';
        elseif (is_float($param)) $types .= 'd';
        else $types .= 's';
    }
    
    $stmt->bind_param($types, ...$params);
    return $stmt->execute();
}
?>
