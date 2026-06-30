<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket de Compra #{{ $venta->numero_recibo }}</title>
    <style>
        /* Estilos puramente diseñados para impresoras térmicas de 80mm/58mm */
        body {
            font-family: 'Courier New', Courier, monospace; 
            width: 300px; 
            margin: 0 auto;
            padding: 20px;
            color: #000;
        }
        .text-center { text-align: center; }
        .divider { border-top: 1px dashed #000; margin: 10px 0; }
        table { width: 100%; text-align: left; font-size: 14px; border-collapse: collapse; }
        .text-right { text-align: right; }
        
        /* Ocultar el botón de regreso al imprimir el documento */
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    
    <div class="text-center no-print" style="margin-bottom: 20px;">
        <a href="/dashboard-libros" style="padding: 5px 10px; background: #0d6efd; color: white; text-decoration: none; border-radius: 5px; font-family: sans-serif;">⬅ Volver al Sistema</a>
    </div>

    <div class="text-center">
        <h2>LIBRERÍA PARA TODOS</h2>
        <p style="margin: 5px 0;">Sucursal Central</p>
        <p style="margin: 5px 0;">Fecha: {{ $venta->fecha }}</p>
        <p style="margin: 5px 0;">Recibo: <b>{{ $venta->numero_recibo }}</b></p>
        <p style="margin: 5px 0;">Cajero: {{ $venta->user->name ?? $venta->user->nombre }}</p>
    </div>

    <div class="divider"></div>

    <table>
        <thead>
            <tr>
                <th>Cant</th>
                <th>Libro</th>
                <th class="text-right">Importe</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->books as $libro)
            <tr>
                <td style="vertical-align: top;">{{ $libro->pivot->cantidad }}</td>
                <td style="padding-right: 5px;">{{ substr($libro->nombre, 0, 16) }}...</td>
                <td class="text-right" style="vertical-align: top;">${{ number_format($libro->pivot->cantidad * $libro->pivot->precio_unitario, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <div class="text-right">
        <h3>TOTAL: ${{ number_format($venta->total, 2) }}</h3>
    </div>

    <div class="text-center" style="margin-top: 20px;">
        <p>¡Gracias por su compra!</p>
        <p>*** Vuelva pronto ***</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>