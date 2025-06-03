<?php
require_once 'blog_functions.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                if (!empty($_POST['title']) && !empty($_POST['content'])) {
                    addArticle($_POST['title'], $_POST['content'], $_POST['link_url'], $_POST['link_text']);
                    $message = "Artikel berhasil ditambahkan!";
                }
                break;
            
            case 'delete':
                if (!empty($_POST['id'])) {
                    deleteArticle($_POST['id']);
                    $message = "Artikel berhasil dihapus!";
                }
                break;
            
            case 'update':
                if (!empty($_POST['id']) && !empty($_POST['title']) && !empty($_POST['content'])) {
                    updateArticle($_POST['id'], $_POST['title'], $_POST['content'], $_POST['link_url'], $_POST['link_text']);
                    $message = "Artikel berhasil diupdate!";
                }
                break;
        }
        
        // Redirect to prevent form resubmission
        header("Location: blog.php" . (isset($message) ? "?msg=" . urlencode($message) : ""));
        exit;
    }
}

// Get all articles
$articles = getAllArticles();

// Get article for editing if ID is provided
$editArticle = null;
if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $editArticle = getArticleById($_GET['edit']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Rieell - Blog</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Additional styles for admin panel */
        .admin-panel {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        body.dark-mode .admin-panel {
            background-color: #1a1a2e;
            border: 1px solid #333;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #0a2c5d;
        }
        
        body.dark-mode .form-group label {
            color: #4d76b2;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            font-family: inherit;
        }
        
        body.dark-mode .form-group input,
        body.dark-mode .form-group textarea {
            background-color: #0d2240;
            border-color: #333;
            color: #d1d5db;
        }
        
        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
        }
        
        .btn-primary {
            background-color: #0a2c5d;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #144a8a;
        }
        
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #c82333;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        
        .btn-small {
            padding: 0.4rem 0.8rem;
            font-size: 0.875rem;
        }
        
        .article-actions {
            margin-top: 1rem;
            border-top: 1px solid #eee;
            padding-top: 1rem;
        }
        
        body.dark-mode .article-actions {
            border-top-color: #333;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        body.dark-mode .alert-success {
            background-color: #1e3a1e;
            color: #7fc87f;
            border-color: #2a5a2a;
        }
        
        .admin-toggle {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 100;
        }
        
        @media (max-width: 768px) {
            .admin-toggle {
                top: 60px;
                right: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="dark-mode-toggle" id="darkModeToggle">🌓</div>
    
    <button class="btn btn-secondary btn-small admin-toggle" onclick="toggleAdmin()">
        <span id="adminToggleText">Tampilkan Admin Panel</span>
    </button>

    <header>
        <div class="logo">Rieell</div>
        <nav>
            <ul>
                <li><a href="../index.html">Home</a></li>
                <li><a href="gallery.html">Gallery</a></li>
                <li><a href="blog.php" class="active">Blog</a></li>
                <li><a href="contact.html">Contact</a></li>
            </ul>
        </nav>
    </header>

    <section id="blog" class="visible">
        <h2>Blog</h2>
        
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_GET['msg']); ?>
            </div>
        <?php endif; ?>
        
        <!-- Admin Panel -->
        <div class="admin-panel" id="adminPanel" style="display: none;">
            <h3><?php echo $editArticle ? 'Edit Artikel' : 'Tambah Artikel Baru'; ?></h3>
            <form method="POST" action="blog.php">
                <input type="hidden" name="action" value="<?php echo $editArticle ? 'update' : 'add'; ?>">
                <?php if ($editArticle): ?>
                    <input type="hidden" name="id" value="<?php echo $editArticle['id']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="title">Judul Artikel:</label>
                    <input type="text" id="title" name="title" required 
                           value="<?php echo $editArticle ? htmlspecialchars($editArticle['title']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="content">Konten Artikel:</label>
                    <textarea id="content" name="content" required 
                              placeholder="Gunakan \n untuk baris baru"><?php echo $editArticle ? htmlspecialchars($editArticle['content']) : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="link_url">URL Link (opsional):</label>
                    <input type="url" id="link_url" name="link_url" 
                           value="<?php echo $editArticle ? htmlspecialchars($editArticle['link_url']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="link_text">Teks Link (opsional):</label>
                    <input type="text" id="link_text" name="link_text" 
                           value="<?php echo $editArticle ? htmlspecialchars($editArticle['link_text']) : ''; ?>">
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <?php echo $editArticle ? 'Update Artikel' : 'Tambah Artikel'; ?>
                </button>
                
                <?php if ($editArticle): ?>
                    <a href="blog.php" class="btn btn-secondary">Batal</a>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- Articles Display -->
        <?php if (empty($articles)): ?>
            <p>Tidak ada artikel untuk ditampilkan.</p>
        <?php else: ?>
            <?php foreach ($articles as $article): ?>
                <article>
                    <h3><?php echo htmlspecialchars($article['title']); ?></h3>
                    <p class="article-date" style="font-size: 0.9rem; color: #666; margin-bottom: 1rem;">
                        Dipublikasikan: <?php echo formatDate($article['created_at']); ?>
                    </p>
                    <p><?php echo nl2br(htmlspecialchars($article['content'])); ?></p>
                    
                    <?php if (!empty($article['link_url']) && !empty($article['link_text'])): ?>
                        <p>
                            <a href="<?php echo htmlspecialchars($article['link_url']); ?>" target="_blank">
                                <?php echo htmlspecialchars($article['link_text']); ?>
                            </a>
                        </p>
                    <?php endif; ?>
                    
                    <div class="article-actions" id="actions-<?php echo $article['id']; ?>" style="display: none;">
                        <a href="blog.php?edit=<?php echo $article['id']; ?>" class="btn btn-secondary btn-small">Edit</a>
                        
                        <form method="POST" action="blog.php" style="display: inline-block;" 
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus artikel ini?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $article['id']; ?>">
                            <button type="submit" class="btn btn-danger btn-small">Hapus</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

    <footer>
        <p>&copy; 2025 Website Rieell - Semua Hak Dilindungi</p>
    </footer>

    <div class="back-to-top" id="backToTop">↑</div>

    <script src="script.js"></script>
    <script>
        // Function to toggle admin panel
        function toggleAdmin() {
            const adminPanel = document.getElementById('adminPanel');
            const toggleText = document.getElementById('adminToggleText');
            const allActions = document.querySelectorAll('[id^="actions-"]');
            
            if (adminPanel.style.display === 'none') {
                adminPanel.style.display = 'block';
                toggleText.textContent = 'Sembunyikan Admin Panel';
                allActions.forEach(action => action.style.display = 'block');
            } else {
                adminPanel.style.display = 'none';
                toggleText.textContent = 'Tampilkan Admin Panel';
                allActions.forEach(action => action.style.display = 'none');
            }
        }
        
        // Auto-expand textarea
        document.getElementById('content').addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
        
        // Show admin panel if editing
        <?php if ($editArticle): ?>
            document.addEventListener('DOMContentLoaded', function() {
                toggleAdmin();
                document.getElementById('adminPanel').scrollIntoView({behavior: 'smooth'});
            });
        <?php endif; ?>
    </script>
</body>
</html>