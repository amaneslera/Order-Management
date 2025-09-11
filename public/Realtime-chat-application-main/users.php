<?php
if (isset($_GET['user']) && isset($_GET['role'])) {
    session_start();
    $_SESSION['username'] = $_GET['user'];
    $_SESSION['role'] = $_GET['role'];
    include_once "php/config.php";
    $username = mysqli_real_escape_string($conn, $_SESSION['username']);
    $sql = "SELECT * FROM users WHERE username = '{$username}' LIMIT 1";
    $query = mysqli_query($conn, $sql);
    if (mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_assoc($query);
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['role'] = $row['role'];
    } else {
        echo "<h2>User not found.</h2>";
        exit;
    }
} else {
    session_start();
}
?>

<?php 
  include_once "php/config.php";
  if(!isset($_SESSION['user_id'])){
    echo "<h2>Access denied. Please log in through the main application.</h2>";
    exit;
  }
?>

<?php include_once "header.php"; ?>
<body>
  <div class="wrapper">
    <section class="users">
      <header>
        <div class="content">
          <?php 
            $sql = mysqli_query($conn, "SELECT * FROM users WHERE id = {$_SESSION['user_id']}");
            if(mysqli_num_rows($sql) > 0){
              $row = mysqli_fetch_assoc($sql);
            }
          ?>
          <div class="details">
            <span><?php echo htmlspecialchars($row['username']); ?> (<?php echo htmlspecialchars($row['role']); ?>)</span>
          </div>
        </div>
        <!-- Removed logout button -->
      </header>
      <div class="search">
        <span class="text">Choose buddy to Start Chatting</span>
        <input type="text" placeholder="Enter name to search...">
        <button><i class="fas fa-search"></i></button>
      </div>
      <div class="users-list">
  
      </div>
    </section>
  </div>

  <script src="javascript/users.js"></script>

</body>
</html>
