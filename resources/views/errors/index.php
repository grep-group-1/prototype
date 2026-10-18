<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Error <?= $e->code ?? 500 ?> | CapitalEdge</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>body{font-family:'Inter',sans-serif;background:#f8f9fb;min-height:100vh;display:flex;align-items:center;}</style>
</head><body><div class="container text-center py-5">
    <h1 class="display-1 fw-bold text-navy"><?= $e->code ?? 500 ?></h1>
    <h2 class="mb-3"><?= htmlspecialchars($e->title ?? 'Error') ?></h2>
    <p class="text-muted mb-4"><?= htmlspecialchars($e->message ?? 'An unexpected error occurred.') ?></p>
    <a href="/" class="btn btn-dark">Go Home</a>
</div></body></html>
