<?php
require_once 'modelos/CategoriaModel.php';
require_once 'clases/Categoria.php';

class CategoriaController {
    private $categoriaModel;

    public function __construct() {
        $this->categoriaModel = new CategoriaModel();
    }

    public function index() {
        $categorias = $this->categoriaModel->getAll();
        $categoriaModel = new CategoriaModel();
        $categoriaTop = $categoriaModel->getCategoriaTop();
        $categorias = $categoriaModel->getAll();

        
        include 'vistas/Categorias/index.php';
    }

    public function create() {
        include 'vistas/Categorias/create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $categoria = new Categoria(null, $_POST['nombre'], $_POST['descripcion']);
            $this->categoriaModel->insert($categoria);
            $this->index();
        }
    }
    
    public function edit($id) {
        $categoria = $this->categoriaModel->getById($id);
        include 'vistas/Categorias/edit.php';
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $categoria = new Categoria(
                $id,
                $_POST['nombre'],
                $_POST['descripcion']
            );
            $this->categoriaModel->update($categoria);
            $this->index();
        }
    }

    public function delete($id) {
        $categoria = $this->categoriaModel->getById($id);
        if ($categoria) {
            $this->categoriaModel->delete($categoria);
        }
        $this->index();
    }
}
?>
