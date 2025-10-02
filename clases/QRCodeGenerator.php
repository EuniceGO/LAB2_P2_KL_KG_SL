<?php
class QRCodeGenerator {
    // URLs de APIs para códigos QR (orden de prioridad)
    private static $qrAPIs = [
        'qrserver' => 'https://api.qrserver.com/v1/create-qr-code/',
        'quickchart' => 'https://quickchart.io/qr',
        'googlecharts' => 'https://chart.googleapis.com/chart'
    ];
    
    /**
     * Genera una URL para código QR usando la API más confiable
     * @param string $data - Datos a codificar
     * @param int $size - Tamaño del QR
     * @return string - URL del QR
     */
    public static function generateQRCode($data, $size = 200) {
        $encodedData = urlencode($data);
        
        // Prioridad 1: QR Server (más confiable)
        $qrUrl = self::$qrAPIs['qrserver'] . '?size=' . $size . 'x' . $size . '&data=' . $encodedData;
        
        // Verificar si la API responde (test rápido)
        if (self::testQRUrl($qrUrl)) {
            return $qrUrl;
        }
        
        // Fallback 1: QuickChart
        $qrUrl = self::$qrAPIs['quickchart'] . '?text=' . $encodedData . '&size=' . $size;
        if (self::testQRUrl($qrUrl)) {
            return $qrUrl;
        }
        
        // Fallback 2: Google Charts (formato actualizado)
        $qrUrl = self::$qrAPIs['googlecharts'] . '?chs=' . $size . 'x' . $size . '&cht=qr&chl=' . $encodedData;
        
        return $qrUrl;
    }
    
    /**
     * Verifica si una URL de QR responde correctamente
     * @param string $url - URL a verificar
     * @return bool - True si responde, false si no
     */
    private static function testQRUrl($url) {
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_NOBODY, true); // Solo headers
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3); // Timeout corto para test
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            return $httpCode === 200;
        }
        
        return true; // Si no hay CURL, asumir que funciona
    }
    
    /**
     * Genera datos del producto para el QR (URL para móvil)
     * @param object $producto - Objeto producto
     * @return string - URL para acceder al producto desde móvil
     */
    public static function generateProductData($producto) {
        // Generar URL accesible desde móvil en la red WiFi
        $baseUrl = self::getBaseUrl();
        $productUrl = $baseUrl . "?c=producto&a=viewMobile&id=" . $producto->getIdProducto();
        return $productUrl;
    }
    
    /**
     * Obtiene la URL base de la aplicación (configurable para WiFi)
     * @return string - URL base
     */
    private static function getBaseUrl() {
        // Configuración manual de IP para WiFi
        // IP configurada manualmente para acceso desde celular en WiFi
        $host = '192.168.1.24';
        
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        
        // Comentado: detección automática que causaba problemas con VirtualBox
        /*
        // Si estás en desarrollo local, usar la IP de tu máquina
        // Puedes cambiar esta IP por la de tu máquina en la red WiFi
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        
        // Si el host es localhost, intentar obtener la IP local
        if ($host === 'localhost' || $host === '127.0.0.1') {
            $localIP = self::getLocalIP();
            if ($localIP) {
                $host = $localIP;
            }
        }
        */
        
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        if ($scriptName === '/' || $scriptName === '\\') {
            $scriptName = '';
        }
        
        return $protocol . '://' . $host . $scriptName . '/index.php';
    }
    
    /**
     * Intenta obtener la IP local de la máquina
     * @return string|null - IP local o null si no se puede obtener
     */
    private static function getLocalIP() {
        // Método para Windows (funciona en XAMPP)
        if (PHP_OS_FAMILY === 'Windows') {
            $output = shell_exec('ipconfig | findstr /i "IPv4"');
            if ($output) {
                preg_match('/\d+\.\d+\.\d+\.\d+/', $output, $matches);
                if (!empty($matches)) {
                    $ip = $matches[0];
                    // Evitar IPs de loopback o virtuales
                    if (!in_array($ip, ['127.0.0.1', '169.254.'])) {
                        return $ip;
                    }
                }
            }
        }
        
        // Método alternativo usando gethostbyname
        $hostname = gethostname();
        $ip = gethostbyname($hostname);
        if ($ip !== $hostname && filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return $ip;
        }
        
        return null;
    }
    
    /**
     * Genera un código QR específico para un producto
     * @param object $producto - Objeto producto
     * @param int $size - Tamaño del QR
     * @return string - URL del QR del producto
     */
    public static function generateProductQR($producto, $size = 200) {
        $productData = self::generateProductData($producto);
        return self::generateQRCode($productData, $size);
    }
    
    /**
     * Genera un nombre único para el archivo QR
     * @param int $productId - ID del producto
     * @return string - Nombre del archivo
     */
    public static function generateQRFileName($productId) {
        return 'qr_producto_' . $productId . '_' . time() . '.png';
    }
    
    /**
     * Descarga y guarda el QR como imagen local
     * @param string $qrUrl - URL del QR
     * @param string $fileName - Nombre del archivo
     * @param string $directory - Directorio donde guardar (por defecto 'assets/qr/')
     * @return string|false - Ruta del archivo guardado o false en caso de error
     */
    public static function saveQRImage($qrUrl, $fileName, $directory = 'assets/qr/') {
        try {
            // Crear directorio si no existe
            if (!file_exists($directory)) {
                if (!mkdir($directory, 0777, true)) {
                    error_log("Error: No se pudo crear el directorio $directory");
                    return false;
                }
            }
            
            $filePath = $directory . $fileName;
            $imageData = false;
            
            // Método prioritario: CURL (más confiable en Windows/XAMPP)
            if (function_exists('curl_init')) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $qrUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
                
                $imageData = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
                $curlError = curl_error($ch);
                curl_close($ch);
                
                // Verificar que sea una imagen válida
                if ($curlError) {
                    error_log("CURL Error: " . $curlError);
                    $imageData = false;
                } elseif ($httpCode !== 200) {
                    error_log("HTTP Error: " . $httpCode . " para URL: " . $qrUrl);
                    $imageData = false;
                } elseif (!$imageData || strlen($imageData) < 100) {
                    error_log("Datos insuficientes descargados: " . strlen($imageData) . " bytes");
                    $imageData = false;
                } elseif (strpos($contentType, 'image/') === false && strpos($contentType, 'application/octet-stream') === false) {
                    error_log("Tipo de contenido incorrecto: " . $contentType . " (esperaba imagen)");
                    $imageData = false;
                } elseif (strpos($imageData, '<html') !== false || strpos($imageData, '<!DOCTYPE') !== false) {
                    error_log("Se descargó HTML en lugar de imagen desde: " . $qrUrl);
                    $imageData = false;
                }
            }
            
            // Método fallback: file_get_contents (solo si allow_url_fopen está habilitado)
            if ($imageData === false && ini_get('allow_url_fopen')) {
                $context = stream_context_create([
                    'http' => [
                        'timeout' => 30,
                        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
                    ],
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false
                    ]
                ]);
                
                $imageData = @file_get_contents($qrUrl, false, $context);
                
                // Verificar que no sea HTML
                if ($imageData && (strpos($imageData, '<html') !== false || strpos($imageData, '<!DOCTYPE') !== false)) {
                    error_log("file_get_contents descargó HTML en lugar de imagen desde: " . $qrUrl);
                    $imageData = false;
                }
            }
            
            if ($imageData !== false && !empty($imageData)) {
                if (file_put_contents($filePath, $imageData)) {
                    return $filePath;
                } else {
                    error_log("Error: No se pudo escribir el archivo $filePath");
                }
            } else {
                error_log("Error: No se pudo descargar imagen válida desde $qrUrl");
            }
            
        } catch (Exception $e) {
            error_log("Error en saveQRImage: " . $e->getMessage());
        }
        
        return false;
    }
    
    /**
     * Método alternativo usando URL directa (sin descargar archivo)
     * @param object $producto - Objeto producto
     * @return string - URL directa del QR
     */
    public static function generateProductQRUrl($producto) {
        return self::generateProductQR($producto);
    }
    
    /**
     * Proceso completo: genera QR y lo guarda como imagen
     * @param object $producto - Objeto producto
     * @return string|false - Ruta del archivo guardado o false en caso de error
     */
    public static function generateAndSaveProductQR($producto) {
        try {
            $qrUrl = self::generateProductQR($producto);
            $fileName = self::generateQRFileName($producto->getIdProducto());
            
            // Intentar guardar como archivo local
            $filePath = self::saveQRImage($qrUrl, $fileName);
            
            if ($filePath) {
                return $filePath;
            } else {
                // Si falla al guardar, devolver false para no guardar URL inválida
                error_log("Error: No se pudo guardar la imagen QR para el producto " . $producto->getIdProducto());
                return false;
            }
            
        } catch (Exception $e) {
            error_log("Error en generateAndSaveProductQR: " . $e->getMessage());
            return false;
        }
    }
}
?>