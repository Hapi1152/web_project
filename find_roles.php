<?php
function get_roles($user_id)
{
    $dsn = "pgsql:host=localhost;port=5432;dbname=postgres;";
    $pdo = new PDO($dsn, 'postgres', '7746597Ss');

    $sql = 'select r.name
                  from user_roles ur
                       join roles r on r.id = ur.role_id
                 where ur.user_id = :user_id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
}
?>
