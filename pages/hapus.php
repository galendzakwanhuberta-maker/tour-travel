<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
if (!$id) redirect('/pages/paket.php');

$stmt = $conn->prepare("SELECT judul FROM paket_wisata WHERE id=?");
$stmt->bind_param('i',$id); $stmt->execute();
$paket = $stmt->get_result()->fetch_assoc();

if (!$paket) {
    setFlash('error','Paket tidak ditemukan.');
    redirect('/pages/paket.php');
}

$del = $conn->prepare("DELETE FROM paket_wisata WHERE id=?");
$del->bind_param('i',$id);
if ($del->execute()) {
    setFlash('success','Paket "'.htmlspecialchars($paket['judul']).'" berhasil dihapus.');
} else {
    setFlash('error','Gagal menghapus paket.');
}
redirect('/pages/paket.php');