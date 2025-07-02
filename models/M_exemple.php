<?php

require_once 'config/connetion.php';

class Books {

    private static function db(){
        global $pdo;
        return $pdo;
    }

    public static function all() {
        $stmt = self::db()->query('SELECT * FROM books');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find($id) {
        $stmt = self::db()->prepare('SELECT * FROM books WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data, $image = null) {
        $db = self::db();

        $imageName = null;
        if ($image) {
            $imageName = self::uploadImage($image);
        }

        $stmt = $db->prepare("INSERT INTO books (title, author, published_year, category_id, content, image) 
                              VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['title'],
            $data['author'],
            $data['published_year'],
            $data['category_id'],
            $data['content'],
            $imageName
        ]);
    }

    public static function update($id, $data, $image = null) {
        $db = self::db();

        $imageName = $image ? self::uploadImage($image) : null;

        $fields = ['title = ?', 'author = ?', 'published_year = ?', 'category_id = ?', 'content = ?'];
        $params = [
            $data['title'],
            $data['author'],
            $data['published_year'],
            $data['category_id'],
            $data['content']
        ];

        if ($imageName) {
            $fields[] = 'image = ?';
            $params[] = $imageName;
        }

        $params[] = $id;

        $sql = "UPDATE books SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
    }

    public static function delete($id) {
        $stmt = self::db()->prepare("DELETE FROM books WHERE id = ?");
        return $stmt->execute([$id]);
    }

    private static function uploadImage($image) {
        $targetDir = "uploads/images/";
        $imageName = basename($image["name"]);
        $targetFile = $targetDir . $imageName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        $check = getimagesize($image["tmp_name"]);
        if ($check === false) {
            throw new Exception("File bukan gambar.");
        }

        if (file_exists($targetFile)) {
            throw new Exception("File sudah ada.");
        }

        if ($image["size"] > 5000000) {
            throw new Exception("File terlalu besar (maksimal 5MB).");
        }

        if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
            throw new Exception("Format file tidak diizinkan.");
        }

        if (move_uploaded_file($image["tmp_name"], $targetFile)) {
            return $imageName;
        } else {
            throw new Exception("Gagal mengunggah gambar.");
        }
    }
    public static function getTotalBooks() {
        $stmt = self::db()->query("SELECT COUNT(*) FROM books");
        return $stmt->fetchColumn();
    }
    
    public static function getTotalCategories() {
        $stmt = self::db()->query("SELECT COUNT(DISTINCT category_id) FROM books");
        return $stmt->fetchColumn();
    }
    
    // public static function getRecommendedBooks() {
    //     $stmt = self::db()->query("SELECT COUNT(*) FROM books WHERE recommended = 1");
    //     return $stmt->fetchColumn();
    // }
    
}
