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
        $karyawan = Karyawan::with(['jabatan', 'formasi', 'penempatan'])
            ->select('id', 'nik', 'nama', 'jabatan_id', 'formasi_id', 'penempatan_id', 'jenis_kelamin', 'sisa_cuti', 'deleted_at')
            ->paginate($perPage);

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
            'status_karyawan' => 'required|in:KONTRAK,TETAP',
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
        ]);

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

        $data = [
            'data_pribadi' => [
                'nik' => $karyawan->nik,
                'nama' => $karyawan->nama,
                'jenis_kelamin' => $karyawan->jenis_kelamin,
                'agama' => $karyawan->agama,
                'tempat_lahir' => $karyawan->tempat_lahir,
                'tanggal_lahir' => $karyawan->tanggal_lahir,
                'umur' => $karyawan->umur,
                'pendidikan_terakhir' => $karyawan->pendidikan_terakhir,
                'golongan_darah' => $karyawan->golongan_darah,
                'no_telepon' => $karyawan->no_telepon,
                'no_wa' => $karyawan->no_wa,
                'email' => $karyawan->email,
                'status_pernikahan' => $karyawan->status_pernikahan,
                'status' => $karyawan->status,
            ],
            'informasi_pekerjaan' => [
                'jabatan' => $karyawan->jabatan ? $karyawan->jabatan->nama_jabatan : null,
                'formasi' => $karyawan->formasi ? $karyawan->formasi->nama_formasi : null,
                'penempatan' => $karyawan->penempatan ? $karyawan->penempatan->nama_project : null,
                'unit_kerja' => $karyawan->unitKerja ? $karyawan->unitKerja->nama_unit : null,
                'tanggal_masuk' => $karyawan->tanggal_masuk,
                'lama_kerja' => $karyawan->lama_kerja,
                'tanggal_aktif' => $karyawan->tanggal_aktif,
                'tanggal_resign' => $karyawan->tanggal_resign,
                'sisa_cuti_tahunan' => $karyawan->sisa_cuti,
                'keterangan' => $karyawan->keterangan,
            ],
            'kompensasi' => [
                'gaji' => $karyawan->gaji_pokok,
                'insentif' => $karyawan->insentif,
                'uang_makan' => $karyawan->uang_makan,
            ],
            'dokumen_identitas' => [
                'no_kk' => $karyawan->no_kk,
                'bpjs_ketenagakerjaan' => $karyawan->no_bpjs_ketenagakerjaan,
                'npp_bpjs_ketenagakerjaan' => $karyawan->npp_bpjs_ketenagakerjaan,
                'bpjs_kesehatan' => $karyawan->no_bpjs_kesehatan,
                'npp_bpjs_kesehatan' => $karyawan->npp_bpjs_kesehatan,
                'bu_bpjs_kesehatan' => $karyawan->bu_bpjs_kesehatan,
            ],
            'alamat' => [
                'alamat_lengkap' => $karyawan->alamat,
                'rt_rw' => $karyawan->rt_rw,
                'kelurahan' => $karyawan->kelurahan,
                'kecamatan' => $karyawan->kecamatan,
                'kab_kota' => $karyawan->kabupaten_kota,
                'kode_pos' => $karyawan->kode_pos,
            ],
            'informasi_keluarga' => [
                'istri_suami' => $karyawan->keluarga->whereIn('hubungan', ['ISTRI', 'SUAMI'])->first(),
                'anak' => $karyawan->keluarga->where('hubungan', 'ANAK'),
                'orang_tua' => $karyawan->ayahIbu,
            ],
            'akses_akun' => [
                'username' => $karyawan->user ? $karyawan->user->username : null,
                'password' => '********',
            ],
        ];

        return response()->json($data);
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
        $rules = $this->getValidationRules($section);
        $validated = $request->validate($rules);

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

    private function getValidationRules(string $section): array
    {
        $rules = [
            'data_pribadi' => [
                'nik' => 'string|unique:karyawan,nik',
                'nama' => 'string|max:255',
                'jenis_kelamin' => 'in:L,P',
                'agama' => 'string|max:50',
                'tempat_lahir' => 'string|max:255',
                'tanggal_lahir' => 'date',
                'pendidikan_terakhir' => 'string|max:100',
                'golongan_darah' => 'string|max:3',
                'no_telepon' => 'string|max:20',
                'no_wa' => 'string|max:20',
                'email' => 'email|max:255',
                'status_pernikahan' => 'in:MENIKAH,BELUM_MENIKAH,CERAI',
            ],
            'informasi_pekerjaan' => [
                'jabatan_id' => 'exists:jabatan,id',
                'formasi_id' => 'exists:formasi,id',
                'penempatan_id' => 'exists:project,id',
                'unit_kerja_id' => 'exists:unit_kerja,id',
                'tanggal_masuk' => 'date',
                'tanggal_aktif' => 'date',
                'tanggal_resign' => 'date|nullable',
                'sisa_cuti' => 'integer|min:0',
                'keterangan' => 'string|nullable',
            ],
            'kompensasi' => [
                'gaji_pokok' => 'numeric|min:0',
                'insentif' => 'numeric|min:0',
                'uang_makan' => 'numeric|min:0',
            ],
            'dokumen_identitas' => [
                'no_kk' => 'string|max:20|nullable',
                'no_bpjs_ketenagakerjaan' => 'string|max:20|nullable',
                'npp_bpjs_ketenagakerjaan' => 'string|max:20|nullable',
                'no_bpjs_kesehatan' => 'string|max:20|nullable',
                'npp_bpjs_kesehatan' => 'string|max:20|nullable',
                'bu_bpjs_kesehatan' => 'string|max:20|nullable',
            ],
            'alamat' => [
                'alamat' => 'string|nullable',
                'rt_rw' => 'string|max:10|nullable',
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
}
