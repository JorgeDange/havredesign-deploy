<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attachment extends Model
{
    use HasUuid;

    /** A tabela só tem `created_at`. */
    public const UPDATED_AT = null;

    /** @var list<string> */
    protected $fillable = [
        'request_id',
        'original_name',
        'path',
        'mime_type',
        'size_bytes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'size_bytes' => 'integer',
        ];
    }

    public function projectRequest(): BelongsTo
    {
        return $this->belongsTo(ProjectRequest::class, 'request_id');
    }

    /**
     * O browser consegue mostrar o ficheiro em separador novo (PDF/imagem)?
     * Os restantes (DWG, ZIP, …) só fazem sentido como download.
     */
    public function visualizavel(): bool
    {
        $extensao = $this->extensao();

        return $this->ehImagem() || $extensao === 'pdf';
    }

    /** Formato de imagem que a modal mostra num <img>. */
    public function ehImagem(): bool
    {
        return in_array($this->extensao(), ['jpg', 'jpeg', 'png', 'webp'], true);
    }

    private function extensao(): string
    {
        return strtolower(pathinfo((string) $this->path, PATHINFO_EXTENSION));
    }
}
