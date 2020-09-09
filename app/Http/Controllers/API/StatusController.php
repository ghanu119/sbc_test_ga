<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\API\StatusCreateRequest;
use App\Http\Requests\API\StatusVIewRequest;
use App\Http\Requests\API\StatusDeleteRequest;
use App\Models\Status;
use Illuminate\Support\Str;

class StatusController extends Controller
{
    /**
     * Get all statuses
     * 
     * @param filter['search_text']
     * 
     */
    public function getAll ( Request $request ) {
        $code = 200;

        $status = new Status;

        if( is_null( auth()->user() ) ){
            $status = $status->publicStatus();
        }else{
            
            $status = $status->where('user_id', auth()->id() );
        }

        if( $request->has('filter') ){

            $status = $status->customSearch( $request->get('filter') );
        }
        $status = $status->active()->simplePaginate( 12 );

        $data = [
            'status' => 'success',
            'code' => $code,
            'message' => 'Status list retrive successfully.',
            'data' => [
                'status' => $status
            ] 
        ];

        return response()->json( $data, $code);
    }

    /**
     * Create Status
     * 
     */
    public function create ( StatusCreateRequest $request  ) {
        $code = 200;
        
        $expireTime = null;
        if( !empty( $request->get('expire_time') ) ){
            $expireTime = strtotime( $request->get('expire_time') );
            $expireTime = date('Y-m-d 00:00:00', $expireTime );


        }

        $status = new Status;
        $status->user_id = auth()->id();
        $status->slug = $this->checkSlug( Str::random( 10 ) );
        $status->text = $request->get('text');
        $status->type = $request->get('type');
        $status->expire_time = $expireTime;
        $status->save();

        $data = [
            'status' => 'success',
            'code' => $code,
            'message' => 'Status created successfully.'
        ];

        return response()->json( $data, $code );
    }

    /**
     * Status view
     * 
     */
    public function view ( StatusVIewRequest $request ) {
        $code = 200 ;

        $status = Status::whereSlug( $request->get('slug') )
                            ->active()
                            ->first();
        if( !is_null( $status ) ){


            $data = [
                'status' => 'success',
                'code' => $code,
                'message' => 'Status found successfully.',
                'data' => [
                    'status' => $status
                ]
            ];
        }else{
            $code = 500;
            $data = [
                'status' => 'error',
                'code' => $code,
                'message' => 'Status not found.',
                'data' => [
                ]
            ];

        }

        return response()->json( $data, $code );
    }

    /**
     * Delete status
     * 
     */
    public function delete ( StatusDeleteRequest $request ) {
        $code = 200 ;

        $status = Status::whereSlug( $request->get('slug') )
                            ->active()
                            ->where('user_id', auth()->id() )
                            ->first();
                            
        if( !is_null( $status ) ){

            $status->delete();
            
            $data = [
                'status' => 'success',
                'code' => $code,
                'message' => 'Status deleted successfully.',
                'data' => [
                ]
            ];
        }else{
            $code = 500;
            $data = [
                'status' => 'error',
                'code' => $code,
                'message' => 'Status not found.',
                'data' => [
                ]
            ];

        }

        return response()->json( $data, $code );
    }

    /**
     * checkSlug
     * 
     */
    private function checkSlug ( $slug ) {
        recheck_slug:
        $status = Status::where('slug', $slug)->first();
        if( !is_null( $status ) ){
            $slug = $slug . uniqid();
            goto recheck_slug;
        }   
        $slug = Str::slug( $slug );
        return $slug;   
    }
}
 