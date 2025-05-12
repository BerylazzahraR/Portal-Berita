<?php
class Article {
    private $conn;
    private $table = "articles";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getCategories() {
        $stmt = $this->conn->prepare("SELECT DISTINCT category FROM $this->table");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getViewsMap() {
        $stmt = $this->conn->prepare("SELECT id, views FROM $this->table");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $viewsMap = [];
        foreach ($result as $row) {
            $viewsMap[$row['id']] = $row['views'];
        }
    
        return $viewsMap;
    }
    

    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM $this->table ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt;
    }
    public function getViewsPerArticle() {
        $stmt = $this->conn->prepare("SELECT title, views FROM $this->table ORDER BY views DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        // Tambah jumlah views saat artikel diakses
        $update = $this->conn->prepare("UPDATE $this->table SET views = views + 1 WHERE id = ?");
        $update->execute([$id]);

        // Ambil data artikel
        $stmt = $this->conn->prepare("SELECT * FROM $this->table WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($title, $category, $image, $content) {
        $stmt = $this->conn->prepare("INSERT INTO $this->table (title, category, image, content) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$title, $category, $image, $content]);
    }

    public function update($id, $title, $category, $image, $content) {
        $stmt = $this->conn->prepare("UPDATE $this->table SET title=?, category=?, image=?, content=? WHERE id=?");
        return $stmt->execute([$title, $category, $image, $content, $id]);
    }


    public function getFiltered($search = '', $category = '', $order = 'newest') {
        $sql = "SELECT * FROM $this->table WHERE 1=1";
        $params = [];
    
        if (!empty($search)) {
            $sql .= " AND title LIKE ?";
            $params[] = "%$search%";
        }
    
        if (!empty($category)) {
            $sql .= " AND category = ?";
            $params[] = $category;
        }
    
        switch ($order) {
            case 'oldest':
                $sql .= " ORDER BY created_at ASC";
                break;
            case 'most_viewed':
                $sql .= " ORDER BY views DESC";
                break;
            default:
                $sql .= " ORDER BY created_at DESC";
        }
    
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM $this->table WHERE id=?");
        return $stmt->execute([$id]);
    }
}


