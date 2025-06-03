<?php
// blog_functions.php - Fungsi-fungsi untuk blog
require_once 'config.php';

// Fungsi untuk mengambil semua artikel
function getAllArticles() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM articles ORDER BY created_at DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fungsi untuk menambah artikel baru
function addArticle($title, $content, $link_url = '', $link_text = '') {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO articles (title, content, link_url, link_text) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$title, $content, $link_url, $link_text]);
}

// Fungsi untuk menghapus artikel
function deleteArticle($id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
    return $stmt->execute([$id]);
}

// Fungsi untuk mengambil artikel berdasarkan ID
function getArticleById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fungsi untuk update artikel
function updateArticle($id, $title, $content, $link_url = '', $link_text = '') {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE articles SET title = ?, content = ?, link_url = ?, link_text = ? WHERE id = ?");
    return $stmt->execute([$title, $content, $link_url, $link_text, $id]);
}

// Fungsi untuk format tanggal
function formatDate($date) {
    return date('d M Y', strtotime($date));
}
?>