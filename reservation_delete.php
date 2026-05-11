<?php
require_once 'auth.php';
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id   = (int)$_POST['id'];
    $uid  = $_SESSION['user_id'];
    $role = $_SESSION['user_role'];

    if ($role !== 'admin') {
        $chk = $conn->prepare("SELECT id FROM reservasi WHERE id=? AND user_id=?");
        $chk->bind_param("ii",$id,$uid); $chk->execute();
        if ($chk->get_result()->num_rows === 0) { header("Location: index.php"); exit; }
    }

    $st = $conn->prepare("DELETE FROM reservasi WHERE id=?");
    $st->bind_param("i",$id);
    $st->execute();
    $_SESSION['msg_del'] = '✓ Reservasi berhasil dihapus.';
}
header("Location: index.php#reservasi"); exit;
