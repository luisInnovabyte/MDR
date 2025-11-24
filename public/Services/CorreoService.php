<?php

namespace TOLDOS\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

class CorreoService
{
    private $configuracion = [
        'servidor' => 'smtp.gmail.com',
        'usuario' => 'alejandrosolvam@gmail.com',
        'contraseña' => 'lfxx puwp gprc nzbf',
        'seguridad' => 'ssl',
        'puerto' => 465,
        'remitente' => 'alejandrosolvam@gmail.com',
        'nombre_remitente' => 'AleKarting',
        'destinatario' => 'alejandrosolvam@gmail.com',
        'nombre_destinatario' => 'Alejandro Jiménez Cabrera'
    ];

    public function __construct()
    {
        // Incluir PHPMailer (igual que en el controlador original)
        require_once '../lib/PHPMailer/src/PHPMailer.php';
        require_once '../lib/PHPMailer/src/SMTP.php';
        require_once '../lib/PHPMailer/src/Exception.php';
    }

    /**
     * Envía un correo electrónico
     * data Datos del correo
     * Resultado del envío
     */
    public function enviar(array $data): array
    {
        // Validación igual que en el controlador original
        if (!$data || !is_array($data)) {
            return [
                'success' => false,
                'message' => 'Datos incompletos o no válidos',
                'errors' => ['general' => 'Datos no válidos']
            ];
        }

        if (empty($data['email'])) {
            return [
                'success' => false,
                'message' => 'Datos incompletos o no válidos. El campo email es requerido.',
                'errors' => ['email' => 'El campo email es obligatorio']
            ];
        }

        log_message('debug', 'Datos recibidos para correo: ' . print_r($data, true));

        try {
            $mail = new PHPMailer(true);

            // Configuración SMTP (igual que en el controlador original)
            $mail->isSMTP();
            $mail->Host       = $this->configuracion['servidor'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->configuracion['usuario'];
            $mail->Password   = $this->configuracion['contraseña'];
            $mail->SMTPSecure = $this->configuracion['seguridad'];
            $mail->Port       = $this->configuracion['puerto'];
            $mail->CharSet    = 'UTF-8';

            // Remitente y destinatario (igual que en el controlador original)
            $mail->setFrom(
                $this->configuracion['remitente'], 
                $this->configuracion['nombre_remitente']
            );
            $mail->addAddress($data['email'], $data['nombre'] ?? ''); // Destinatario: usuario
            $mail->addBCC($this->configuracion['destinatario']);       // Copia oculta: tú
            $mail->addReplyTo($data['email'], $data['nombre'] ?? '');  // Si alguien responde, va al usuario


            // Contenido (igual que en el controlador original)
        $mail->isHTML(true);
        $mail->Subject = $data['asunto'] ?? 'Nuevo mensaje de contacto';
        
        if (strpos($data['mensaje'] ?? '', '<div style=') !== false) {
            // Si es un mensaje de recuperación (contiene div con estilos)
            $mail->Body = $data['mensaje'];
            $mail->AltBody = strip_tags(preg_replace('/<br\s?\/?>/i', "\n", $data['mensaje']));
        } else {
            // Para otros tipos de correo
            $mail->Body = $this->crearCuerpoHTML($data);
            $mail->AltBody = $this->crearCuerpoTexto($data);
        }

            $mail->send();

            return [
                'success' => true,
                'message' => 'Correo enviado con éxito'
            ];

        } catch (PHPMailerException $e) {
            log_message('error', 'Error PHPMailer: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al enviar el correo: ' . $e->getMessage()
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error inesperado: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Ocurrió un error inesperado'
            ];
        }
    }

    /**
 * Envía un correo al administrador desde el formulario de contacto
 * data Datos del formulario
 */
public function enviarContacto(array $data): array
{
    // Validar
    if (empty($data['email']) || empty($data['mensaje']) || empty($data['nombre'])) {
        return [
            'success' => false,
            'message' => 'Faltan datos obligatorios',
            'errors' => [
                'email' => 'Email requerido',
                'nombre' => 'Nombre requerido',
                'mensaje' => 'Mensaje requerido'
            ]
        ];
    }

    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = $this->configuracion['servidor'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $this->configuracion['usuario'];
        $mail->Password   = $this->configuracion['contraseña'];
        $mail->SMTPSecure = $this->configuracion['seguridad'];
        $mail->Port       = $this->configuracion['puerto'];
        $mail->CharSet    = 'UTF-8';

        // El destinatario SIEMPRE eres tú
        $mail->setFrom($this->configuracion['remitente'], $this->configuracion['nombre_remitente']);
        $mail->addAddress($this->configuracion['destinatario'], $this->configuracion['nombre_destinatario']);
        
        // El usuario aparece en Reply-To
        $mail->addReplyTo($data['email'], $data['nombre']);

        // Asunto y cuerpo
        $mail->isHTML(true);
        $mail->Subject = $data['asunto'] ?? 'Mensaje de contacto';
        $mail->Body    = $this->crearCuerpoHTML($data);
        $mail->AltBody = $this->crearCuerpoTexto($data);

        $mail->send();

        return [
            'success' => true,
            'message' => 'El mensaje fue enviado correctamente.'
        ];

    } catch (PHPMailerException $e) {
        log_message('error', 'PHPMailer Error: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error al enviar el mensaje: ' . $e->getMessage()
        ];
    }
}


    // Métodos privados idénticos al controlador original
    private function crearCuerpoHTML($datos)
    {
        // Validación de campos con valores por defecto
        $nombre = htmlspecialchars($datos['nombre'] ?? 'No proporcionado');
        $email = htmlspecialchars($datos['email'] ?? 'No proporcionado');
        $asunto = htmlspecialchars($datos['asunto'] ?? '');
        $mensaje = nl2br(htmlspecialchars($datos['mensaje'] ?? ''));
        
        // Configuración del logo
        $logoUrl = 'https://alejandrojimenez.com.es/imagenLogo/logo_karting.png';
    
        return '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Nuevo mensaje de contacto</title>
            <style>
                /* Estilos base modernizados */
                body {
                    font-family: "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                    line-height: 1.6;
                    color: #2d3748;
                    background-color: #f8fafc;
                    margin: 0;
                    padding: 0;
                }
                
                /* Contenedor principal con sombra más suave */
                .email-container {
                    max-width: 600px;
                    margin: 20px auto;
                    background: #ffffff;
                    border-radius: 12px;
                    overflow: hidden;
                    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                    border: 1px solid #e2e8f0;
                }
                
                /* Cabecera con gradiente azul */
                .email-header {
                    background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
                    color: white;
                    padding: 30px;
                    text-align: center;
                    border-bottom: 4px solid #3730a3;
                }
                
                .email-header h1 {
                    margin: 15px 0 0;
                    font-size: 26px;
                    font-weight: 700;
                    letter-spacing: 0.5px;
                    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
                }
                
                /* Cuerpo del email con más espacio */
                .email-body {
                    padding: 35px 30px;
                }
                
                .info-item {
                    margin-bottom: 18px;
                    display: flex;
                    align-items: flex-start;
                }
                
                .info-label {
                    font-weight: 600;
                    color: #4f46e5;
                    width: 90px;
                    flex-shrink: 0;
                    font-size: 15px;
                }
                
                .info-value {
                    flex-grow: 1;
                    color: #4a5568;
                }
                
                /* Contenedor de mensaje mejorado */
                .message-container {
                    background: #f8fafc;
                    border-left: 4px solid #4f46e5;
                    padding: 20px;
                    margin-top: 25px;
                    border-radius: 0 8px 8px 0;
                    box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05);
                }
                
                .message-container p {
                    margin-top: 0;
                    color: #4f46e5;
                    font-weight: 600;
                    font-size: 16px;
                }
                
                /* Pie de email más elegante */
                .email-footer {
                    background: #f1f5f9;
                    padding: 25px 20px;
                    text-align: center;
                    font-size: 13px;
                    color: #64748b;
                    border-top: 1px solid #e2e8f0;
                }
                
                .email-footer p {
                    margin: 5px 0;
                }
                
                .logo {
                    max-width: 200px;
                    height: auto;
                    margin: 0 auto;
                    display: block;
                    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
                }
                
                /* Efectos hover para interactividad */
                a {
                    color: #4f46e5;
                    text-decoration: none;
                    transition: color 0.2s;
                }
                
                a:hover {
                    color: #3730a3;
                    text-decoration: underline;
                }
                
                /* Responsive mejorado */
                @media only screen and (max-width: 600px) {
                    .email-container {
                        margin: 10px;
                        border-radius: 8px;
                    }
                    
                    .email-header, .email-body, .email-footer {
                        padding: 25px 20px;
                    }
                    
                    .email-header h1 {
                        font-size: 22px;
                    }
                    
                    .logo {
                        max-width: 160px;
                    }
                    
                    .info-item {
                        flex-direction: column;
                    }
                    
                    .info-label {
                        width: 100%;
                        margin-bottom: 5px;
                    }
                }
            </style>
        </head>
        <body>
            <div class="email-container">
                <div class="email-header">
                    <img src="'.$logoUrl.'" alt="Logo AleKarting" class="logo">
                    <h1>Nuevo mensaje de contacto</h1>
                </div>
                
                <div class="email-body">
                    <div class="info-item">
                        <span class="info-label">Nombre:</span>
                        <span class="info-value">'.$nombre.'</span>
                    </div>
                    
                    <div class="info-item">
                        <span class="info-label">Email:</span>
                        <span class="info-value">'.$email.'</span>
                    </div>'
                    .(!empty($asunto) ? '
                    <div class="info-item">
                        <span class="info-label">Asunto:</span>
                        <span class="info-value">'.$asunto.'</span>
                    </div>' : '').'
                    
                    <div class="message-container">
                        <p><strong>Mensaje:</strong></p>
                        <div class="info-value">'.$mensaje.'</div>
                    </div>
                </div>
                
                <div class="email-footer">
                    <p>Este mensaje fue enviado desde la página de <a href="https://alejandrojimenez.com.es/AleKarting/">AleKarting</a></p>
                    <p>&copy; '.date('Y').' AleKarting. Todos los derechos reservados.</p>
                </div>
            </div>
        </body>
        </html>';
    }

    private function crearCuerpoTexto($datos)
    {
        $nombre = $datos['nombre'] ?? 'No proporcionado';
        $email = $datos['email'] ?? 'No proporcionado';
        $asunto = $datos['asunto'] ?? '';
        $mensaje = $datos['mensaje'] ?? '';

        return "NUEVO MENSAJE DE ALEKARTING\n\n" .
            "==========================\n\n" .
            "Nombre: {$nombre}\n" .
            "Email: {$email}\n" .
            (!empty($asunto) ? "Asunto: {$asunto}\n" : "") .
            "\nMensaje:\n" .
            str_repeat("-", 50) . "\n" .
            wordwrap($mensaje, 70) . "\n" .
            str_repeat("-", 50) . "\n\n" .
            "Enviado el: " . date('d/m/Y H:i') . "\n" .
            "Desde el formulario de contacto de InnovaByte";
    }
}