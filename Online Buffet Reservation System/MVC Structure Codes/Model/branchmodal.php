<?php
// /Model/branchmodal.php

function get_all_branches($pdo) {
    $stmt = $pdo->query("SELECT * FROM branch_details ORDER BY branch_name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}