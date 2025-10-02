<?php
require_once 'modelos/UsuarioModel.php';
require_once 'modelos/RoleModel.php';
require_once 'clases/Usuario.php';
require_once 'modelos/ProductoModel.php';
// Removed require_once 'modelos/Model.php'; because modelos/Model.php does not exist
require_once __DIR__ . '/../libs/fpdf186/fpdf.php';
// Removed require_once('tcpdf_include.php'); because the file does not exist and is not used in the current code

class UsuarioController {
    private $usuarioModel;
    private $roleModel;

    public function __construct() {
        $this->usuarioModel = new UsuarioModel();
        $this->roleModel = new RoleModel();
    }

    // Acción para mostrar reportes y gráficos
    public function verReportes() {
        $this->startSession();
        $this->checkAuthentication();

        // Cargar modelos necesarios
        require_once 'modelos/ProductoModel.php';
        require_once 'modelos/CategoriaModel.php';
        require_once 'modelos/FacturaModel.php';

        $productoModel = new ProductoModel();
        $categoriaModel = new CategoriaModel();
        $facturaModel = new FacturaModel();

        // Obtener datos para reportes
        $totalUsuarios = $this->usuarioModel->getTotalUsuarios();
        $usuariosPorRol = $this->usuarioModel->getUsuariosPorRol();

        $totalProductos = $productoModel->getTotalProductos();
        $productosPorCategoria = $productoModel->getProductosPorCategoria();

        $totalCategorias = $categoriaModel->getTotalCategorias();

        // Obtener resumen de facturas por cliente
        $facturasClientes = $facturaModel->obtenerFacturasConCliente(100, 0); // Limitar a 100 para reporte

        // Calcular totales por cliente
        $resumenClientes = [];
        foreach ($facturasClientes as $factura) {
            $idCliente = $factura['id_cliente'] ?? 0;
            if (!isset($resumenClientes[$idCliente])) {
                $resumenClientes[$idCliente] = [
                    'cliente_nombre' => $factura['cliente_nombre_completo'] ?? 'Cliente General',
                    'total_gastos' => 0,
                    'total_facturas' => 0
                ];
            }
            $resumenClientes[$idCliente]['total_gastos'] += $factura['total'];
            $resumenClientes[$idCliente]['total_facturas'] += 1;
        }

        include 'vistas/Usuarios/reportes.php';
    }

    // Generar reporte PDF de usuarios
    public function generarReporteUsuarios() {
        $this->startSession();
        $this->checkAuthentication();

        require_once 'libs/fpdf186/fpdf.php';

        $usuarios = $this->usuarioModel->getAll();
        $usuariosPorRol = $this->usuarioModel->getUsuariosPorRol();

        // Generate chart image for users by role
        $labels = [];
        $data = [];
        foreach ($usuariosPorRol as $rol) {
            $labels[] = $rol['rol'];
            $data[] = $rol['total_usuarios'];
        }

        $chartConfig = json_encode([
            'type' => 'pie',
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'data' => $data,
                    'backgroundColor' => ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'],
                    'borderWidth' => 1,
                    'borderColor' => '#fff'
                ]]
            ],
            'options' => [
                'plugins' => [
                    'legend' => ['display' => false],
                    'datalabels' => [
                        'display' => true,
                        'color' => 'white',
                        'font' => ['size' => 12, 'weight' => 'bold'],
                        'formatter' => 'function(value, context) { var total = context.dataset.data.reduce((a,b)=>a+b); return Math.round(value / total * 100) + "%"; }'
                    ]
                ]
            ]
        ]);

        $chartUrl = 'https://quickchart.io/chart?c=' . urlencode($chartConfig);
        $chartImage = file_get_contents($chartUrl);
        $imagePath = sys_get_temp_dir() . '/chart_usuarios.png';
        file_put_contents($imagePath, $chartImage);

        $pdf = new FPDF();
        $pdf->AddPage();

        // Header
        $pdf->SetFillColor(102, 126, 234);
        $pdf->Rect(0, 0, 210, 30, 'F');
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->Cell(0, 15, 'Sistema de Gestion', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 5, 'Reporte de Usuarios - ' . date('d/m/Y'), 0, 1, 'C');
        $pdf->Ln(10);

        // Chart
        if (file_exists($imagePath)) {
            $pdf->Image($imagePath, 60, 40, 90, 60);
            $pdf->Ln(70);
        }

        // Table
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(240, 240, 240);
        $pdf->Cell(15, 10, 'ID', 1, 0, 'C', true);
        $pdf->Cell(50, 10, 'Nombre', 1, 0, 'C', true);
        $pdf->Cell(70, 10, 'Email', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Rol', 1, 1, 'C', true);

        $pdf->SetFont('Arial', '', 10);
        $fill = false;
        foreach ($usuarios as $usuario) {
            $pdf->SetFillColor(255, 255, 255);
            if ($fill) $pdf->SetFillColor(248, 248, 248);
            $pdf->Cell(15, 8, $usuario->getIdUsuario(), 1, 0, 'C', $fill);
            $pdf->Cell(50, 8, $usuario->getNombre(), 1, 0, 'L', $fill);
            $pdf->Cell(70, 8, $usuario->getEmail(), 1, 0, 'L', $fill);
            $pdf->Cell(30, 8, $usuario->getIdRol(), 1, 1, 'C', $fill);
            $fill = !$fill;
        }

        // Footer
        $pdf->SetY(-15);
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 10, 'Generado el ' . date('d/m/Y H:i'), 0, 0, 'C');

        if (file_exists($imagePath)) unlink($imagePath);

        $pdf->Output('D', 'reporte_usuarios.pdf');
    }

    // Generar reporte Excel de usuarios
    public function generarReporteUsuariosExcel() {
        $this->startSession();
        $this->checkAuthentication();

        $usuarios = $this->usuarioModel->getAll();

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename=reporte_usuarios.xls');
        header('Cache-Control: max-age=0');

        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        echo '<head>';
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        echo '<style>';
        echo 'table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; }';
        echo 'th, td { border: 1px solid #000; padding: 8px; text-align: left; }';
        echo 'th { background-color: #4CAF50; color: white; font-weight: bold; }';
        echo 'tr:nth-child(even) { background-color: #f2f2f2; }';
        echo 'tr:nth-child(odd) { background-color: #ffffff; }';
        echo 'h2 { color: #333; text-align: center; }';
        echo '</style>';
        echo '</head>';
        echo '<body>';
        echo '<h2>Reporte de Usuarios</h2>';
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th>ID</th>';
        echo '<th>Nombre</th>';
        echo '<th>Email</th>';
        echo '<th>Rol</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        foreach ($usuarios as $usuario) {
            echo '<tr>';
            echo '<td>' . $usuario->getIdUsuario() . '</td>';
            echo '<td>' . htmlspecialchars($usuario->getNombre()) . '</td>';
            echo '<td>' . htmlspecialchars($usuario->getEmail()) . '</td>';
            echo '<td>' . $usuario->getIdRol() . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        echo '</body>';
        echo '</html>';
        exit;
    }

    // Generar reporte PDF de productos
    public function generarReporteProductos() {
        $this->startSession();
        $this->checkAuthentication();

        require_once 'libs/fpdf186/fpdf.php';
        require_once 'modelos/ProductoModel.php';

        $productoModel = new ProductoModel();
        $productos = $productoModel->getAll();
        $productosPorCategoria = $productoModel->getProductosPorCategoria();

        // Generate chart image for products by category
        $labels = [];
        $data = [];
        foreach ($productosPorCategoria as $cat) {
            $labels[] = $cat['categoria'];
            $data[] = $cat['total_productos'];
        }

        $chartConfig = json_encode([
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => 'Productos',
                    'data' => $data,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.8)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1
                ]]
            ],
            'options' => [
                'scales' => [
                    'y' => ['beginAtZero' => true]
                ],
                'plugins' => [
                    'legend' => ['display' => false],
                    'datalabels' => [
                        'display' => true,
                        'color' => 'black',
                        'font' => ['size' => 12, 'weight' => 'bold'],
                        'anchor' => 'end',
                        'align' => 'top'
                    ]
                ]
            ]
        ]);

        $chartUrl = 'https://quickchart.io/chart?c=' . urlencode($chartConfig);
        $chartImage = file_get_contents($chartUrl);
        $imagePath = sys_get_temp_dir() . '/chart_productos.png';
        file_put_contents($imagePath, $chartImage);

        $pdf = new FPDF();
        $pdf->AddPage();

        // Header
        $pdf->SetFillColor(102, 126, 234);
        $pdf->Rect(0, 0, 210, 30, 'F');
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->Cell(0, 15, 'Sistema de Gestion', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 5, 'Reporte de Productos - ' . date('d/m/Y'), 0, 1, 'C');
        $pdf->Ln(10);

        // Chart
        if (file_exists($imagePath)) {
            $pdf->Image($imagePath, 30, 40, 150, 60);
            $pdf->Ln(70);
        }

        // Table
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(240, 240, 240);
        $pdf->Cell(20, 10, 'ID', 1, 0, 'C', true);
        $pdf->Cell(60, 10, 'Nombre', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Precio', 1, 0, 'C', true);
        $pdf->Cell(50, 10, 'Categoria', 1, 1, 'C', true);

        $pdf->SetFont('Arial', '', 10);
        $fill = false;
        foreach ($productos as $producto) {
            $pdf->SetFillColor(255, 255, 255);
            if ($fill) $pdf->SetFillColor(248, 248, 248);
            $pdf->Cell(20, 8, $producto->getIdProducto(), 1, 0, 'C', $fill);
            $pdf->Cell(60, 8, $producto->getNombre(), 1, 0, 'L', $fill);
            $pdf->Cell(30, 8, '$' . number_format($producto->getPrecio(), 2), 1, 0, 'R', $fill);
            $pdf->Cell(50, 8, $producto->getIdCategoria(), 1, 1, 'C', $fill);
            $fill = !$fill;
        }

        // Footer
        $pdf->SetY(-15);
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 10, 'Generado el ' . date('d/m/Y H:i'), 0, 0, 'C');

        if (file_exists($imagePath)) unlink($imagePath);

        $pdf->Output('D', 'reporte_productos.pdf');
    }

    // Generar reporte Excel de productos
    public function generarReporteProductosExcel() {
        $this->startSession();
        $this->checkAuthentication();

        require_once 'modelos/ProductoModel.php';

        $productoModel = new ProductoModel();
        $productos = $productoModel->getAll();

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename=reporte_productos.xls');
        header('Cache-Control: max-age=0');

        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        echo '<head>';
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        echo '<style>';
        echo 'table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; }';
        echo 'th, td { border: 1px solid #000; padding: 8px; text-align: left; }';
        echo 'th { background-color: #4CAF50; color: white; font-weight: bold; }';
        echo 'tr:nth-child(even) { background-color: #f2f2f2; }';
        echo 'tr:nth-child(odd) { background-color: #ffffff; }';
        echo 'h2 { color: #333; text-align: center; }';
        echo '</style>';
        echo '</head>';
        echo '<body>';
        echo '<h2>Reporte de Productos</h2>';
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th>ID</th>';
        echo '<th>Nombre</th>';
        echo '<th>Precio</th>';
        echo '<th>Categoria</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        foreach ($productos as $producto) {
            echo '<tr>';
            echo '<td>' . $producto->getIdProducto() . '</td>';
            echo '<td>' . htmlspecialchars($producto->getNombre()) . '</td>';
            echo '<td>$' . number_format($producto->getPrecio(), 2) . '</td>';
            echo '<td>' . $producto->getIdCategoria() . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        echo '</body>';
        echo '</html>';
        exit;
    }

    // Generar reporte PDF de categorías
    public function generarReporteCategorias() {
        $this->startSession();
        $this->checkAuthentication();

        require_once 'libs/fpdf186/fpdf.php';
        require_once 'modelos/CategoriaModel.php';
        require_once 'modelos/ProductoModel.php';

        $categoriaModel = new CategoriaModel();
        $productoModel = new ProductoModel();
        $categorias = $categoriaModel->getAll();

        // Generate chart image for categories (products per category)
        $labels = [];
        $data = [];
        $productosPorCategoria = $productoModel->getProductosPorCategoria();
        foreach ($productosPorCategoria as $cat) {
            $labels[] = $cat['categoria'];
            $data[] = $cat['total_productos'];
        }

        $chartConfig = json_encode([
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => 'Productos por Categoria',
                    'data' => $data,
                    'backgroundColor' => 'rgba(75, 192, 192, 0.8)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 1
                ]]
            ],
            'options' => [
                'scales' => [
                    'y' => ['beginAtZero' => true]
                ],
                'plugins' => [
                    'legend' => ['display' => false],
                    'datalabels' => [
                        'display' => true,
                        'color' => 'black',
                        'font' => ['size' => 12, 'weight' => 'bold'],
                        'anchor' => 'end',
                        'align' => 'top'
                    ]
                ]
            ]
        ]);

        $chartUrl = 'https://quickchart.io/chart?c=' . urlencode($chartConfig);
        $chartImage = file_get_contents($chartUrl);
        $imagePath = sys_get_temp_dir() . '/chart_categorias.png';
        file_put_contents($imagePath, $chartImage);

        $pdf = new FPDF();
        $pdf->AddPage();

        // Header
        $pdf->SetFillColor(102, 126, 234);
        $pdf->Rect(0, 0, 210, 30, 'F');
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->Cell(0, 15, 'Sistema de Gestion', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 5, 'Reporte de Categorias - ' . date('d/m/Y'), 0, 1, 'C');
        $pdf->Ln(10);

        // Chart
        if (file_exists($imagePath)) {
            $pdf->Image($imagePath, 60, 40, 90, 60);
            $pdf->Ln(70);
        }

        // Table
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(240, 240, 240);
        $pdf->Cell(20, 10, 'ID', 1, 0, 'C', true);
        $pdf->Cell(80, 10, 'Nombre', 1, 0, 'C', true);
        $pdf->Cell(80, 10, 'Descripcion', 1, 1, 'C', true);

        $pdf->SetFont('Arial', '', 10);
        $fill = false;
        foreach ($categorias as $categoria) {
            $pdf->SetFillColor(255, 255, 255);
            if ($fill) $pdf->SetFillColor(248, 248, 248);
            $pdf->Cell(20, 8, $categoria->getIdCategoria(), 1, 0, 'C', $fill);
            $pdf->Cell(80, 8, $categoria->getNombre(), 1, 0, 'L', $fill);
            $pdf->Cell(80, 8, $categoria->getDescripcion(), 1, 1, 'L', $fill);
            $fill = !$fill;
        }

        // Footer
        $pdf->SetY(-15);
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 10, 'Generado el ' . date('d/m/Y H:i'), 0, 0, 'C');

        if (file_exists($imagePath)) unlink($imagePath);

        $pdf->Output('D', 'reporte_categorias.pdf');
    }

    // Generar reporte Excel de categorías
    public function generarReporteCategoriasExcel() {
        $this->startSession();
        $this->checkAuthentication();

        require_once 'modelos/CategoriaModel.php';

        $categoriaModel = new CategoriaModel();
        $categorias = $categoriaModel->getAll();

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename=reporte_categorias.xls');
        header('Cache-Control: max-age=0');

        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        echo '<head>';
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        echo '<style>';
        echo 'table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; }';
        echo 'th, td { border: 1px solid #000; padding: 8px; text-align: left; }';
        echo 'th { background-color: #4CAF50; color: white; font-weight: bold; }';
        echo 'tr:nth-child(even) { background-color: #f2f2f2; }';
        echo 'tr:nth-child(odd) { background-color: #ffffff; }';
        echo 'h2 { color: #333; text-align: center; }';
        echo '</style>';
        echo '</head>';
        echo '<body>';
        echo '<h2>Reporte de Categorías</h2>';
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th>ID</th>';
        echo '<th>Nombre</th>';
        echo '<th>Descripción</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        foreach ($categorias as $categoria) {
            echo '<tr>';
            echo '<td>' . $categoria->getIdCategoria() . '</td>';
            echo '<td>' . htmlspecialchars($categoria->getNombre()) . '</td>';
            echo '<td>' . htmlspecialchars($categoria->getDescripcion()) . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        echo '</body>';
        echo '</html>';
        exit;
    }

    // Iniciar sesión si no existe
    private function startSession() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Mostrar todos los usuarios
    public function index() {
        $this->startSession();
        $this->checkAuthentication();
        
        $buscar = isset($_GET['buscar']) ? $_GET['buscar'] : null;
        if ($buscar) {
            $usuarios = $this->usuarioModel->search($buscar);
        } else {
            $usuarios = $this->usuarioModel->getAll();
        }
        
        // Obtener roles para mostrar nombres en lugar de IDs
        $roles = $this->roleModel->getAll();
        $rolesArray = [];
        foreach ($roles as $rol) {
            $rolesArray[$rol->getIdRol()] = $rol->getNombre();
        }
        
        include 'vistas/Usuarios/index.php';
    }

    // Mostrar formulario para crear nuevo usuario
    public function create() {
        $this->startSession();
        $this->checkAuthentication();
        
        $roles = $this->roleModel->getAll();
        include 'vistas/Usuarios/create.php';
    }

    // Guardar nuevo usuario
    public function store() {
        $this->startSession();
        $this->checkAuthentication();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Verificar si el email ya existe
            if ($this->usuarioModel->emailExists($_POST['email'])) {
                $error = "Ya existe un usuario con ese email";
                $roles = $this->roleModel->getAll();
                include 'vistas/Usuarios/create.php';
                return;
            }
            
            $usuario = new Usuario(
                null,
                $_POST['nombre'],
                $_POST['email'],
                $_POST['password'],
                $_POST['id_rol']
            );
            
            if ($this->usuarioModel->insert($usuario)) {
                header('Location: ?controller=usuario&action=index&success=created');
                exit;
            } else {
                $error = "Error al crear el usuario";
                $roles = $this->roleModel->getAll();
                include 'vistas/Usuarios/create.php';
            }
        }
    }

    // Mostrar formulario para editar usuario
    public function edit() {
        $this->startSession();
        $this->checkAuthentication();
        
        $id = $_GET['id'] ?? null;
        if ($id) {
            $usuario = $this->usuarioModel->getById($id);
            if ($usuario) {
                $roles = $this->roleModel->getAll();
                include 'vistas/Usuarios/edit.php';
            } else {
                header('Location: ?controller=usuario&action=index&error=notfound');
                exit;
            }
        } else {
            header('Location: ?controller=usuario&action=index');
            exit;
        }
    }

    // Actualizar usuario
    public function update() {
        $this->startSession();
        $this->checkAuthentication();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id_usuario'];
            
            // Verificar si el email ya existe (excluyendo el usuario actual)
            if ($this->usuarioModel->emailExists($_POST['email'], $id)) {
                $error = "Ya existe un usuario con ese email";
                $usuario = $this->usuarioModel->getById($id);
                $roles = $this->roleModel->getAll();
                include 'vistas/Usuarios/edit.php';
                return;
            }
            
            $usuario = new Usuario(
                $id,
                $_POST['nombre'],
                $_POST['email'],
                null, // No actualizamos la contraseña aquí
                $_POST['id_rol']
            );
            
            if ($this->usuarioModel->update($usuario)) {
                header('Location: ?controller=usuario&action=index&success=updated');
                exit;
            } else {
                $error = "Error al actualizar el usuario";
                $roles = $this->roleModel->getAll();
                include 'vistas/Usuarios/edit.php';
            }
        }
    }

    // Eliminar usuario
    public function delete() {
        $this->startSession();
        $this->checkAuthentication();
        
        $id = $_GET['id'] ?? null;
        if ($id) {
            // No permitir que el usuario se elimine a sí mismo
            if ($_SESSION['user_id'] == $id) {
                header('Location: ?controller=usuario&action=index&error=cannotdeleteyourself');
                exit;
            }
            
            if ($this->usuarioModel->delete($id)) {
                header('Location: ?controller=usuario&action=index&success=deleted');
                exit;
            } else {
                header('Location: ?controller=usuario&action=index&error=deleteerror');
                exit;
            }
        } else {
            header('Location: ?controller=usuario&action=index');
            exit;
        }
    }

    // Mostrar formulario de login
    public function login() {
        $this->startSession();
        // Si ya está logueado, redirigir al dashboard
        if (isset($_SESSION['user_id'])) {
            header('Location: ?controller=usuario&action=dashboard');
            exit;
        }
        include 'vistas/Usuarios/login.php';
    }
    
    // Mostrar formulario de login móvil (para usuarios que escanean QR)
    public function loginMobile() {
        $this->startSession();
        
        // Si ya está logueado, redirigir a donde corresponda
        if (isset($_SESSION['user_id'])) {
            if ($_SESSION['user_role_id'] == 1) {
                header('Location: ?controller=usuario&action=dashboard');
            } else {
                // Redirigir a donde venía o al carrito móvil
                $redirect = $_SESSION['redirect_after_login'] ?? '?c=carrito&a=mobile';
                unset($_SESSION['redirect_after_login']);
                header('Location: ' . $redirect);
            }
            exit;
        }
        
        // Obtener información del producto si viene desde QR
        $producto_id = $_GET['producto_id'] ?? null;
        $producto = null;
        if ($producto_id) {
            require_once 'modelos/ProductoModel.php';
            $productoModel = new ProductoModel();
            $producto = $productoModel->getById($producto_id);
        }
        
        include 'vistas/Usuarios/login_mobile.php';
    }
    
    // Mostrar formulario de registro para clientes
    public function registro() {
        $this->startSession();
        // Si ya está logueado, redirigir al dashboard
        if (isset($_SESSION['user_id'])) {
            header('Location: ?controller=usuario&action=dashboard');
            exit;
        }
        include 'vistas/Usuarios/registro.php';
    }
    
    // Procesar registro de cliente
    public function procesarRegistro() {
        $this->startSession();
        $errores = [];
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validar datos
            $nombre = trim($_POST['nombre'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $telefono = trim($_POST['telefono'] ?? '');
            $direccion = trim($_POST['direccion'] ?? '');
            
            // Validaciones
            if (empty($nombre)) {
                $errores[] = "El nombre es requerido";
            }
            
            if (empty($email)) {
                $errores[] = "El email es requerido";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errores[] = "El email no es válido";
            }
            
            if (empty($password)) {
                $errores[] = "La contraseña es requerida";
            } elseif (strlen($password) < 6) {
                $errores[] = "La contraseña debe tener al menos 6 caracteres";
            }
            
            if ($password !== $confirm_password) {
                $errores[] = "Las contraseñas no coinciden";
            }
            
            // Verificar si el email ya existe
            if (empty($errores)) {
                $usuarioExistente = $this->usuarioModel->getByEmail($email);
                if ($usuarioExistente) {
                    $errores[] = "Ya existe una cuenta con este email";
                }
            }
            
            if (empty($errores)) {
                try {
                    // Crear usuario con rol de cliente (ID 2)
                    $usuario = new Usuario(null, $nombre, $email, $password, 2);
                    $resultado = $this->usuarioModel->insert($usuario);
                    
                    if ($resultado) {
                        // También crear registro en tabla clientes si existe
                        try {
                            require_once 'modelos/ClienteModel.php';
                            $clienteModel = new ClienteModel();
                            
                            // Obtener el ID del usuario recién creado
                            $usuarioCreado = $this->usuarioModel->getByEmail($email);
                            if ($usuarioCreado) {
                                $datosCliente = [
                                    'nombre' => $nombre,
                                    'email' => $email,
                                    'telefono' => $telefono,
                                    'direccion' => $direccion,
                                    'id_usuario' => $usuarioCreado->getIdUsuario()
                                ];
                                $clienteModel->crear($datosCliente);
                            }
                        } catch (Exception $e) {
                            // Si falla la creación del cliente, continúa (el usuario ya está creado)
                            error_log("Error al crear cliente: " . $e->getMessage());
                        }
                        
                        // Login automático después del registro exitoso
                        $result = $this->usuarioModel->authenticate($email, $password);
                        if ($result) {
                            $_SESSION['user_id'] = $result['usuario']->getIdUsuario();
                            $_SESSION['user_name'] = $result['usuario']->getNombre();
                            $_SESSION['user_email'] = $result['usuario']->getEmail();
                            $_SESSION['user_role_id'] = $result['usuario']->getIdRol();
                            $_SESSION['user_role'] = 'Cliente';
                            
                            // Redirigir al dashboard simple de cliente
                            header('Location: ?controller=usuario&action=dashboardCliente&success=registro');
                            exit;
                        }
                    } else {
                        throw new Exception("Error al crear la cuenta de usuario");
                    }
                    
                } catch (Exception $e) {
                    $errores[] = "Error en el registro: " . $e->getMessage();
                }
            }
        }
        
        if (!empty($errores)) {
            include 'vistas/Usuarios/registro.php';
        }
    }

    public function authenticate() {
        $this->startSession();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            // Verificar el usuario y la contraseña en la base de datos
            $result = $this->usuarioModel->authenticate($email, $password);

            if ($result) {
                // Si la autenticación es exitosa, guardamos los datos del usuario en la sesión
                $_SESSION['user_id'] = $result['usuario']->getIdUsuario();
                $_SESSION['user_name'] = $result['usuario']->getNombre();
                $_SESSION['user_email'] = $result['usuario']->getEmail();
                $_SESSION['user_role_id'] = $result['usuario']->getIdRol();

                // Redirigir según el rol del usuario
                if ($_SESSION['user_role_id'] == 1) {
                    // Si el rol es Administrador - ir al dashboard admin
                    $_SESSION['user_role'] = 'Administrador';
                    header('Location: ?controller=usuario&action=dashboard');  
                    exit();
                } elseif ($_SESSION['user_role_id'] == 2) {
                    // Si el rol es Cliente - ir al panel simple de cliente
                    $_SESSION['user_role'] = 'Cliente';
                    
                    // Obtener ID del cliente desde la tabla clientes
                    try {
                        require_once 'modelos/ClienteModel.php';
                        $clienteModel = new ClienteModel();
                        $cliente = $clienteModel->obtenerPorUsuario($_SESSION['user_id']);
                        if ($cliente) {
                            $_SESSION['cliente_id'] = $cliente['id_cliente'];
                        }
                    } catch (Exception $e) {
                        // Si no existe en la tabla clientes, continuar sin cliente_id
                        error_log("Cliente no encontrado en tabla clientes: " . $e->getMessage());
                    }
                    
                    // Verificar si hay redirección pendiente (desde móvil)
                    if (isset($_SESSION['redirect_after_login'])) {
                        $redirect = $_SESSION['redirect_after_login'];
                        unset($_SESSION['redirect_after_login']);
                        header('Location: ' . $redirect);
                        exit();
                    }
                    
                    // Redirigir al dashboard simple de cliente
                    header('Location: ?controller=usuario&action=dashboardCliente&success=login');
                    exit();
                } else {
                    // Si el rol no es válido, redirigir al login con error
                    $error = "Rol de usuario no válido";
                    include 'vistas/Usuarios/login.php';
                }
            } else {
                // Si las credenciales son incorrectas
                $error = "Email o contraseña incorrectos";
                
                // Verificar si es login móvil para mostrar la vista correcta
                if (isset($_POST['from_mobile']) && $_POST['from_mobile'] == 1) {
                    // Obtener información del producto si viene desde QR
                    $producto_id = $_POST['producto_id'] ?? null;
                    $producto = null;
                    if ($producto_id) {
                        require_once 'modelos/ProductoModel.php';
                        $productoModel = new ProductoModel();
                        $producto = $productoModel->getById($producto_id);
                    }
                    include 'vistas/Usuarios/login_mobile.php';
                } else {
                    include 'vistas/Usuarios/login.php';
                }
            }
        }
    }


    // Dashboard del usuario administrador
    public function dashboard() {
        $this->startSession();
        $this->checkAuthentication();
        
        // Solo permitir acceso a administradores
        if ($_SESSION['user_role_id'] != 1) {
            header('Location: ?controller=usuario&action=dashboardCliente');
            exit;
        }
        
        // Obtener estadísticas de usuarios
        $totalUsuarios = $this->usuarioModel->getTotalUsuarios();
        $usuariosPorRol = $this->usuarioModel->getUsuariosPorRol();
        
        // Obtener estadísticas de facturas/ventas
        require_once 'modelos/FacturaModel.php';
        require_once 'modelos/ProductoModel.php';
        require_once 'modelos/CategoriaModel.php';
        
        $facturaModel = new FacturaModel();
        $productoModel = new ProductoModel();
        $categoriaModel = new CategoriaModel();
        
        $estadisticasFacturas = $facturaModel->obtenerEstadisticasVentas();
        $totalProductos = $productoModel->getTotalProductos();
        $totalCategorias = $categoriaModel->getTotalCategorias();
        
        include 'vistas/Usuarios/dashboard.php';
    }

    // Dashboard simple para clientes - solo sus facturas
    public function dashboardCliente() {
        $this->startSession();
        $this->checkAuthentication();
        
        // Solo permitir acceso a clientes
        if ($_SESSION['user_role_id'] != 2) {
            header('Location: ?controller=usuario&action=dashboard');
            exit;
        }
        
        // Inicializar variables por defecto
        $misFacturas = [];
        $totalGastado = 0;
        $totalFacturas = 0;
        
        // Obtener facturas del cliente logueado
        try {
            require_once 'modelos/FacturaModel.php';
            require_once 'modelos/ClienteModel.php';
            
            $facturaModel = new FacturaModel();
            $clienteModel = new ClienteModel();
            
            // Obtener el cliente vinculado al usuario logueado
            $cliente = $clienteModel->obtenerPorUsuario($_SESSION['user_id']);
            
            if ($cliente) {
                // Usar el ID del cliente para obtener sus facturas
                $cliente_id = $cliente['id_cliente'] ?? $cliente['id'];
                $misFacturas = $facturaModel->obtenerPorCliente($cliente_id);
                
                // Calcular totales
                $totalFacturas = count($misFacturas);
                foreach ($misFacturas as $factura) {
                    $totalGastado += $factura['total'];
                }
                
            } else {
                // Si no hay cliente vinculado, buscar facturas por datos del usuario
                // Esto es un fallback por si no está vinculado correctamente
                try {
                    $conexion = new mysqli("localhost", "root", "", "productos_iniciales");
                    if (!$conexion->connect_error) {
                        $stmt = $conexion->prepare("
                            SELECT f.* FROM facturas f 
                            WHERE f.cliente_email = ? OR f.cliente_nombre = ?
                            ORDER BY f.fecha_factura DESC
                        ");
                        $stmt->bind_param("ss", $_SESSION['user_email'], $_SESSION['user_name']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        while ($row = $result->fetch_assoc()) {
                            $misFacturas[] = $row;
                            $totalGastado += $row['total'];
                        }
                        $totalFacturas = count($misFacturas);
                        $conexion->close();
                    }
                } catch (Exception $fallbackError) {
                    error_log("Error en fallback de facturas: " . $fallbackError->getMessage());
                }
            }
            
        } catch (Exception $e) {
            // Si hay error, continuar con valores por defecto
            error_log("Error al cargar facturas: " . $e->getMessage());
        }
        
        include 'vistas/Usuarios/dashboard_cliente.php';
    }

    // Cerrar sesión
    public function logout() {
        $this->startSession();
        session_destroy();
        header('Location: ?controller=usuario&action=login');
        exit;
    }

    // Verificar autenticación
    private function checkAuthentication() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?controller=usuario&action=login');
            exit;
        }
    }

    // Cambiar contraseña
    public function changePassword() {
        $this->startSession();
        $this->checkAuthentication();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            // Verificar que las nuevas contraseñas coincidan
            if ($newPassword !== $confirmPassword) {
                $error = "Las nuevas contraseñas no coinciden";
                include 'vistas/Usuarios/change_password.php';
                return;
            }
            
            // Verificar contraseña actual
            $usuario = $this->usuarioModel->getById($_SESSION['user_id']);
            if ($currentPassword !== $usuario->getPassword()) {
                $error = "La contraseña actual es incorrecta";
                include 'vistas/Usuarios/change_password.php';
                return;
            }
            
            // Actualizar contraseña
            if ($this->usuarioModel->updatePassword($_SESSION['user_id'], $newPassword)) {
                $success = "Contraseña actualizada correctamente";
                include 'vistas/Usuarios/change_password.php';
            } else {
                $error = "Error al actualizar la contraseña";
                include 'vistas/Usuarios/change_password.php';
            }
        } else {
            include 'vistas/Usuarios/change_password.php';
        }
    }

    // Perfil del usuario
    public function profile() {
        $this->startSession();
        $this->checkAuthentication();
        
        $usuario = $this->usuarioModel->getById($_SESSION['user_id']);
        $rol = $this->roleModel->getById($usuario->getIdRol());
        
        include 'vistas/Usuarios/profile.php';
    }


    

    // Generar reporte PDF de facturas por clientes (resumen general)
    public function generarReporteFacturasClientes() {
        $this->startSession();
        $this->checkAuthentication();

        require_once 'libs/fpdf186/fpdf.php';
        require_once 'modelos/FacturaModel.php';

        $facturaModel = new FacturaModel();
        $facturasClientes = $facturaModel->obtenerFacturasConCliente(1000, 0); // Obtener hasta 1000 facturas para el reporte

        // Calcular resumen por cliente
        $resumenClientes = [];
        foreach ($facturasClientes as $factura) {
            $idCliente = $factura['id_cliente'] ?? 0;
            if (!isset($resumenClientes[$idCliente])) {
                $resumenClientes[$idCliente] = [
                    'cliente_nombre' => $factura['cliente_nombre_completo'] ?? 'Cliente General',
                    'total_gastos' => 0,
                    'total_facturas' => 0
                ];
            }
            $resumenClientes[$idCliente]['total_gastos'] += $factura['total'];
            $resumenClientes[$idCliente]['total_facturas'] += 1;
        }

        $pdf = new FPDF();
        $pdf->AddPage();

        // Header
        $pdf->SetFillColor(102, 126, 234);
        $pdf->Rect(0, 0, 210, 30, 'F');
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->Cell(0, 15, 'Sistema de Gestion', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 5, 'Reporte de Facturas por Clientes - ' . date('d/m/Y'), 0, 1, 'C');
        $pdf->Ln(10);

        // Table header
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(240, 240, 240);
        $pdf->Cell(80, 10, 'Cliente', 1, 0, 'C', true);
        $pdf->Cell(50, 10, 'Total Facturas', 1, 0, 'C', true);
        $pdf->Cell(50, 10, 'Total Gastos', 1, 1, 'C', true);

        // Table rows
        $pdf->SetFont('Arial', '', 10);
        $fill = false;
        foreach ($resumenClientes as $cliente) {
            $pdf->SetFillColor(255, 255, 255);
            if ($fill) $pdf->SetFillColor(248, 248, 248);
            $pdf->Cell(80, 8, $cliente['cliente_nombre'], 1, 0, 'L', $fill);
            $pdf->Cell(50, 8, $cliente['total_facturas'], 1, 0, 'C', $fill);
            $pdf->Cell(50, 8, '$' . number_format($cliente['total_gastos'], 2), 1, 1, 'R', $fill);
            $fill = !$fill;
        }

        // Footer
        $pdf->SetY(-15);
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 10, 'Generado el ' . date('d/m/Y H:i'), 0, 0, 'C');

        $pdf->Output('D', 'reporte_facturas_clientes.pdf');
    }

    // Generar reporte Excel de facturas por clientes (resumen general)
    public function generarReporteFacturasClientesExcel() {
        $this->startSession();
        $this->checkAuthentication();

        require_once 'modelos/FacturaModel.php';

        $facturaModel = new FacturaModel();
        $facturasClientes = $facturaModel->obtenerFacturasConCliente(1000, 0); // Obtener hasta 1000 facturas para el reporte

        // Calcular resumen por cliente
        $resumenClientes = [];
        foreach ($facturasClientes as $factura) {
            $idCliente = $factura['id_cliente'] ?? 0;
            if (!isset($resumenClientes[$idCliente])) {
                $resumenClientes[$idCliente] = [
                    'cliente_nombre' => $factura['cliente_nombre_completo'] ?? 'Cliente General',
                    'total_gastos' => 0,
                    'total_facturas' => 0
                ];
            }
            $resumenClientes[$idCliente]['total_gastos'] += $factura['total'];
            $resumenClientes[$idCliente]['total_facturas'] += 1;
        }

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename=reporte_facturas_clientes.xls');
        header('Cache-Control: max-age=0');

        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        echo '<head>';
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        echo '<style>';
        echo 'table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; }';
        echo 'th, td { border: 1px solid #000; padding: 8px; text-align: left; }';
        echo 'th { background-color: #4CAF50; color: white; font-weight: bold; }';
        echo 'tr:nth-child(even) { background-color: #f2f2f2; }';
        echo 'tr:nth-child(odd) { background-color: #ffffff; }';
        echo 'h2 { color: #333; text-align: center; }';
        echo '</style>';
        echo '</head>';
        echo '<body>';
        echo '<h2>Reporte de Facturas por Clientes</h2>';
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Cliente</th>';
        echo '<th>Total Facturas</th>';
        echo '<th>Total Gastos</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        foreach ($resumenClientes as $cliente) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($cliente['cliente_nombre']) . '</td>';
            echo '<td>' . $cliente['total_facturas'] . '</td>';
            echo '<td>$' . number_format($cliente['total_gastos'], 2) . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        echo '</body>';
        echo '</html>';
        exit;
    }
}
?>
