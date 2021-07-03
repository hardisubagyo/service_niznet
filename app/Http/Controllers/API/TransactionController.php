<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Produk;
use App\ProdukToko;
use App\TrCart;
use App\TrCheckout;
use App\MasterTrx;
use App\MasterPaymentMethod;
use App\Bank;
// use App\Notification;
use Auth;
use Validator;
// use App\Services\FirebaseService;

class TransactionController extends Controller
{
  public function getListCart(Request $request) {
    if($request->isMethod('GET')) {
      $result = [];
      $data = $request->all();
      $whereField = '';
      $whereValue = (isset($data['where_value'])) ? $data['where_value'] : '';
      $trCart = TrCart::join('master_produk', 'master_produk.id', 'tr_cart.id_product')
                ->join('master_brand', 'master_brand.id', 'master_produk.brand_code')
                ->join('master_category', 'master_category.id', 'master_produk.category_code')
                ->where(function($query) use($whereField, $whereValue) {
                  if($whereValue) {
                    foreach(explode(', ', $whereField) as $idx => $field) {
                      $query->orWhere($field, 'LIKE', "%".$whereValue."%");
                    }
                  }
                })
                ->select('tr_cart.*', 'master_produk.product_name','master_produk.stock', 'master_produk.product_code',
                    'master_produk.price','master_produk.image1', 'master_brand.brand_name', 'master_category.category_name')
                ->where('id_user', Auth::user()->id)
                ->orderBy('tr_cart.updated_at', 'DESC')
                ->get();

          foreach($trCart as $row) {
            $row->image1 = ($row->image1) ? url('uploads/produk/'.$row->image1) :url('uploads/produk/nia3.png');
            $result[] = $row;
          }
          
          $meta = [
            'code' => 200,
            'message' => 'Success',
            'status' => 'OK'
          ];
    
          $data =  $result;
    
          $res['meta'] = $meta;
          $res['data'] = $data;
    
          return response()->json($res
          , 200);
          
        } else {
           $meta = [
            'code' => 400,
            'message' => 'Failed',
            'status' => 'Fail'
          ];
    
          $data = [
            'results' => null
          ];
    
          $res['meta'] = $meta;
          $res['data'] = $trCart;
    
          return response()->json($res
          , 400);
        }
    
  }

  public function getListMasterTrx(Request $request) {
    if($request->isMethod('GET')) {
      $result = [];
      $data = $request->all();
      $whereField = '';
      $whereValue = null;
      
      if(isset($request->status) && $request->status != 'null' ) {
          $whereValue = $data['status'];
      }

      $trCart = MasterTrx::where(function($query) use($whereField, $whereValue, $data) {
                  if(isset($whereValue)) {
                    $query->where('status', $data['status']);
                  }
                })
                ->where('users_id', Auth::user()->id)
                ->orderBy('master_trx.updated_at', 'DESC')
                ->paginate($request->limit);
      
      foreach($trCart as $row) {
        $result[] = $row;
      }
      
      $meta = [
        'code' => 200,
        'message' => 'Success',
        'status' => 'OK'
      ];

      $data = [
        'page' => $trCart->currentPage(),
        'total_results' => $trCart->total(),
        'total_pages' => $trCart->total(),
        'results' => $result
      ];

      $res['meta'] = $meta;
      $res['data'] = $data;

      return response()->json($res
      , 200);
      
    } else {
      $meta = [
        'code' => 400,
        'message' => 'Failed',
        'status' => 'Fail'
      ];

      $data = [
        'page' => 0,
        'total_results' => 0,
        'total_pages' => 0,
        'results' => null
      ];

      $res['meta'] = $meta;
      $res['data'] = $data;

      return response()->json($res
      , 400);
    }
  }

  public function addCart(Request $request) {
    if($request->isMethod('POST')) {
      $data = $request->all();
      $trCart = new TrCart;
      
      unset($data['_token']);

      foreach($data as $key => $val) {
        $trCart->{$key} = $val;
        $trCart->id_user = Auth::user()->id;
      }

        // $trCart->variasi = $data['variasi'] == '' ? null : $data['variasi'];
        // dd($trCart);
      if($trCart->save()) {
        $meta = ['code' => 200,
                 'message' => 'Success',
                 'status' => 'OK'
                ];

        $res['meta'] = $meta;

        return response()->json($res
        , 200);
      }

    } else {
        $meta = ['code' => 405,
                 'message' => 'Failed',
                 'status' => 'Error'
                ];

        $res['meta'] = $meta;

        return response()->json($res
        , 405);
    }
  }
  
  public function checkout(Request $request) {
    if($request->isMethod('POST')) {
      $data = $request->all();
      $user = Auth::user();
      unset($data['totalPrice']);
      unset($data['id_toko']);
      unset($data['payment_method']);
      $namaBankNya = $request->nama_bank;
      unset($data['nama_bank']);
      foreach($data as $key => $val) {
          foreach($val as $keys => $vals) {
              $trCartUpdate = TrCart::find($data['id'][$keys]);
              $trCartUpdate->quantity = $data['quantity'][$keys];
              $trCartUpdate->save();
          }
      }

      $trCart = TrCart::where('id_user', $user->id);
      $trCartList = $trCart->get();

      if(!count($trCartList)) {
          $meta = ['code' => 400,
                 'message' => 'Failed',
                 'status' => 'Fail'
                ];

        $res['meta'] = $meta;


        return response()->json($res
        , 400);
      }

    //   $isTersedia = true;
      foreach($trCartList as $key => $val) {
          $product = Produk::where('id',$val->id_product)->where('isdelete',0)->first();
        //   $productToko = ProdukToko::where('id_master_produk',$product->id)->where('isdelete',0)->first();
        //   if(isset($productToko)){
              $trCheckout = new TrCheckout;
              $product_code = $product->product_code;
              $brand_id = $product->brand_code;
              $category_id = $product->category_code;
              $price = $product->price * $val->quantity;
                
            //   $current_date_time = Carbon::now()->toDateTimeString(); 
              $trCheckout->checkout_code = date('Ymd').$trCartList[0]->id;
              $trCheckout->product_code = $product_code;
              $trCheckout->quantity = $val->quantity;
              $trCheckout->brand_id = $brand_id;
              $trCheckout->category_id = $category_id;
              $trCheckout->users_id = $user->id;
              $trCheckout->price = $price;
              $trCheckout->variasi = $val->variasi;
              $trCheckout->save();
              if($trCheckout->save()){
                 $product->stock = $product->stock - $val->quantity;
                 $product->save();
              }
            //  }else{
            //     $isTersedia = false;  
            //  }
            }
        
    // if($isTersedia){
      if($trCheckout) {
        $masterTrx = new MasterTrx;
        $masterTrx->checkout_code = $trCheckout->checkout_code;
        $masterTrx->id_toko = $request->id_toko;
        $masterTrx->status = 1;
        $masterTrx->total_price = $request->totalPrice;
        $masterTrx->users_id = $user->id;
        $masterTrx->id_payment_method = $request->payment_method;
        $masterTrx->nama_bank = $namaBankNya;
        $masterTrx->save();
        
    //     $userOwner = User::where('id_level', '4')->where('fcm_token','<>','')->get();
    //     foreach($userOwner as $key => $row) {
    //       $notification = new Notification();
    //       $notification->content_id = $masterTrx->id;
    //       $notification->content_type = 'transaksi';
    //       $notification->navigate_to_mobile = 'list_transaksi';
    //       $notification->navigate_to_web = 'list_transaksi';
    //       $notification->content_title = 'Pesanan Baru';
    //       $notification->content_body = 'Ada pesanan baru dari'.$user->name. '. Total harga : '.$request->totalPrice;
    //       $notification->content_img = '';
    //       $notification->created_at = $current_date_time;
    //       $notification->id_level = 4;
    //       $notification->id_user_to = $row->id;
    //       $notification->description = '';
    //       $notification->id_user_from = $idUser;
    //       $notification->save();

    //       if($row->id_fcm_android != null){
    //         $notif = array(
    //           'title' => $notification->content_title,
    //           'body' => $notification->content_body
    //         );
    //         $datas = array(
    //           'content_id' => $notification->content_id,
    //           'content_type' => $notification->content_type,
    //           'navigate_to_mobile' => $notification->navigate_to_mobile ,
    //           'navigate_to_web' => $notification->navigate_to_web,
    //           'content_title' => $notification->content_title,
    //           'content_body' => $notification->content_body,
    //           'content_img' => $notification->content_img,
    //           'created_at' => $notification->created_at,
    //           'id_group' => $notification->id_group,
    //           'id_user_to' => $notification->id_user_to,
    //           'description' => $notification->description,
    //           'id_user_from' => $notification->id_user_from,
    //           'updated_at' => $notification->updated_at,
    //           'id' => $notification->id
    //         );

    //           if($row->id_fcm_android) {
    //               $requests = array(
    //                 'tokenFcm' => $row->id_fcm_android,
    //                 'notif' => $notif,
    //                 'data' => $datas
    //               );
    //               $factory->sendNotif($requests);
    //           }
    //         }        
    //       }
        $meta = ['code' => 200,
                 'message' => 'Success',
                 'status' => 'OK'
                ];

        $res['meta'] = $meta;

        $trCartDelete = $trCart->delete();
        
        return response()->json($res
        , 200);
      }
    // }else{
    //              $meta = ['code' => 400,
    //                  'message' => 'Barang tidak tersedia',
    //                  'status' => 'OK'
    //                 ];
    
    //         $res['meta'] = $meta;
            
    //         return response()->json($res
    //         , 400);
    //     }
    } else {
       $meta = ['code' => 400,
                 'message' => 'Failed',
                 'status' => 'Fail'
                ];

        $res['meta'] = $meta;


        return response()->json($res
        , 400);
    }
   
  }
  
  public function listToko(Request $request){
      $toko = User::leftjoin('master_toko','users.id','master_toko.id_user')
                            ->where('master_toko.status',1)
                            ->where('master_toko.isdelete',0)
                            ->select('master_toko.id','users.name')->get();
      if(isset($toko)){
        $meta = ['code' => 200,
             'message' => 'Success',
             'status' => 'OK'
            ];

        $res['meta'] = $meta;
        $res['data'] = $toko;
        return response()->json($res
        , 200);
      }else{
          $meta = ['code' => 400,
             'message' => 'Data tidak ditemukan',
             'status' => 'Failed'
            ];

        $res['meta'] = $meta;
        $res['data'] = null;
        return response()->json($res
        , 400);
      }
      
  }
  
   public function getPaymentMethod(Request $request){
      $paymentMethod = MasterPaymentMethod::where('status',1)
                            ->where('isdeleted',0)->get();
      if(isset($paymentMethod)){
        $meta = ['code' => 200,
             'message' => 'Success',
             'status' => 'OK'
            ];

        $res['meta'] = $meta;
        $res['data'] = $paymentMethod;
        return response()->json($res
        , 200);
      }else{
          $meta = ['code' => 400,
             'message' => 'Data tidak ditemukan',
             'status' => 'Failed'
            ];

        $res['meta'] = $meta;
        $res['data'] = null;
        return response()->json($res
        , 400);
      }
      
  }
  
  public function delete(Request $request) {
    if($request->isMethod('POST')) { 
        $trCart = TrCart::find($request->id);
        $isSuccess = $trCart->delete();
        if($isSuccess){
            $meta = ['code' => 200,
                 'message' => 'Success',
                 'status' => 'Ok'
                ];

            $res['meta'] = $meta;
    
    
            return response()->json($res
            , 200);
        }else{
             $meta = ['code' => 400,
                 'message' => 'Failed',
                 'status' => 'Fail'
                ];

        $res['meta'] = $meta;


        return response()->json($res
        , 400); 
        }
    } else {
       $meta = ['code' => 400,
                 'message' => 'Failed',
                 'status' => 'Fail'
                ];

        $res['meta'] = $meta;


        return response()->json($res
        , 400);
    }
  }
  
  public function GetBankList(Request $request){
       $masterBank = Bank::where('status',1)->orderBy('id', 'DESC')->get();
      if(isset($masterBank)){
        $meta = ['code' => 200,
             'message' => 'Success',
             'status' => 'OK'
            ];

        $res['meta'] = $meta;
        $res['data'] = $masterBank;
        return response()->json($res
        , 200);
      }else{
          $meta = ['code' => 400,
             'message' => 'Data tidak ditemukan',
             'status' => 'Failed'
            ];

        $res['meta'] = $meta;
        $res['data'] = null;
        return response()->json($res
        , 400);
      }
  }
}
