<?php
/**
 * Database Connection Manager (PDO)
 * MedTour Services
 */

require_once __DIR__ . '/constants.php';

/**
 * Returns a shared PDO database instance.
 *
 * @return PDO
 */
function getDbConnection(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_NAME,
            DB_CHARSET
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Server-side logging without exposing secrets
            $logDir = LOGS_PATH;
            if (!is_dir($logDir)) {
                @mkdir($logDir, 0755, true);
            }
            $logMessage = sprintf(
                "[%s] Database connection failed: %s in %s on line %d\n",
                date('Y-m-d H:i:s'),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            );
            @file_put_contents($logDir . '/database_errors.log', $logMessage, FILE_APPEND);

            // Safe user-facing error response
            http_response_code(500);
            ?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Service Unavailable - <?php echo htmlspecialchars(APP_NAME); ?></title>
                <style>
                    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f8fafc; color: #1e293b; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
                    .card { background: white; padding: 2.5rem; border-radius: 1rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); max-width: 480px; text-align: center; border: 1px solid #e2e8f0; }
                    h1 { font-size: 1.5rem; margin-bottom: 0.5rem; color: #0f172a; }
                    p { color: #64748b; font-size: 0.95rem; line-height: 1.5; margin-bottom: 1.5rem; }
                    .btn { display: inline-block; background: #0284c7; color: white; text-decoration: none; padding: 0.75rem 1.5rem; border-radius: 0.5rem; font-weight: 600; font-size: 0.9rem; }
                </style>
            </head>
            <body>
                <div class="card">
                    <h1>Service Temporarily Unavailable</h1>
                    <p>We are experiencing a temporary issue connecting to our services. Our engineering team has been notified. Please try again in a few moments.</p>
                    <a href="javascript:location.reload()" class="btn">Retry Connection</a>
                </div>
            </body>
            </html>
            <?php
            exit();
        }
    }

    return $pdo;
}
