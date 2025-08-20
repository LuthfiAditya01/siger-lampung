<?php
session_start();
require_once 'koneksi.php';

if (!empty($_SESSION['alert_success'])) {
    echo "<script>alert('" . addslashes($_SESSION['alert_success']) . "');</script>";
    unset($_SESSION['alert_success']);
}

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
  header("Location: admin/index.php");
  exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  session_start();
  $username = $_POST['username'];
  $password = $_POST['password'];

  $query = "SELECT * FROM users WHERE username = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['username'] = $user['username'];
      $_SESSION['alert_success'] = "Berhasil login sebagai " . $user['username'] . "!";
      header("Location: admin/beranda");
      exit();
    } else {
      $error = "Password salah!";
    }
  } else {
    $error = "Username tidak ditemukan!";
  }
}



?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  <link rel="stylesheet" href="css/output.css" />
</head>

<body>
  <div class="min-h-screen min-w-max flex items-center justify-center" style="background-color:#eb891b">
    <div class="bg-white p-8 rounded-lg shadow-lg w-96">
      <div class="flex justify-center mb-6">
        <img src="img/logo.jpg" class="w-32 h-auto" alt="Logo Siger Bandar Lampung">
      </div>
      <?php if (isset($error)): ?>
        <div class="mb-4 p-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
          <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>
      <form class="mx-auto" method="POST" action="">
        <div class="mb-5">
          <label
            for="username"
            class="block mb-2 text-sm font-medium text-gray-900">Username</label>
          <input
            type="text"
            id="username"
            name="username"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
            required />
        </div>
        <div class="mb-5">
          <label
            for="password"
            class="block mb-2 text-sm font-medium text-gray-900">Password</label>
          <input
            type="password"
            id="password"
            name="password"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
            required />
        </div>
        <button
          type="submit"
          class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full px-5 py-2.5 text-center">
          Login
        </button>
      </form>
    </div>
  </div>
</body>

</html>