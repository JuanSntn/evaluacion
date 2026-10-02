<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\JsonPlaceholderPostService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class ServicePostController extends Controller
{
    public function __construct(private readonly JsonPlaceholderPostService $posts) {}

    public function index(): JsonResponse
    {
        try {
            return response()->json(['data' => $this->posts->all()]);
        } catch (ConnectionException) {
            return response()->json(['message' => 'No fue posible conectar con el servicio de publicaciones.'], 504);
        } catch (RequestException | RuntimeException) {
            return response()->json(['message' => 'No fue posible obtener las publicaciones.'], 502);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate(['title' => ['required', 'string'], 'body' => ['required', 'string']]);
        try {
            $post = $this->posts->create(['title' => $data['title'], 'body' => $data['body'], 'userId' => $request->user()->getKey()]);

            return response()->json(['message' => 'Publicación creada correctamente.', 'data' => $post], 201);
        } catch (ConnectionException) {
            return response()->json(['message' => 'No fue posible conectar con el servicio de publicaciones.'], 504);
        } catch (RequestException | RuntimeException) {
            return response()->json(['message' => 'No fue posible crear la publicación.'], 502);
        }
    }

    public function update(Request $request, int $post): JsonResponse
    {
        $data = $request->validate(['title' => ['required', 'string'], 'body' => ['required', 'string']]);
        try {
            $updatedPost = $this->posts->update($post, ['title' => $data['title'], 'body' => $data['body'], 'userId' => $request->user()->getKey()]);

            return response()->json(['message' => 'Publicación actualizada correctamente.', 'data' => $updatedPost]);
        } catch (ConnectionException) {
            return response()->json(['message' => 'No fue posible conectar con el servicio de publicaciones.'], 504);
        } catch (RequestException | RuntimeException) {
            return response()->json(['message' => 'No fue posible actualizar la publicación.'], 502);
        }
    }

    public function destroy(int $post): JsonResponse
    {
        try {
            $this->posts->delete($post);

            return response()->json(['message' => 'Publicación eliminada correctamente.']);
        } catch (ConnectionException) {
            return response()->json(['message' => 'No fue posible conectar con el servicio de publicaciones.'], 504);
        } catch (RequestException | RuntimeException) {
            return response()->json(['message' => 'No fue posible eliminar la publicación.'], 502);
        }
    }
}
