<?php
function addUser($data) {
    global $conn;

    $username = $data['username'];
    $password = password_hash($data['password'], PASSWORD_DEFAULT); // Hash the password for security
    $nama_lengkap = $data['nama_lengkap'];
    $role_id = $data['role_id'];

    $query = "INSERT INTO users (username, password, nama_lengkap, role_id) VALUES ('$username', '$password', '$nama_lengkap', '$role_id')";
    return mysqli_query($conn, $query);
}

function getUsers() {
    global $conn;

    $query = "SELECT * FROM users";
    $result = mysqli_query($conn, $query);

    $users = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }

    return $users;
}

function toggleUserStatus($user_id) {
    global $conn;
    $id = (int)$user_id;

    $query = "UPDATE users SET status = NOT status WHERE id = $id";
    return mysqli_query($conn, $query);
}

function resetPassword($user_id, $new_password) {
    global $conn;
    $id = (int)$user_id;
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    $query = "UPDATE users SET password = '$hashed_password' WHERE id = $id";
    return mysqli_query($conn, $query);
}
?>