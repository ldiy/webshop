<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>500 Internal Server Error</title>
</head>
<body>
<?php if ($displayErrors): ?>
    <h1>500 Internal Server Error</h1>
    <p>Message: <?php echo $message ?></p>
    <p>File: <?php echo $file . ':' . $line ?></p>
    <hr>
    <h3>Request: </h3>
    <pre><?php var_export($request) ?></pre>
    <hr>
    <h3>Previous: </h3>
    <pre><?php var_export($previous) ?></pre>
    <hr>
    <h3>Stacktrace: </h3>
    <pre><?php var_export($trace) ?></pre>
<?php else: ?>
    <h1>Internal Server Error</h1>
    <p>The server encountered an internal error or misconfiguration and was unable to complete your request.</p>
    <p>Please contact the server administrator at webmaster@localhost to inform them of the time this error occurred, and the actions you performed just before this error.</p>
    <p>More information about this error may be available in the server error log.</p>
<?php endif; ?>
</body>
</html>