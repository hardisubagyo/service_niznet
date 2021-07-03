<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Produk;
use App\MasterTrx;
use App\MasterKurir;
use App\TrCheckout;
use Auth;
use Validator;

class TrackingController extends Controller
{
  public function getList(Request $request) {
    if($request->isMethod('GET')) {
      $masterKurir = MasterKurir::where('id_users',Auth::user()->id)->first();
      $result = [];
      $data = $request->all();
      $whereField = '';
      $whereValue = null;
        if(isset($request->status) && $request->status != 'null' ) {
          $whereValue = $data['status'];
      }
      $kurirTrxList = MasterTrx::where(function($query) use($whereField, $whereValue, $data) {
                          if($whereValue != null) {
                            $query->where('status', $data['status']);
                          }
                        })
                      ->where('id_kurir', $masterKurir->id_users)
                      ->orderBy('updated_at', 'DESC')
                      ->paginate($request->limit);
                    //   dd($whereValue);
      foreach($kurirTrxList->items() as $row) {
        $result[] = $row;
      }

      $meta = [
        'code' => 200,
        'message' => 'Success',
        'status' => 'OK'
      ];

      $data = [
        'page' => $kurirTrxList->currentPage(),
        'total_results' => $kurirTrxList->total(),
        'total_pages' => $kurirTrxList->total(),
        'results' => $result
      ];

      $res['meta'] = $meta;
      $res['data'] = $data;

      return response()->json($res
      , 200);
      
    } else {
      return response()->json([
        'status' => false,
        'message' => "<strong>failed') !</strong> method_not_allowed"
      ], 405);
    }
  }
  
  public function getListDetail(Request $request) {
    if($request->isMethod('GET')) {
        $result = [];
      $data = $request->all();
      $whereField = '';
      $whereValue = (isset($data['where_value'])) ? $data['where_value'] : '';
      $kurirTrxList = MasterTrx::join('users', 'users.id', 'master_trx.users_id')
                      ->leftJoin('master_toko','master_trx.id_toko','master_toko.id')
                      ->leftJoin('users as userToko', 'master_toko.id_user','userToko.id')
                      ->leftJoin('master_payment_method', 'master_payment_method.id','master_trx.id_payment_method')
                      ->where(function($query) use($whereField, $whereValue) {
                        if($whereValue) {
                          foreach(explode(', ', $whereField) as $idx => $field) {
                            $query->orWhere($field, 'LIKE', "%".$whereValue."%");
                          }
                        }
                      })
                      ->select('master_trx.*','users.name', 'users.telp', 'userToko.name as address', 'master_payment_method.nama as paymentMethod')
                      ->find($request->id);
      $trCheckout = TrCheckout::join('master_produk', 'master_produk.product_code', 'tr_checkout.product_code')->where('checkout_code', $kurirTrxList->checkout_code)
      ->select('master_produk.product_name', 'master_produk.image1', 'tr_checkout.price', 'tr_checkout.quantity','tr_checkout.variasi')->get();
      $kurirTrxList['product'] = $trCheckout;
      $kurirTrxList->photo_payment = ($kurirTrxList->photo_payment) ? url('uploads/struk/'.$kurirTrxList->photo_payment) :url('uploads/produk/nia3.png');
       foreach($kurirTrxList['product'] as $row) {
        $row->image1 = ( $row->image1) ? url('uploads/produk/'. $row->image1) :url('uploads/produk/nia3.png');
      }
      
       
      
            // $kurirTrxList->image1 = ($kurirTrxList->image1) ? url('uploads/produk/'.$kurirTrxList->image1) :url('uploads/produk/nia3.png');
            // $kurirTrxList->image2 = ($kurirTrxList->image2) ? url('uploads/produk/'.$kurirTrxList->image2) :url('uploads/produk/nia3.png');
            // $kurirTrxList->image3 = ($kurirTrxList->image3) ? url('uploads/produk/'.$kurirTrxList->image3) :url('uploads/produk/nia3.png');
            // $kurirTrxList->image4 = ($kurirTrxList->image4) ? url('uploads/produk/'.$kurirTrxList->image4) :url('uploads/produk/nia3.png');
            // $kurirTrxList->image5 = ($kurirTrxList->image5) ? url('uploads/produk/'.$kurirTrxList->image5) :url('uploads/produk/nia3.png');
    
      $meta = [
        'code' => 200,
        'message' => 'Success',
        'status' => 'OK'
      ];
        
      $res['meta'] = $meta;
      $res['data'] = $kurirTrxList;

      return response()->json($res
      , 200);
      
    } else {
      return response()->json([
        'status' => false,
        'message' => "<strong>failed') !</strong> method_not_allowed"
      ], 405);
    }
  }
  
  public function updateStatus(Request $request) {
      if($request->isMethod('POST')) {
          $data = $request->all();
          $masterTrx = MasterTrx::find($request->id_master_trx);
          
              $masterTrx->status = $data['status']; 
              $masterTrx->save();
         
           $meta = [
          'code' => 200,
          'message' => 'Success',
          'status' => 'Ok'
        ];
        return response()->json($meta
        , 200);
      } else {
        $meta = [
          'code' => 405,
          'message' => 'Fail',
          'status' => 'Fail'
        ];
        return response()->json($meta
        , 405);
    }
  }
  
  public function uploadImg(Request $request) {
    if($request->isMethod('POST')) {
      $data = $request->all();
      $img = $request->file('images');
      $mstTrx = MasterTrx::find($request->id);
      $fileExt = $img->extension();
      $fileName = "IMG-TRACKING-".$mstTrx->checkout_code."-".$request->id.".".$fileExt;
      $path =  public_path().'/uploads/struk/' ;
      $mstTrx->photo_payment = $fileName;
      $mstTrx->status = 2;
      $img->move($path, $fileName);

      if($mstTrx->save()) {
        $meta = [
          'code' => 200,
          'message' => 'Success',
          'status' => 'OK'
        ];
        return response()->json($meta
        , 200);
      
      }else {
        $meta = [
          'code' => 405,
          'message' => 'Fail',
          'status' => 'Fail'
        ];
        return response()->json($meta
        , 405);
      }
    } else {
        $meta = [
          'code' => 405,
          'message' => 'Fail',
          'status' => 'Fail'
        ];
        return response()->json($meta
        , 405);
    }
  }
}
