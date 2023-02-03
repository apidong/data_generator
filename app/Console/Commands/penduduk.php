<?php

namespace App\Console\Commands;

use App\Models\Wilayah;
use App\Models\Keluarga;
use App\Models\Penduduk as modelPenduduk;
use Faker\Factory as Faker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;



class penduduk extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'faker:penduduk';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'generate data penduduk';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $kd_wilayah = Faker::create('id_ID');
        $dir_penduduk = 'D:\kerjoan\web\opendesa\premium\desa\upload\user_pict';

        for ($i=0; $i < 30; $i++) {
            $dusun = $kd_wilayah->unique()->streetName;
            $lat = $kd_wilayah->latitude();
            $lng = $kd_wilayah->longitude();

            $wilayah = Wilayah::insert([
                [
                    'rt' => 0,
                    'rw'=> 0,
                    'dusun' => $dusun,
                    'lat' => $lat,
                    'lng' => $lng,
                    'urut_cetak' =>($i *3)
                ],
                [
                    'rt' => 0,
                    'rw'=> '-',
                    'dusun' => $dusun,
                    'lat' => $lat,
                    'lng' => $lng,
                    'urut_cetak' =>($i *3) +1
                ],
                [
                    'rt' => '-',
                    'rw'=> '-',
                    'dusun' => $dusun,
                    'lat' => $lat,
                    'lng' => $lng,
                    'urut_cetak' =>($i *3) + 2
                ]
                ]
            );
            $id_wilayah = DB::getPdo()->lastInsertId() + 2;

            // create keluarga
            $fake_keluarga = Faker::create('id_ID');
            $no_kk = $fake_keluarga->unique()->numerify('34##############');
            $nik_kepala = $fake_keluarga->unique()->numerify('351#############');
            $tgl_daftar = $fake_keluarga->date('Y-m-d', '-8 years');
            $alamat = $fake_keluarga->address;

            $keluarga = Keluarga::insert(
                [
                    'no_kk' => $no_kk,
                    'nik_kepala' => $nik_kepala,
                    'tgl_daftar' => $tgl_daftar,
                    'alamat' => $alamat,
                    'id_cluster' => $id_wilayah,
                    'updated_by' => 1
                ]

            );
            $id_kk = DB::getPdo()->lastInsertId();
            // buat penduduk kepala keluarga
            $arr_foto = explode('\\',$fake_keluarga->image($dir_penduduk, 640, 880));
            modelPenduduk::insert(
                [
                    'nama' => $fake_keluarga->name,
                    'nik' => $nik_kepala,
                    'id_kk' =>  $id_kk,
                    'kk_level' => 1,
                    'sex' => $fake_keluarga->numberBetween(1,2),
                    'tempatlahir' => $fake_keluarga->city,
                    'tanggallahir' => $fake_keluarga->date('Y-m-d', '-18 years'),
                    'agama_id' => $fake_keluarga->numberBetween(1,7),
                    'pendidikan_kk_id' => $fake_keluarga->numberBetween(1,10),
                    'pendidikan_sedang_id' => $fake_keluarga->numberBetween(1,18),
                    'pekerjaan_id' => $fake_keluarga->numberBetween(1,89),
                    'status_kawin' => $fake_keluarga->numberBetween(1,2),
                    'warganegara_id' => 1,
                    'ayah_nik' => $fake_keluarga->numerify('35##############'),
                    'ibu_nik' => $fake_keluarga->numerify('35##############'),
                    'nama_ayah' => $fake_keluarga->name('male'),
                    'nama_ibu' => $fake_keluarga->name('female'),
                    'foto' => end($arr_foto),
                    'golongan_darah_id' => $fake_keluarga->numberBetween(1,13),
                    'id_cluster' => $id_wilayah,
                    'status' => 1,
                    'alamat_sebelumnya' => $fake_keluarga->address,
                    'alamat_sekarang' => $alamat,
                    'status_dasar' => 1,
                    'cacat_id' => 7,
                    'sakit_menahun_id' => 14,
                    'akta_lahir' => $fake_keluarga->numerify('######/SKK/R.S.U.S/IV/####'),
                    'ktp_el' => 2,
                    'status_rekam' => 8,
                    'created_by' => 1,
                    'hubung_warga' =>' Telegram'
                ]
            );
            $jmlh_anggota = $fake_keluarga->numberBetween(2,7);
            for ($j=0; $j < $jmlh_anggota; $j++) {
                modelPenduduk::insert(
                    [
                        'nama' => $fake_keluarga->name,
                        'nik' => $nik_kepala,
                        'id_kk' =>  $id_kk,
                        'kk_level' => 1,
                        'sex' => $fake_keluarga->numberBetween(1,2),
                        'tempatlahir' => $fake_keluarga->city,
                        'tanggallahir' => $fake_keluarga->date('Y-m-d', '-18 years'),
                        'agama_id' => $fake_keluarga->numberBetween(1,7),
                        'pendidikan_kk_id' => $fake_keluarga->numberBetween(1,10),
                        'pendidikan_sedang_id' => $fake_keluarga->numberBetween(1,18),
                        'pekerjaan_id' => $fake_keluarga->numberBetween(1,89),
                        'status_kawin' => $fake_keluarga->numberBetween(1,2),
                        'warganegara_id' => 1,
                        'ayah_nik' => $fake_keluarga->numerify('35##############'),
                        'ibu_nik' => $fake_keluarga->numerify('35##############'),
                        'nama_ayah' => $fake_keluarga->name('male'),
                        'nama_ibu' => $fake_keluarga->name('female'),
                        'foto' => end($arr_foto),
                        'golongan_darah_id' => $fake_keluarga->numberBetween(1,13),
                        'id_cluster' => $id_wilayah,
                        'status' => 1,
                        'alamat_sebelumnya' => $fake_keluarga->address,
                        'alamat_sekarang' => $alamat,
                        'status_dasar' => 1,
                        'cacat_id' => 7,
                        'sakit_menahun_id' => 14,
                        'akta_lahir' => $fake_keluarga->numerify('######/SKK/R.S.U.S/IV/####'),
                        'ktp_el' => 2,
                        'status_rekam' => 8,
                        'created_by' => 1,
                        'hubung_warga' =>' Telegram'
                    ]
                );
            }

        }
    }
}
