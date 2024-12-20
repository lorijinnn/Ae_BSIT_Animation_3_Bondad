<?php
include('db.php');
include('header.php');
session_start();

// Fetch all users
$query = "SELECT * FROM users";
$result = $conn->query($query);
$users = $result->fetch_all(MYSQLI_ASSOC);
?>
<h2>Manage Users</h2>
<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Full Name</th>
      <th>Username</th>
      <th>Role</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($users as $user): ?>
      <tr>
        <td><?php echo $user['user_id']; ?></td>
        <td><?php echo $user['fullname']; ?></td>
        <td><?php echo $user['username']; ?></td>
        <td><?php echo ucfirst($user['role']); ?></td>
        <td>
          <?php if ($user['role'] != 'admin'): ?>
            <a href="ban_user.php?id=<?php echo $user['user_id']; ?>">Ban</a>
            <a href="delete_user.php?id=<?php echo $user['user_id']; ?>">Remove</a>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
