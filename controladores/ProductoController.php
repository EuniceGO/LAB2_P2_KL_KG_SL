<?php
require_once 'modelos/ProductoModel.php';
require_once 'modelos/CategoriaModel.php';
require_once 'clases/Producto.php';

class ProductoController {
    private $productoModel;

    public function __construct() {
        $this->productoModel = new ProductoModel();
    }

   public function index() {
    $buscar = isset($_GET['buscar']) ? $_GET['buscar'] : null;
    if ($buscar) {
        $productos = $this->productoModel->search($buscar);
    } else {
        $productos = $this->productoModel->getAll();
    }
    // Agregamos la estadística de categoría con más productos
    $categoriaModel = new CategoriaModel();
    $categoriaTop = $categoriaModel->getCategoriaConMasProductos();

    include 'vistas/Productos/index.php';
}




  

    public function create() {
        // Necesitamos categorías para el select
        $categoriaModel = new CategoriaModel();
        $categorias = $categoriaModel->getAll();
        include 'vistas/Productos/create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $producto = new Producto(
                null,
                $_POST['nombre'],
                $_POST['precio'],
                $_POST['id_categoria']
            );
            
            $resultado = $this->productoModel->insert($producto);
            if ($resultado) {
                // Redirigir con éxito
                header('Location: ?c=producto&a=index&success=1');
                exit;
            } else {
                // Error al crear
                $error = "Error al crear el producto";
                $categoriaModel = new CategoriaModel();
                $categorias = $categoriaModel->getAll();
                include 'vistas/Productos/create.php';
            }
        }
    }

    public function edit($id) {
        $producto = $this->productoModel->getById($id);
        $categoriaModel = new CategoriaModel();
        $categorias = $categoriaModel->getAll();
        include 'vistas/Productos/edit.php';
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Obtener el producto actual para conservar el QR
            $productoActual = $this->productoModel->getById($id);
            
            $producto = new Producto(
                $id,
                $_POST['nombre'],
                $_POST['precio'],
                $_POST['id_categoria'],
                $productoActual ? $productoActual->getCodigoQr() : null
            );
            $this->productoModel->update($producto);
            header('Location: ?c=producto&a=index&updated=1');
            exit;
        }
    }

    public function delete($id) {
        $producto = $this->productoModel->getById($id);
        if ($producto) {
            $this->productoModel->delete($producto);
        }
        header('Location: ?c=producto&a=index&deleted=1');
        exit;
    }

    // Método para regenerar código QR
    public function regenerateQR($id) {
        $qrPath = $this->productoModel->regenerateQR($id);
        if ($qrPath) {
            header('Location: ?c=producto&a=index&qr_generated=1');
        } else {
            header('Location: ?c=producto&a=index&qr_error=1');
        }
        exit;
    }

    // Método para ver el código QR
    public function viewQR($id) {
        $producto = $this->productoModel->getById($id);
        include 'vistas/Productos/view_qr.php';
    }
    
    // Método para vista móvil (acceso desde código QR)
    public function viewMobile($id) {
        $producto = $this->productoModel->getById($id);
        if ($producto) {
            // Obtener información adicional
            $categoriaModel = new CategoriaModel();
            $categoria = $categoriaModel->getById($producto->getIdCategoria());
            include 'vistas/Productos/mobile_view.php';
        } else {
            // Producto no encontrado
            include 'vistas/Productos/mobile_not_found.php';
        }
    }
}
?>
