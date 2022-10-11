<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>404 Not Found</title>
</head>
<body>
<?php if ($displayErrors): ?>
    <h1>404 Not found</h1>
    <p>Message: <?php echo $message ?></p>
    <p>File: <?php echo $file . ':' . $line ?></p>
    <hr>
    <h3>Request: </h3>
    <pre><?php var_export($request) ?></pre>
    <hr>
    <h3>Stacktrace: </h3>
    <pre><?php var_export($trace) ?></pre>
<?php else: ?>
    <h1>404 Not found</h1>
<?php endif; ?>
</body>
</html>