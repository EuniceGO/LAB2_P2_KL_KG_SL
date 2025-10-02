<?php
require_once 'modelos/ImagenModel.php';
require_once 'modelos/ProductoModel.php';
require_once 'clases/Imagen.php';

class ImagenController {
    private $imagenModel;

    public function __construct() {
        $this->imagenModel = new ImagenModel();
    }

    public function index() {
        $buscar = isset($_GET['buscar']) ? $_GET['buscar'] : null;
        if ($buscar) {
            $imagenes = $this->imagenModel->search($buscar);
        } else {
            $imagenes = $this->imagenModel->getAll();
        }
        include 'vistas/Imagenes/index.php';
    }

    public function create() {
        $productoModel = new ProductoModel();
        $productos = $productoModel->getAll();
        include 'vistas/Imagenes/create.php';
    }

  public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
               
            $imagen = new Imagen(null, $_POST['id_producto'],$_POST['url_imagen'], $_POST['descripcion']);
            $this->imagenModel->insert($imagen);
            $this->index();
        }
    }

    public function edit($id) {
        $imagen = $this->imagenModel->getById($id);
        $productoModel = new ProductoModel();
        $productos = $productoModel->getAll();
        include 'vistas/Imagenes/edit.php';
    }

    

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $fileName = $_POST['imagen_actual'] ?? null;
            
            if (isset($_FILES['url_imagen']) && $_FILES['url_imagen']['error'] == 0) {
                $fileName = basename($_FILES['url_imagen']['name']);
                move_uploaded_file($_FILES['url_imagen']['tmp_name'], "uploads/" . $fileName);
            }

            $imagen = new Imagen(
                $id,
                $_POST['id_producto'],
                $fileName,
                $_POST['descripcion']
            );
            $this->imagenModel->update($imagen);
            $this->index();
        }
    }

    public function delete($id) {
        $imagen = $this->imagenModel->getById($id);
        if ($imagen) {
            $this->imagenModel->delete($imagen);
            // Opcional: borrar archivo físico
            $filePath = "uploads/" . $imagen->getUrlImagen();
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        $this->index();
    }
}
?>
