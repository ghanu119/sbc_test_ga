<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Status extends Model
{
    use HasFactory;
    
    protected $table = 'status';

    /**
     * Eloquent relationship with User
     * 
     */
    public function user () {
        return $this->belongsTo( '\App\Models\Status', 'user_id', 'id');
    }

    /**
     * Eloquent custom scope function
     * 
     */
    public function scopeCustomSearch ( $q, $search) {
        if( !empty( $search ) ){
            $q->where( function( $whereQry ) use ( $search ) {
                if( !empty( $search['search_text'] ) ){
                    $whereQry->where('text', 'LIKE', '%'. $search['search_text'] . '%');
                }
                if( !empty( $search['search_type'] ) ){
                    $whereQry->where('type', '=', $search['search_type'] );
                }
            });
        }
        return $q;
    }

    /**
     * Eloquent custom scope function 
     * 
     */
    public function scopePublicStatus ( $q ){
        return $q->where('type', 'public');
    }

    /**
     * Eloquent custom scope function 
     * 
     */
    public function scopeActive ( $q ){
        return $q->whereNull('expire_time')
                    ->orWhere( DB::raw( 'DATE(expire_time)'), '>=', date('Y-m-d'));
    }
}
