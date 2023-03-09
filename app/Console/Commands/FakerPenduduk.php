<?php

/*
 *
 * File ini bagian dari:
 *
 * OpenSID
 *
 * Sistem informasi desa sumber terbuka untuk memajukan desa
 *
 * Aplikasi dan source code ini dirilis berdasarkan lisensi GPL V3
 *
 * Hak Cipta 2009 - 2015 Combine Resource Institution (http://lumbungkomunitas.net/)
 * Hak Cipta 2016 - 2023 Perkumpulan Desa Digital Terbuka (https://opendesa.id)
 *
 * Dengan ini diberikan izin, secara gratis, kepada siapa pun yang mendapatkan salinan
 * dari perangkat lunak ini dan file dokumentasi terkait ("Aplikasi Ini"), untuk diperlakukan
 * tanpa batasan, termasuk hak untuk menggunakan, menyalin, mengubah dan/atau mendistribusikan,
 * asal tunduk pada syarat berikut:
 *
 * Pemberitahuan hak cipta di atas dan pemberitahuan izin ini harus disertakan dalam
 * setiap salinan atau bagian penting Aplikasi Ini. Barang siapa yang menghapus atau menghilangkan
 * pemberitahuan ini melanggar ketentuan lisensi Aplikasi Ini.
 *
 * PERANGKAT LUNAK INI DISEDIAKAN "SEBAGAIMANA ADANYA", TANPA JAMINAN APA PUN, BAIK TERSURAT MAUPUN
 * TERSIRAT. PENULIS ATAU PEMEGANG HAK CIPTA SAMA SEKALI TIDAK BERTANGGUNG JAWAB ATAS KLAIM, KERUSAKAN ATAU
 * KEWAJIBAN APAPUN ATAS PENGGUNAAN ATAU LAINNYA TERKAIT APLIKASI INI.
 *
 * @package   OpenSID
 * @author    Tim Pengembang OpenDesa
 * @copyright Hak Cipta 2009 - 2015 Combine Resource Institution (http://lumbungkomunitas.net/)
 * @copyright Hak Cipta 2016 - 2023 Perkumpulan Desa Digital Terbuka (https://opendesa.id)
 * @license   http://www.gnu.org/licenses/gpl.html GPL V3
 * @link      https://github.com/OpenSID/OpenSID
 *
 */

namespace App\Console\Commands;

use App\Models\Config;
use App\Models\Keluarga;
use App\Models\LogPenduduk;
use App\Models\Penduduk;
use App\Models\Wilayah;
use Exception;
use Faker\Factory as Faker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FakerPenduduk extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'faker:penduduk {--retry=10}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate data penduduk';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        set_time_limit(0);

        $retry = (int) $this->option('retry');
        $retry = ($retry < 10) ? $retry : 10;

        $faker = Faker::create('id_ID');
        $dir_penduduk = base_path('desa/upload/user_pict');
        $create_data = 0;

        retry($retry, function () use ($faker, $dir_penduduk, $create_data) {
            $faker = Faker::create('id_ID');
            $dir_penduduk = base_path('desa/upload/user_pict');

            for ($i = 0; $i < 1000; $i++) {
                Config::insert([
                    'app_key' => $faker->uuid(),
                    'nama_desa' => $faker->unique()->streetName,
                    'kode_desa' => $faker->unique()->numerify('18120#####'),
                    'kode_pos' => $faker->unique()->numerify('345##'),
                    'nama_kecamatan' => 'Pagar Dewa',
                    'kode_kecamatan' => '181208',
                    'nama_kepala_camat' => $faker->unique()->name(),
                    'nip_kepala_camat' =>  $faker->unique()->numerify('19580818 198### # ###'),
                    'nama_kabupaten' => 'Tulang Bawang Barat',
                    'kode_kabupaten' => '1812',
                    'nama_propinsi' => 'Lampung',
                    'kode_propinsi' => 18,
                    'alamat_kantor' => $faker->unique()->address,
                ]);

                $config_id = DB::getPdo()->lastInsertId();

                $dusun = $faker->unique()->streetName;
                $lat = $faker->latitude();
                $lng = $faker->longitude();

                Wilayah::insert([
                    [
                        'rt' => 0,
                        'rw' => 0,
                        'dusun' => $dusun,
                        'lat' => $lat,
                        'lng' => $lng,
                        'urut_cetak' => ($i * 3),
                        'config_id' => $config_id,
                    ],
                    [
                        'rt' => 0,
                        'rw' => '-',
                        'dusun' => $dusun,
                        'lat' => $lat,
                        'lng' => $lng,
                        'urut_cetak' => ($i * 3) + 1,
                        'config_id' => $config_id,
                    ],
                    [
                        'rt' => '-',
                        'rw' => '-',
                        'dusun' => $dusun,
                        'lat' => $lat,
                        'lng' => $lng,
                        'urut_cetak' => ($i * 3) + 2,
                        'config_id' => $config_id,
                    ]
                ]);

                $id_wilayah = DB::getPdo()->lastInsertId() + 2;

                echo '- Dusun ', $dusun, ' berhasil dibuat', PHP_EOL;

                $jmlh_kk = $faker->numberBetween(10, 20);

                for ($k = 0; $k < $jmlh_kk; $k++) {
                    // create keluarga
                    $no_kk = $faker->unique()->numerify('34##############');
                    $nik_kepala = $faker->unique()->numerify('351#############');
                    $tgl_daftar = $faker->date('Y-m-d', '-8 years');
                    $tgl_daftar = ($tgl_daftar == '1970-01-01') ? '1980-01-15' : $tgl_daftar;
                    $alamat = $faker->address;

                    Keluarga::insert([
                        'no_kk' => $no_kk,
                        'nik_kepala' => $nik_kepala,
                        'tgl_daftar' => $tgl_daftar,
                        'alamat' => $alamat,
                        'id_cluster' => $id_wilayah,
                        'updated_by' => 1,
                        'config_id' => $config_id,
                    ]);

                    $id_kk = DB::getPdo()->lastInsertId();
                    // buat penduduk kepala keluarga
                    // $arr_foto = explode('\\',$faker->image($dir_penduduk, 640, 880));
                    $tanggal_lahir = $faker->date('Y-m-d', '-18 years');
                    $tanggal_lahir = ($tanggal_lahir == '1970-01-01') ? '1980-01-15' : $tanggal_lahir;

                    Penduduk::insert([
                        'nama' => $faker->name,
                        'nik' => $nik_kepala,
                        'id_kk' =>  $id_kk,
                        'kk_level' => 1,
                        'sex' => $faker->numberBetween(1, 2),
                        'tempatlahir' => $faker->city,
                        'tanggallahir' => $tanggal_lahir,
                        'agama_id' => $faker->numberBetween(1, 7),
                        'pendidikan_kk_id' => $faker->numberBetween(1, 10),
                        'pendidikan_sedang_id' => $faker->numberBetween(1, 18),
                        'pekerjaan_id' => $faker->numberBetween(1, 89),
                        'status_kawin' => $faker->numberBetween(1, 2),
                        'warganegara_id' => 1,
                        'ayah_nik' => $faker->numerify('35##############'),
                        'ibu_nik' => $faker->numerify('35##############'),
                        'nama_ayah' => $faker->name('male'),
                        'nama_ibu' => $faker->name('female'),
                        'foto' => '',
                        'golongan_darah_id' => $faker->numberBetween(1, 13),
                        'id_cluster' => $id_wilayah,
                        'status' => 1,
                        'alamat_sebelumnya' => $faker->address,
                        'alamat_sekarang' => $alamat,
                        'status_dasar' => 1,
                        'cacat_id' => 7,
                        'sakit_menahun_id' => 14,
                        'akta_lahir' => $faker->numerify('######/SKK/R.S.U.S/IV/####'),
                        'ktp_el' => 2,
                        'status_rekam' => 8,
                        'created_by' => 1,
                        'hubung_warga' => ' Telegram',
                        'config_id' => $config_id,
                    ]);

                    $id_penduduk = DB::getPdo()->lastInsertId();
                    $tgl_lapor = $faker->date('Y-m-d');
                    $tgl_lapor = ($tgl_lapor == '1970-01-01') ? '1980-01-15' : $tgl_lapor;

                    LogPenduduk::insert([
                        'id_pend' => $id_penduduk,
                        'kode_peristiwa' => 1,
                        'tgl_lapor' =>  $tgl_lapor,
                        'config_id' =>  $config_id,
                    ]);

                    $kk = Keluarga::find($id_kk);
                    $kk->nik_kepala =  $id_penduduk;
                    $kk->save();

                    echo '- Keluarga no ' . $no_kk . ' berhasil dibuat', PHP_EOL;

                    $create_data++;
                    $jmlh_anggota = $faker->numberBetween(2, 7);

                    $arr_foto = [];

                    for ($j = 0; $j < $jmlh_anggota; $j++) {
                        try {
                            $arr_foto = explode('\\', $faker->image($dir_penduduk, 640, 880));
                        } catch (Exception $e) {
                            $arr_foto = null;
                        }

                        $tanggal_lahir = $faker->date('Y-m-d', '-18 years');
                        $tanggal_lahir = ($tanggal_lahir == '1970-01-01') ? '1980-01-15' : $tanggal_lahir;

                        Penduduk::insert([
                            'nama' => $faker->name,
                            'nik' => $faker->unique()->numerify('352#############'),
                            'id_kk' =>  $id_kk,
                            'kk_level' =>  $faker->numberBetween(2, 11),
                            'sex' => $faker->numberBetween(1, 2),
                            'tempatlahir' => $faker->city,
                            'tanggallahir' => $tanggal_lahir,
                            'agama_id' => $faker->numberBetween(1, 7),
                            'pendidikan_kk_id' => $faker->numberBetween(1, 10),
                            'pendidikan_sedang_id' => $faker->numberBetween(1, 18),
                            'pekerjaan_id' => $faker->numberBetween(1, 89),
                            'status_kawin' => $faker->numberBetween(1, 2),
                            'warganegara_id' => 1,
                            'ayah_nik' => $faker->numerify('35##############'),
                            'ibu_nik' => $faker->numerify('35##############'),
                            'nama_ayah' => $faker->name('male'),
                            'nama_ibu' => $faker->name('female'),
                            'foto' => ($arr_foto == null) ? '' : end($arr_foto),
                            'golongan_darah_id' => $faker->numberBetween(1, 13),
                            'id_cluster' => $id_wilayah,
                            'status' => 1,
                            'alamat_sebelumnya' => $faker->address,
                            'alamat_sekarang' => $alamat,
                            'status_dasar' => 1,
                            'cacat_id' => 7,
                            'sakit_menahun_id' => 14,
                            'akta_lahir' => $faker->numerify('######/SKK/R.S.U.S/IV/####'),
                            'ktp_el' => 2,
                            'status_rekam' => 8,
                            'created_by' => 1,
                            'hubung_warga' => ' Telegram',
                            'config_id' => $config_id,
                        ]);

                        $id_penduduk = DB::getPdo()->lastInsertId();
                        $tgl_lapor = $faker->date('Y-m-d');
                        $tgl_lapor = ($tgl_lapor == '1970-01-01') ? '1980-01-15' : $tgl_lapor;

                        LogPenduduk::insert([
                            'id_pend' => $id_penduduk,
                            'kode_peristiwa' => 1,
                            'tgl_lapor' =>  $tgl_lapor,
                            'config_id' => $config_id,
                        ]);


                        $create_data++;
                    }

                    echo '- berhasil membuat ', $jmlh_anggota, ' anggota keluarga ', PHP_EOL;
                }

                if ($create_data > 1000000) {
                    echo '- generate data selesai', PHP_EOL;
                    return;
                }
            }
        }, 1000);
    }
}
