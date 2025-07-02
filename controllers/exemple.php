<?php

class BooksController {

    public function index() {
        Tampilan('');
    }

    public function create() {
        Tampilan('');
    }

    public function store() {
        session_start();
        $data = [
            'title' => $_POST['title'],
            'author' => $_POST['author'],
            'published_year' => $_POST['published_year'],
            'category_id' => $_POST['category_id'],
            'content' => $_SESSION['book_content'] ?? $_POST['content']
        ];

        $image = isset($_FILES['image']) ? $_FILES['image'] : null;

        try {
            Books::create($data, $image);
            unset($_SESSION['book_content']); 
            $_SESSION['success_message'] = 'Buku berhasil ditambahkan!';
            redirect('/addbook');
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
            redirect('/books/create');
        }
    }

    public function edit() {
        $book = Books::find($_GET['id']);
        $categories = Categories::getCategory();
        Tampilan('books/edit', compact('book', 'categories'), 'dashboard/index');
    }

    public function update() {
        $data = [
            'title' => $_POST['title'],
            'author' => $_POST['author'],
            'published_year' => $_POST['published_year'],
            'category_id' => $_POST['category_id'],
            'content' => $_POST['content']
        ];

        $image = isset($_FILES['image']) ? $_FILES['image'] : null;

        try {
            Books::update($_POST['id'], $data, $image);
            $_SESSION['success_message'] = 'Buku berhasil diperbarui!';
            redirect('/addbook');
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
            redirect('/books/edit?id=' . $_POST['id']);
        }
    }

    public function delete() {
        try {
            Books::delete($_GET['id']);
            $_SESSION['success_message'] = 'Buku berhasil dihapus!';
            redirect('/addbook');
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
            redirect('/addbook');
        }
    }
}
