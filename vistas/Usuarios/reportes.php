<?php include 'layout/menu.php'; ?>

<style>
    .enhanced-card {
        background: #667eea;
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
        position: relative;
    }
    .card-icon {
        font-size: 2.5rem;
        opacity: 0.8;
        margin-bottom: 10px;
    }
    .progress-bar-custom {
        background: #ff9a9e;
        border-radius: 10px;
    }
    .chart-container {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .btn-custom {
        background: #667eea;
        border: none;
        border-radius: 25px;
        padding: 10px 20px;
    }
</style>

<div class="container mt-4">
    <h1 class="mb-4 text-center fade-in-up" style="background: linear-gradient(45deg, #667eea, #764ba2); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: bold;">
        <i class="fas fa-chart-line"></i> Reportes y Gráficos Avanzados
    </h1>

    <!-- Progress Bars Section -->

    <!-- Enhanced Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-4">
            <div class="card enhanced-card text-white fade-in-up">
                <div class="card-body text-center">
                    <i class="fas fa-users card-icon"></i>
                    <h3 class="card-title"><?php echo $totalUsuarios; ?></h3>
                    <p class="card-text">Total Usuarios</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card enhanced-card text-white fade-in-up" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body text-center">
                    <i class="fas fa-box card-icon"></i>
                    <h3 class="card-title"><?php echo $totalProductos; ?></h3>
                    <p class="card-text">Total Productos</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card enhanced-card text-white fade-in-up" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="card-body text-center">
                    <i class="fas fa-tags card-icon"></i>
                    <h3 class="card-title"><?php echo $totalCategorias; ?></h3>
                    <p class="card-text">Total Categorías</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Charts Section -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card chart-container fade-in-up">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Distribución de Usuarios por Rol</h5>
                </div>
                <div class="card-body">
                    <canvas id="usuariosPorRolChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card chart-container fade-in-up">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Productos por Categoría</h5>
                </div>
                <div class="card-body">
                    <canvas id="productosPorCategoriaChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Charts Row -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card chart-container fade-in-up">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0"><i class="fas fa-chart-donut"></i> Distribución de Categorías</h5>
                </div>
                <div class="card-body">
                    <canvas id="categoriasDoughnutChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Botones para generar PDFs -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-file-pdf"></i> Generar Reportes PDF</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                        <a href="?controller=usuario&action=generarReporteUsuarios" class="btn btn-primary" target="_blank">
                            <i class="fas fa-users"></i> Reporte de Usuarios
                        </a>
                        <a href="?controller=usuario&action=generarReporteProductos" class="btn btn-success" target="_blank">
                            <i class="fas fa-box"></i> Reporte de Productos
                        </a>
                        <a href="?controller=usuario&action=generarReporteCategorias" class="btn btn-info" target="_blank">
                            <i class="fas fa-tags"></i> Reporte de Categorías
                        </a>
                        <a href="?controller=usuario&action=generarReporteFacturasClientes" class="btn btn-warning" target="_blank">
                            <i class="fas fa-receipt"></i> Reporte de Facturas de Clientes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botones para generar Excel -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-file-excel"></i> Generar Reportes Excel</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                        <a href="?controller=usuario&action=generarReporteUsuariosExcel" class="btn btn-primary" target="_blank">
                            <i class="fas fa-users"></i> Reporte de Usuarios Excel
                        </a>
                        <a href="?controller=usuario&action=generarReporteProductosExcel" class="btn btn-success" target="_blank">
                            <i class="fas fa-box"></i> Reporte de Productos Excel
                        </a>
                        <a href="?controller=usuario&action=generarReporteCategoriasExcel" class="btn btn-info" target="_blank">
                            <i class="fas fa-tags"></i> Reporte de Categorías Excel
                        </a>
                        <a href="?controller=usuario&action=generarReporteFacturasClientesExcel" class="btn btn-warning" target="_blank">
                            <i class="fas fa-receipt"></i> Reporte de Facturas de Clientes Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Función para generar colores dinámicos
    function generateColors(count) {
        const colors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40',
            '#FF6384', '#C9CBCF', '#4BC0C0', '#FF6384', '#36A2EB', '#FFCE56'
        ];
        return colors.slice(0, count);
    }

    // Gráfico de Usuarios por Rol (Pie Chart)
    const usuariosPorRolData = <?php echo json_encode($usuariosPorRol); ?>;
    const ctxUsuarios = document.getElementById('usuariosPorRolChart').getContext('2d');
    new Chart(ctxUsuarios, {
        type: 'pie',
        data: {
            labels: usuariosPorRolData.map(item => item.rol),
            datasets: [{
                data: usuariosPorRolData.map(item => item.total_usuarios),
                backgroundColor: generateColors(usuariosPorRolData.length),
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });

    // Gráfico de Productos por Categoría (Bar Chart)
    const productosPorCategoriaData = <?php echo json_encode($productosPorCategoria); ?>;
    const ctxProductos = document.getElementById('productosPorCategoriaChart').getContext('2d');
    new Chart(ctxProductos, {
        type: 'bar',
        data: {
            labels: productosPorCategoriaData.map(item => item.categoria),
            datasets: [{
                label: 'Productos',
                data: productosPorCategoriaData.map(item => item.total_productos),
                backgroundColor: 'rgba(54, 162, 235, 0.8)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                borderRadius: 4,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Gráfico de Distribución de Categorías (Doughnut Chart)
    const categoriasData = <?php echo json_encode($productosPorCategoria); ?>;
    const ctxCategorias = document.getElementById('categoriasDoughnutChart').getContext('2d');
    new Chart(ctxCategorias, {
        type: 'doughnut',
        data: {
            labels: categoriasData.map(item => item.categoria),
            datasets: [{
                data: categoriasData.map(item => item.total_productos),
                backgroundColor: generateColors(categoriasData.length),
                borderWidth: 2,
                borderColor: '#fff',
                hoverBorderWidth: 3
            }]
        },
        options: {
            responsive: true,
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': ' + context.parsed + ' productos (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });

    
</script>

<?php include 'layout/footer.php'; ?>
