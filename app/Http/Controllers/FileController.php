<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
     public function serveFile(string $filename)
    {
        // Décoder le nom du fichier au cas où il contiendrait des caractères spéciaux
        $filename = urldecode($filename);
        
        // Chemin vers le fichier dans storage/app/public/lessons/files/
        $path = 'lessons/files/' . $filename;
        
        // Vérifier si le fichier existe
        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'File not found: ' . $filename);
        }
        
        // Obtenir le chemin complet du fichier
        $fullPath = Storage::disk('public')->path($path);
        
        // Obtenir le type MIME du fichier
        $mimeType = Storage::disk('public')->mimeType($path);
        
        // Retourner le fichier avec les headers appropriés
        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Access-Control-Allow-Origin' => 'http://localhost:4200',
            'Access-Control-Allow-Methods' => 'GET, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization',
        ]);
    }
}
