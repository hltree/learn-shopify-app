<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Options extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getAccessToken() :string
    {
        $accessToken = '';

        $where = $this::whereNotNull('access_token');
        if ($where->exists()) {
            $takeOne = $where->get()->take(1)[0];
            $accessToken = $takeOne->getAttribute('access_token');
        }

        return $accessToken;
    }
}
