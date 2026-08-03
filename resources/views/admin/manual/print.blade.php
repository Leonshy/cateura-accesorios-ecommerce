<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Manual de uso — Cateura Accesorios</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Poppins', 'Segoe UI', sans-serif;
            color: #2c2219;
            max-width: 800px;
            margin: 0 auto;
            padding: 2.5rem 2rem 4rem;
            line-height: 1.65;
            font-size: 15px;
        }
        h1 { font-family: 'Cormorant Garamond', Georgia, serif; font-size: 2.1rem; margin: 0 0 .3rem; }
        h1 + p { color: #7a6c5d; margin-top: 0; }
        h2 { font-family: 'Cormorant Garamond', Georgia, serif; font-size: 1.4rem; margin: 2rem 0 .85rem; padding-bottom: .4rem; border-bottom: 1px solid #e8dcc9; page-break-after: avoid; }
        h3 { font-size: 1.05rem; font-weight: 600; margin: 1.25rem 0 .5rem; page-break-after: avoid; }
        p { margin: 0 0 .8rem; }
        ul, ol { margin: 0 0 1rem; padding-left: 1.35rem; }
        li { margin-bottom: .3rem; }
        strong { color: #2c2219; }
        code { font-family: ui-monospace, monospace; font-size: .85em; background: #f7f0e6; border: 1px solid #e8dcc9; border-radius: 4px; padding: .1em .4em; color: #b5521a; }
        blockquote { border-left: 3px solid #b5521a; background: #fdf6f0; padding: .7rem 1rem; margin: 1rem 0; color: #58250d; font-size: .92rem; page-break-inside: avoid; }
        blockquote p:last-child { margin-bottom: 0; }
        table { width: 100%; border-collapse: collapse; margin: 1rem 0 1.4rem; font-size: .85rem; page-break-inside: avoid; }
        th, td { border: 1px solid #ccb996; padding: .45rem .65rem; text-align: left; }
        th { background: #f7f0e6; }
        hr { border: none; border-top: 1px solid #e8dcc9; margin: 1.75rem 0; }
        .print-toolbar {
            position: sticky; top: 0;
            background: #b5521a; color: #fff;
            padding: .75rem 1rem;
            display: flex; justify-content: space-between; align-items: center;
            margin: -2.5rem -2rem 2rem;
        }
        .print-toolbar button {
            background: #fff; color: #b5521a; border: none;
            padding: .45rem 1rem; font-size: .85rem; font-weight: 600;
            border-radius: 5px; cursor: pointer;
        }
        @media print {
            .print-toolbar { display: none; }
            body { padding: 0; max-width: none; }
        }
    </style>
</head>
<body>
    <div class="print-toolbar">
        <span>Usá "Guardar como PDF" en el diálogo de impresión de tu navegador.</span>
        <button onclick="window.print()">Imprimir / Guardar PDF</button>
    </div>
    {!! $html !!}
</body>
</html>
