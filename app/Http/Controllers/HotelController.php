<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HotelController extends Controller
{
    /**
     * Mostrar la lista de hoteles enriquecida desde la API externa.
     */
    public function index()
    {
        // 1. Consumir la API REST externa (simulada con JSONPlaceholder posts)
        try {
            $response = Http::get('https://jsonplaceholder.typicode.com/posts');
            $posts = $response->successful() ? $response->json() : [];
        } catch (\Exception $e) {
            $posts = [];
        }

        // Tomar solo los primeros 10 para hacer el ejercicio simple y claro
        $posts = array_slice($posts, 0, 10);

        // Datos estáticos sencillos para enriquecer el JSON de posts
        $hotelesEstaticos = [
            ['name' => 'Hotel Cartagena Caribe', 'city' => 'Cartagena', 'price' => 250000, 'rooms' => 12, 'rating' => 4.8, 'image' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=200&h=150&fit=crop'],
            ['name' => 'Hotel Medellín Plaza', 'city' => 'Medellín', 'price' => 180000, 'rooms' => 8, 'rating' => 4.5, 'image' => 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?w=200&h=150&fit=crop'],
            ['name' => 'Hotel Bogotá Imperial', 'city' => 'Bogotá', 'price' => 220000, 'rooms' => 20, 'rating' => 4.6, 'image' => 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=200&h=150&fit=crop'],
            ['name' => 'Hotel Santa Marta Sol', 'city' => 'Santa Marta', 'price' => 300000, 'rooms' => 5, 'rating' => 4.9, 'image' => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=200&h=150&fit=crop'],
            ['name' => 'Hotel San Andrés Beach', 'city' => 'San Andrés', 'price' => 350000, 'rooms' => 15, 'rating' => 4.7, 'image' => 'https://images.unsplash.com/photo-1439066615861-d1af74d74000?w=200&h=150&fit=crop'],
            ['name' => 'Hotel Eje Cafetero Colonial', 'city' => 'Eje Cafetero', 'price' => 150000, 'rooms' => 6, 'rating' => 4.4, 'image' => 'https://images.unsplash.com/photo-1584132967334-10e028bd69f7?w=200&h=150&fit=crop'],
            ['name' => 'Hotel Cali Salsa Club', 'city' => 'Cali', 'price' => 130000, 'rooms' => 18, 'rating' => 4.3, 'image' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=200&h=150&fit=crop'],
            ['name' => 'Hotel Villa de Leyva Colonial', 'city' => 'Villa de Leyva', 'price' => 170000, 'rooms' => 4, 'rating' => 4.5, 'image' => 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?w=200&h=150&fit=crop'],
            ['name' => 'Hotel Barranquilla Sol', 'city' => 'Barranquilla', 'price' => 160000, 'rooms' => 22, 'rating' => 4.2, 'image' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?w=200&h=150&fit=crop'],
            ['name' => 'Hotel Amazonas EcoLodge', 'city' => 'Leticia', 'price' => 200000, 'rooms' => 7, 'rating' => 4.6, 'image' => 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?w=200&h=150&fit=crop']
        ];

        // Mapear los datos de la API externa a nuestros hoteles estáticos
        $hotels = [];
        foreach ($posts as $index => $post) {
            $meta = $hotelesEstaticos[$index % count($hotelesEstaticos)];
            
            $hotels[] = [
                'id' => $post['id'],
                'name' => $meta['name'],
                'description' => $post['body'], // Tomado de la API JSON
                'city' => $meta['city'],
                'price' => $meta['price'],
                'rooms' => $meta['rooms'],
                'rating' => $meta['rating'],
                'image' => $meta['image'],
            ];
        }

        return view('hotels', compact('hotels'));
    }

    /**
     * Registrar una nueva reserva usando método POST.
     */
    public function storeReserve(Request $request)
    {
        // Validar los campos simples obligatorios de forma básica
        $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
        ], [
            'title.required' => 'El título de la reserva es obligatorio.',
            'body.required' => 'Los detalles/cuerpo de la reserva son obligatorios.',
        ]);

        // Guardar valores del request
        $title = $request->input('title');
        $body = $request->input('body');

        try {
            // Consumir API externa usando el método POST (tal cual el ejemplo)
            $response = Http::post('https://jsonplaceholder.typicode.com/posts', [
                'title' => $title,
                'body' => $body
            ]);

            if ($response->successful()) {
                // Retornar a la vista con éxito y la respuesta JSON del servidor
                return back()->with([
                    'success' => '¡Reserva registrada con éxito mediante POST!',
                    'response_json' => $response->json(),
                    'sent_title' => $title,
                    'sent_body' => $body
                ]);
            } else {
                return back()->withErrors(['api' => 'Error al registrar la reserva en la API externa. Código: ' . $response->status()]);
            }
        } catch (\Exception $e) {
            return back()->withErrors(['api' => 'No se pudo conectar con la API externa para procesar la reserva.']);
        }
    }
}
