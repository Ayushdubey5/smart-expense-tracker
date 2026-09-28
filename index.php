<?php
session_start();
if (!empty($_SESSION['user_id'])) {
  header('Location: ' . ($_SESSION['role']==='admin' ? 'admin/dashboard.php' : 'student/dashboard.php'));
} else {
  header('Location: auth/login.php');
}
exit;