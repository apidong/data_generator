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

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;



class Artikel extends BaseModel
{
    public const ENABLE         = 1;
    public const HEADLINE       = 1;
    public const NOT_IN_ARTIKEL = [999, 1000, 1001];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'artikel';

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'author',
        'category',
        'comments',
    ];

    /**
     * Scope a query to only include article.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeOnlyArticle($query)
    {
        return $query->whereNotIn('id_kategori', static::NOT_IN_ARTIKEL);
    }

    /**
     * Scope a query to only enable article.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeEnable($query)
    {
        return $query->where('enabled', static::ENABLE);
    }

    /**
     * Scope a query to only headline article.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeHeadline($query)
    {
        return $query->where('headline', static::HEADLINE);
    }

    /**
     * Scope a query to only archive article.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeArsip($query)
    {
        $kategori = json_decode(preg_replace('/\\\\/', '', setting('anjungan_artikel')));

        $artikel = $query->select(Artikel::raw('*, YEAR(tgl_upload) AS thn, MONTH(tgl_upload) AS bln, DAY(tgl_upload) AS hri'))
            ->where([['enabled', 1], ['tgl_upload', '<', date('Y-m-d H:i:s')]]);

        if (null !== $kategori) {
            return $artikel->whereIn('id_kategori', $kategori);
        }

        return $artikel;
    }

    /**
     * Define an inverse one-to-one or many relationship.
     *
     * @return BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Define an inverse one-to-one or many relationship.
     *
     * @return BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }

    /**
     * Define a one-to-many relationship.
     *
     * @return HasMany
     */
    public function comments()
    {
        return $this->hasMany(Komentar::class, 'id_artikel');
    }

    public function getPerkiraanMembacaAttribute()
    {
        return Str::perkiraanMembaca($this->isi);
    }

    /**
     * Getter untuk menambahkan url gambar.
     *
     * @return string
     */
    public function getUrlGambarAttribute()
    {
        // return $this->gambar
        //     ? config('filesystems.disks.ftp.url') . "/desa/upload/artikel/sedang_{$this->gambar}"
        //     : '';
    }

    /**
     * Getter untuk menambahkan url gambar.
     *
     * @return string
     */
    public function getUrlGambar1Attribute()
    {
        // return $this->gambar1
        //     ? config('filesystems.disks.ftp.url') . "/desa/upload/artikel/sedang_{$this->gambar1}"
        //     : '';
    }

    /**
     * Getter untuk menambahkan url gambar.
     *
     * @return string
     */
    public function getUrlGambar2Attribute()
    {
        // return $this->gambar2
        //     ? config('filesystems.disks.ftp.url') . "/desa/upload/artikel/sedang_{$this->gambar2}"
        //     : '';
    }

    /**
     * Getter untuk menambahkan url gambar.
     *
     * @return string
     */
    public function getUrlGambar3Attribute()
    {
        // return $this->gambar3
        //     ? config('filesystems.disks.ftp.url') . "/desa/upload/artikel/sedang_{$this->gambar3}"
        //     : '';
    }
}
