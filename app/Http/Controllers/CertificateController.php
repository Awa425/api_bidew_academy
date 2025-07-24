<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CertificateController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/certificates/{id}",
     *     summary="Afficher un certificat",
     *     tags={"Certificates"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du certificat",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Certificat PDF retourné",
     *         @OA\MediaType(mediaType="application/pdf")
     *     ),
     *     @OA\Response(response=404, description="Certificat introuvable")
     * )
     */
    public function show($id)
    {
        $certificate = Certificate::with(['user', 'course'])->findOrFail($id);
        $pdf = Pdf::loadView('certificates.template', [
            'user' => $certificate->user,
            'course' => $certificate->course,
            'issued_at' => $certificate->issued_at,
            'code' => $certificate->certificate_code
        ])->setPaper('A4', 'landscape');

        return $pdf->download('certificate.pdf');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id'
        ]);

        $user = auth()->user();
        $code = Str::uuid();
        $path = 'certificates/' . $code . '.pdf';

        $pdf = Pdf::loadView('certificates.template', [
            'user' => $user,
            'course' => $request->course_id,
            'issued_at' => now(),
            'code' => $code
        ]);
        

        \Storage::put('public/' . $path, $pdf->output());

        $certificate = Certificate::create([
            'user_id' => $user->id,
            'course_id' => $request->course_id,
            'certificate_code' => $code,
            'certificate_path' => $path,
            'issued_at' => now(),
        ]);

        return response()->json(['message' => 'Certificat généré', 'certificate_id' => $certificate->id], 201);
    }
}
