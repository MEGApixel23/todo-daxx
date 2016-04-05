<?php
header('Content-Type: application/json');

require_once (__DIR__ . '/Todo.php');

$response = call_user_func(function() {
    $handler = new Todo();

    $action = isset($_GET['action']) ? (string) $_GET['action'] : null;
    $method = 'action' . ucfirst($action);

    if (!method_exists($handler, $method)) {
        http_response_code(404);
        return ['error' => 'Wrong action!'];
    }

    if ($action === 'list') {
        return $handler->$method();
    } elseif ($action === 'add') {
        return $handler->$method($_POST);
    } elseif ($action === 'delete') {
        if (!isset($_GET['id']))
            return ['error' => 'Id parameter is required!'];

        return $handler->$method((int) $_GET['id']);
    } elseif ($action === 'update') {
        if (!isset($_GET['id']))
            return ['error' => 'Id parameter is required!'];

        return $handler->$method((int) $_GET['id'], $_POST);
    }

    return [];
});

echo json_encode($response);