<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\KaryawanService;
use App\Models\Karyawan;
use App\Models\Keluarga;
use App\Models\AyahIbu;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Controller for karyawan management.
 */
class KaryawanController extends Controller
{
    protected $service;

    public function __construct(KaryawanService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the karyawan.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $status = $request->input('status');

        $jabatanId = $request->input('jabatan_id');
        $penempatanId = $request->input('penempatan_id');
        $jenisKelamin = $request->input('jenis_kelamin');

        $query = Karyawan::with(['jabatan', 'formasi', 'penempatan', 'unitKerja'])
            ->latest('id');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'ilike', '%' . $search . '%')
                  ->orWhere('nik', 'like', '%' . $search . '%');
            });
        }
        
        if ($status) {
            $query->where('status', 'ilike', $status);
        }

        if ($jabatanId) {
            $query->where('jabatan_id', $jabatanId);
        }

        if ($penempatanId) {
            $query->where('penempatan_id', $penempatanId);
        }

        if ($jenisKelamin) {
            $query->where('jenis_kelamin', $jenisKelamin);
        }

        $karyawan = $query->paginate($perPage);

        return response()->json($karyawan);
    }

    /**
     * Store a newly created karyawan.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'tanggal_masuk' => 'required|date',
            'tanggal_aktif' => 'required|date',
            'control_status' => 'required|in:KONTRAK,HARIAN',
            'status' => 'required|in:AKTIF,RESIGN',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'required|date',
            'agama' => 'nullable|string|max:50',
            'golongan_darah' => 'nullable|string|max:3',
            'no_telepon' => 'nullable|string|max:20',
            'no_wa' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'alamat' => 'nullable|string',
            'rt_rw' => 'nullable|string|max:10',
            'kelurahan' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kabupaten_kota' => 'nullable|string|max:255',
            'kode_pos' => 'nullable|string|max:10',
            'no_ktp' => 'nullable|string|max:20',
            'no_kk' => 'nullable|string|max:20',
            'no_bpjs_kesehatan' => 'nullable|string|max:20',
            'npp_bpjs_kesehatan' => 'nullable|string|max:20',
            'bu_bpjs_kesehatan' => 'nullable|string|max:20',
            'no_bpjs_ketenagakerjaan' => 'nullable|string|max:20',
            'npp_bpjs_ketenagakerjaan' => 'nullable|string|max:20',
            'no_npwp' => 'nullable|string|max:20',
            'status_pernikahan' => 'nullable|in:MENIKAH,BELUM_MENIKAH,CERAI',
            'jumlah_anak' => 'nullable|integer|min:0',
            'pendidikan_terakhir' => 'nullable|string|max:100',
            'nama_institusi' => 'nullable|string|max:255',
            'tahun_lulus' => 'nullable|integer|min:1900|max:' . date('Y'),
            'no_rekening' => 'nullable|string|max:50',
            'nama_rekening' => 'nullable|string|max:255',
            'bank_id' => 'nullable|exists:bank,id',
            'gaji_pokok' => 'nullable|numeric|min:0',
            'insentif' => 'nullable|numeric|min:0',
            'uang_makan' => 'nullable|numeric|min:0',
            'potongan' => 'nullable|numeric|min:0',
            'formasi_id' => 'nullable|exists:formasi,id',
            'jabatan_id' => 'nullable|exists:jabatan,id',
            'unit_kerja_id' => 'required|exists:unit_kerja,id',
            'penempatan_id' => 'nullable|exists:project,id',
            'awal_mulai_cuti' => 'nullable|date',
            'masa_berlaku_cuti' => 'nullable|date',
            'potongan_cuti_bersama' => 'nullable|integer|min:0',
            'sisa_cuti' => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string',
        ], $this->getValidationMessages());

        $karyawan = $this->service->create($validated);

        return response()->json($karyawan, 201);
    }

    /**
     * Display the specified karyawan detail.
     *
     * @param Karyawan $karyawan
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Karyawan $karyawan): \Illuminate\Http\JsonResponse
    {
        $karyawan->load(['user', 'bank', 'formasi', 'jabatan', 'unitKerja', 'penempatan', 'keluarga', 'ayahIbu']);
        
        return response()->json($karyawan);
    }

    /**
     * Update the specified section of karyawan.
     *
     * @param Request $request
     * @param Karyawan $karyawan
     * @param string $section
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSection(Request $request, Karyawan $karyawan, string $section): \Illuminate\Http\JsonResponse
    {
        $rules = $this->getValidationRules($section, $karyawan);
        $validated = $request->validate($rules, $this->getValidationMessages());

        if ($section === 'informasi_keluarga') {
            $this->service->updateKeluarga($karyawan, $validated);
        } else {
            $this->service->update($karyawan, $validated);
        }

        return response()->json($karyawan);
    }

    /**
     * Remove the specified karyawan (soft delete).
     *
     * @param Karyawan $karyawan
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Karyawan $karyawan): \Illuminate\Http\JsonResponse
    {
        $this->service->delete($karyawan);

        return response()->json(['message' => 'Delete Karyawan successful']);
    }

    /**
     * Reset password for karyawan.
     *
     * @param Karyawan $karyawan
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Karyawan $karyawan): \Illuminate\Http\JsonResponse
    {
        $this->service->resetPassword($karyawan);

        return response()->json(['message' => 'Password reset successful']);
    }

    private function getValidationRules(string $section, ?Karyawan $karyawan = null): array
    {
        $uniqueNikRule = 'string|unique:karyawan,nik';
        if ($karyawan) {
            $uniqueNikRule .= ',' . $karyawan->id;
        }

        $rules = [
            'data_pribadi' => [
                'nik' => $uniqueNikRule,
                'nama' => 'string|max:255',
                'jenis_kelamin' => 'in:L,P',
                'agama' => 'string|max:50',
                'tempat_lahir' => 'string|max:255',
                'tanggal_lahir' => 'date',
                'pendidikan_terakhir' => 'string|max:100|nullable',
                'golongan_darah' => 'string|max:3|nullable',
                'no_telepon' => 'string|max:20|nullable',
                'email' => 'email|max:255|nullable',
                'status_pernikahan' => 'in:MENIKAH,BELUM_MENIKAH,CERAI|nullable',
            ],
            'informasi_pekerjaan' => [
                'jabatan_id' => 'exists:jabatan,id',
                'formasi_id' => 'exists:formasi,id',
                'penempatan_id' => 'exists:project,id|nullable',
                'unit_kerja_id' => 'exists:unit_kerja,id',
                'bu' => 'string|max:50|nullable', // Added Business Unit
                'tanggal_masuk' => 'date',
                'tanggal_aktif' => 'date',
                'tanggal_resign' => 'date|nullable',
                'control_status' => 'in:KONTRAK,HARIAN',
                'status' => 'in:AKTIF,RESIGN',
                'sisa_cuti' => 'integer|min:0',
                'keterangan' => 'string|nullable',
            ],
            'kompensasi' => [
                'gaji_pokok' => 'numeric|min:0|nullable',
                'insentif' => 'numeric|min:0|nullable',
                'uang_makan' => 'numeric|min:0|nullable',
                'bank_id' => 'exists:bank,id|nullable',
                'no_rekening' => 'string|max:50|nullable',
                'no_jaminan_pensiun' => 'string|max:50|nullable', // Added Jaminan Pensiun
            ],
            'dokumen_identitas' => [
                'no_kk' => 'string|max:20|nullable',
                'no_bpjs_ketenagakerjaan' => 'string|max:20|nullable',
                'npp_bpjs_ketenagakerjaan' => 'string|max:20|nullable',
                'no_bpjs_kesehatan' => 'string|max:20|nullable',
                'bu_bpjs_kesehatan' => 'string|max:20|nullable',
                'no_ktp' => 'string|max:20|nullable', 
            ],
            'alamat' => [
                'alamat' => 'string|nullable',
                'rt' => 'string|max:3|nullable',
                'rw' => 'string|max:3|nullable',
                'kelurahan' => 'string|max:255|nullable',
                'kecamatan' => 'string|max:255|nullable',
                'kabupaten_kota' => 'string|max:255|nullable',
                'kode_pos' => 'string|max:10|nullable',
            ],
            'informasi_keluarga' => [
                'istri_suami' => 'array|nullable',
                'istri_suami.id' => 'integer|exists:keluarga,id|nullable',
                'istri_suami.hubungan' => 'required_with:istri_suami|in:ISTRI,SUAMI',
                'istri_suami.nama' => 'required_with:istri_suami|string|max:255',
                'istri_suami.no_ktp' => 'nullable|string|max:20',
                'istri_suami.tempat_lahir' => 'nullable|string|max:255',
                'istri_suami.tanggal_lahir' => 'nullable|date',
                'istri_suami.bpjs_kesehatan' => 'nullable|string|max:20',
                'anak' => 'array|nullable',
                'anak.*.id' => 'integer|exists:keluarga,id|nullable',
                'anak.*.hubungan' => 'required_with:anak.*|in:ANAK',
                'anak.*.nama' => 'required_with:anak.*|string|max:255',
                'anak.*.no_ktp' => 'nullable|string|max:20',
                'anak.*.tempat_lahir' => 'nullable|string|max:255',
                'anak.*.tanggal_lahir' => 'nullable|date',
                'anak.*.bpjs_kesehatan' => 'nullable|string|max:20',
                'anak.*.urutan_anak' => 'required_with:anak.*|integer|min:1',
                'orang_tua' => 'array|nullable',
                'orang_tua.id' => 'integer|exists:ayah_ibu,id|nullable',
                'orang_tua.nama_ayah' => 'string|max:255',
                'orang_tua.nama_ibu' => 'string|max:255',
            ],
        ];

        return $rules[$section] ?? throw ValidationException::withMessages(['section' => 'Invalid section']);
    }
    private function getValidationMessages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'unique' => ':attribute sudah digunakan.',
            'in' => ':attribute tidak valid.',
            'exists' => ':attribute tidak ditemukan.',
            'email' => 'Format email tidak valid.',
            'integer' => ':attribute harus berupa angka.',
            'date' => ':attribute format tanggal salah.',
            'numeric' => ':attribute harus berupa angka.',
        ];
    }
}
