<?php
function get_json($statement)
{
    error_reporting(0);

    session_start();
    if (!array_key_exists('IS_AUTH', $_SESSION)) {
        $result_data = [
            "error" => 1,
            "errorMsg" => "Вы не авторизованы."
        ];
        return json_encode($result_data, JSON_UNESCAPED_UNICODE);
    }

    header('Content-Type: application/json');
    $result_data = [];

    try {
        $statement->execute();
        $result_list = $statement->fetchAll(PDO::FETCH_ASSOC);
        $result_data = [
            "error" => 0,
            "data" => $result_list
        ];
    } catch (PDOException $e) {
        $result_data = [
            "error" => 1,
            "errorMsg" => $e->getMessage()
        ];
    }

    return json_encode($result_data, JSON_UNESCAPED_UNICODE);
}

$dsn = "pgsql:host=localhost;port=5432;dbname=postgres;";
$pdo = new PDO($dsn, 'postgres', '7746597Ss');
?>
