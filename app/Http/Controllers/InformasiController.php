<?php

namespace App\Http\Controllers;

use App\Models\Informasi;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class InformasiController extends Controller
{
    public function index()
    {
        $informasi = Informasi::orderBy('tanggal', 'desc')->get();
        return view('informasi', compact('informasi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string',
            'isi' => 'required|string',
            'file' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        // Start with default values
        $data = [
            'judul' => $request->judul,
            'isi' => $request->isi,
            'tanggal' => now(),
        ];

        $storedFilePath = null;

        // Handle file upload if present
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Store file and get path
            $path = $file->storeAs('uploads/Informasi', $fileName, 'public');
            $storedFilePath = storage_path('app/public/' . $path);

            // Add file_pdf to data array only when file is present
            $data['file_pdf'] = $fileName;

            // Debug information
            Log::info('File uploaded successfully');
            Log::info('File name: ' . $fileName);
            Log::info('Storage path: ' . $path);
            Log::info('Full file path: ' . $storedFilePath);
        } else if ($request->hasFile('file')) {
            Log::error('File upload failed validation');
            return redirect()->back()->with('error', 'File upload gagal. Pastikan file adalah PDF yang valid.');
        }

        // Create new informasi record
        try {
            $informasi = Informasi::create($data);
            Log::info('Database record created: ' . $informasi->id_informasi);
        } catch (\Exception $e) {
            Log::error('Database error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }

        // Send WhatsApp notifications
        try {
            $nomorOrtu = Siswa::pluck('no_orangtua')->toArray();

            // For debugging with limited numbers
            // $nomorOrtu = array_slice($nomorOrtu, 0, 1); // Just send to first number for testing

            foreach ($nomorOrtu as $nomor) {
                $this->kirimPesanWhatsApp($nomor, $informasi, $storedFilePath);
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp notification error: ' . $e->getMessage());
            // Continue execution even if WhatsApp notification fails
        }

        return redirect()->back()->with('success', 'Informasi berhasil ditambahkan!');
    }

    private function kirimPesanWhatsApp($nomor, $informasi, $filePath = null)
    {
        $apiKey = "CFwxvW52cgTBRSxKSprj";

        $pesan = "📢 *PENGUMUMAN* 📢\n\n"
        ."*Assalamu'alaikum Wr. Wb.*\n"
        ."*Shalom*\n"
        ."*Om Swastiastu*\n"
        ."*Namo Buddhaya dan Salam Kebajikan*\n"
        ."\n"
        . "{$informasi->judul}\n"
        ."\n"
        . "{$informasi->isi}\n"
        ."\n"
        . "Jember, {$informasi->tanggal->format('d-m-Y')}\n"
        ."\n"
        ."*Humas SMKN 2 Jember*";

        try {
            // Check if file exists and log its details
            if ($filePath && file_exists($filePath)) {
                Log::info('File exists and will be sent: ' . $filePath);
                Log::info('File size: ' . filesize($filePath) . ' bytes');

                // Use cURL directly for reliable file sending
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api.fonnte.com/send',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => array(
                        'target' => $nomor,
                        'message' => $pesan,
                        'countryCode' => '62',
                        'delay' => '2',
                        'file' => new \CURLFile($filePath, 'application/pdf', basename($filePath))
                    ),
                    CURLOPT_HTTPHEADER => array(
                        'Authorization: ' . $apiKey
                    ),
                ));

                $responseBody = curl_exec($curl);
                $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

                if (curl_errno($curl)) {
                    $errorMsg = curl_error($curl);
                    Log::error('CURL Error: ' . $errorMsg);
                }

                curl_close($curl);

                Log::info('CURL Response Code: ' . $httpCode);
                Log::info('CURL Response: ' . $responseBody);

                return json_decode($responseBody, true);
            } else {
                // No file to send, just send the message
                Log::info('Sending WhatsApp without file to: ' . $nomor);

                // Use cURL for consistency with file sending method
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api.fonnte.com/send',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => array(
                        'target' => $nomor,
                        'message' => $pesan,
                        'countryCode' => '62',
                        'delay' => '2'
                    ),
                    CURLOPT_HTTPHEADER => array(
                        'Authorization: ' . $apiKey
                    ),
                ));

                $responseBody = curl_exec($curl);
                $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

                if (curl_errno($curl)) {
                    $errorMsg = curl_error($curl);
                    Log::error('CURL Error: ' . $errorMsg);
                }

                curl_close($curl);

                Log::info('CURL Response Code: ' . $httpCode);
                Log::info('CURL Response: ' . $responseBody);

                return json_decode($responseBody, true);
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp API error: ' . $e->getMessage());
            Log::error('Exception trace: ' . $e->getTraceAsString());
            return ['error' => $e->getMessage()];
        }
    }

    public function edit($id)
    {
        $informasi = Informasi::findOrFail($id);
        return response()->json($informasi);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'file' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        $informasi = Informasi::findOrFail($id);

        $data = [
            'judul' => $request->judul,
            'isi' => $request->isi,
        ];

        // Handle file upload if present
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            // Delete old file if exists
            if ($informasi->file_pdf) {
                Storage::disk('public')->delete('uploads/Informasi/' . $informasi->file_pdf);
            }

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Store file and get path
            $path = $file->storeAs('uploads/Informasi', $fileName, 'public');

            // Add file_pdf to data array
            $data['file_pdf'] = $fileName;

            Log::info('Updated file: ' . $fileName);
        }

        // Update informasi record
        try {
            $informasi->update($data);
            Log::info('Record updated: ' . json_encode($data));
        } catch (\Exception $e) {
            Log::error('Update error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }

        return response()->json(['success' => true, 'message' => 'Informasi berhasil diperbarui']);
    }

    public function destroy($id)
    {
        $informasi = Informasi::findOrFail($id);

        // Delete associated file if exists
        if ($informasi->file_pdf) {
            Storage::disk('public')->delete('uploads/Informasi/' . $informasi->file_pdf);
        }

        $informasi->delete();

        return response()->json(['success' => true, 'message' => 'Informasi berhasil dihapus']);
    }
}
