@props(['user', 'size' => '40px', 'fontSize' => '14px'])

@php
// 1. LÓGICA DE INICIALES (Mejorada)
$nombre = $user->name ?? 'Usuario';
$palabras = explode(' ', $nombre);
$iniciales = '';

if (count($palabras) >= 2) {
$iniciales = substr($palabras[0], 0, 1) . substr($palabras[1], 0, 1);
} else {
$iniciales = substr($nombre, 0, 2);
}
$iniciales = strtoupper($iniciales);

// 2. PALETA DE COLORES PREMIUM (Esteroides)
// He seleccionado combinaciones de colores con buen contraste
$paletas = [
['bg' => '#4A90D9', 'text' => '#FFFFFF'], // Azul
['bg' => '#E67E22', 'text' => '#FFFFFF'], // Naranja
['bg' => '#2ECC71', 'text' => '#FFFFFF'], // Verde
['bg' => '#9B59B6', 'text' => '#FFFFFF'], // Morado
['bg' => '#E74C3C', 'text' => '#FFFFFF'], // Rojo
['bg' => '#1ABC9C', 'text' => '#FFFFFF'], // Turquesa
['bg' => '#F39C12', 'text' => '#FFFFFF'], // Amarillo oscuro
['bg' => '#34495E', 'text' => '#FFFFFF'], // Azul noche
['bg' => '#FF6B6B', 'text' => '#FFFFFF'], // Coral
['bg' => '#4834D4', 'text' => '#FFFFFF'], // Deep Royal
['bg' => '#6AB04C', 'text' => '#FFFFFF'], // Badlands Green
['bg' => '#EB4D4B', 'text' => '#FFFFFF'], // Carmine Pink
['bg' => '#22A6B3', 'text' => '#FFFFFF'], // Coastal Blue
['bg' => '#BE2EDD', 'text' => '#FFFFFF'], // Steel Pink
['bg' => '#F0932B', 'text' => '#FFFFFF'], // Orange Hibiscus
['bg' => '#535C68', 'text' => '#FFFFFF'], // Iron Gray
['bg' => '#686DE0', 'text' => '#FFFFFF'], // Excalibur
['bg' => '#B33771', 'text' => '#FFFFFF'], // Fiery Fuchsia
['bg' => '#130F40', 'text' => '#FFFFFF'], // Deep Cove
['bg' => '#009432', 'text' => '#FFFFFF'] // Emerald
];

// 3. ASIGNACIÓN DETERMINISTA (Mismo usuario = Mismo color siempre)
// Usamos el ID o el Nombre para elegir un color del array
$index = (crc32($user->email ?? $user->name) % count($paletas));
$colorFondo = $paletas[$index]['bg'];
$colorTexto = $paletas[$index]['text'];
@endphp

<div {{ $attributes->merge(['class' => 'user-avatar-circle']) }}
    style="
        width: {{ $size }}; 
        height: {{ $size }}; 
        background-color: {{ $colorFondo }}; 
        color: {{ $colorTexto }};
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: {{ $fontSize }};
        font-weight: 700;
        text-transform: uppercase;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        border: 2px solid rgba(255,255,255,0.2);
        user-select: none;
        transition: transform 0.2s ease;
     ">
    {{ $iniciales }}
</div>

<style>
    .user-avatar-circle:hover {
        transform: scale(1.05);
    }
</style>