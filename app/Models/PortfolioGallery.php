<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortfolioGallery extends Model
{
    /** Tabela real: `portfolio_gallery` (singular) — não é `portfolio_galleries`. */
    protected $table = 'portfolio_gallery';

    /** A tabela não tem colunas de timestamp. */
    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = [
        'portfolio_id',
        'image_url',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function portfolioItem(): BelongsTo
    {
        return $this->belongsTo(PortfolioItem::class, 'portfolio_id');
    }
}
