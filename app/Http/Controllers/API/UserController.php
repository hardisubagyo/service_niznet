<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\MasterKurir;
use App\MasterToko;
use Auth;
use Carbon\Carbon;
use Validator;

class UserController extends Controller
{
  public $successStatus = 200;
  
  public function loginKurir(Request $request){
      $data = $request->all();
     $user = User::where(function ($query) use($data) {
            $query->where('email', '=', $data['email'])
                  ->orWhere('telp', '=', $data['email']);
                    })->where('isdelete', 0)->where('id_level',3)->first();
    
    if($user != null) {
            if(Auth::attempt(['email' => $request->email, 'password' => $request->password]) || Auth::attempt(['telp' => $request->email, 'password' => $request->password])) {
             
              $data = User::select('users.*', 'master_kurir.id as id_kurir', 'master_kurir.status as status_kurir', 'master_kurir.notif')->join('master_kurir', 'users.id', 'master_kurir.id_users')->where('master_kurir.id_users','=',$user->id)->first();
             
              $data->ktp = $data->ktp ? url('/user/KTP/'.$data->ktp) : url('/uploads/produk/nia3.png');
              $data->photo = $data->photo ? url('/user/FOTO/'.$data->photo) : url('/uploads/produk/nia3.png');
              
              if($user->status == 0) {
                return response()->json(array(
                        'code'      =>  400,
                        'message'   => "Data sedang diverifikasi oleh admin"
                ), 400);
              }else{
                return response()->json([
                  'meta' => ["code" => 200, "message" => "Success", "status" => "OK"],
                  'data' => $data
                ], 200);
              }
            }else{
              return response()->json(array(
                        'code'      =>  401,
                        'message'   => "Email atau password tidak cocok"
                    ), 401);
          }
       
    } else {
      return response()->json(array(
                    'code'      =>  404,
                    'message'   =>"User tidak ditemukan"), 404);
    }
  }
 
  public function login(Request $request){
    $data = $request->all();
    $user = User::where(function ($query) use($data) {
    $query->where('email', '=', $data['email'])
                  ->orWhere('telp', '=', $data['email']);
                    })->where('isdelete', 0)->where('id_level',2)->first();
     if($user != null) {
            if(Auth::attempt(['email' => $request->email, 'password' => $request->password]) || Auth::attempt(['telp' => $request->email, 'password' => $request->password])) {
                         $user->ktp = $user->ktp ? url('/user/KTP/'.$user->ktp) : url('/uploads/produk/nia3.png');
                $user->photo = $user->photo ? url('/user/FOTO/'.$user->photo) : url('/uploads/produk/nia3.png');
              
              // $user = Auth::user();
                
              if($user->status == 0) {
                return response()->json(array(
                        'code'      =>  400,
                        'message'   => "Data sedang diverifikasi oleh admin"
                ), 400);
              }else{
                return response()->json([
                  'meta' => ["code" => 200, "message" => "Success", "status" => "OK"],
                  'data' => $user
                ], 200);
              }
            }else{
              return response()->json(array(
                        'code'      =>  401,
                        'message'   => "Email atau password tidak cocok"
                    ), 401);
          }
       
    } else {
      return response()->json(array(
                    'code'      =>  404,
                    'message'   =>"User tidak ditemukan"), 404);
    }
  }
  
  public function checkEmail(Request $request){
       $validator = Validator::make($request->all(), [
          'email' => 'required|email|unique:users'
      ]);
    if ($validator->fails()) {
         return response()->json([
          'meta' =>  ["code" => 400, "message" => "Email telah terdaftar", "status" => "Fail"],
        ], 400);
    }else{
         return response()->json([
          'meta' =>  ["code" => 200, "message" => "Email tersedia", "status" => "Fail"],
        ], 200);
    }
  }

  public function register(Request $request)
  {
      $validator = Validator::make($request->all(), [
          'name' => 'required',
          'email' => 'required|email|unique:users',
          'id_level' => 'required',
          'password' => 'required',
          'password_confirmation' => 'required|same:password'
      ]);

      if ($validator->fails()) {
        return response()->json([
          'meta' =>  ["code" => 400, "message" => "Gagal mendaftarkan akun, silahkan hubungi admin", "status" => "Fail"],
        ], 400);      
      }

      $user = new User;
      $data = $request->all();
      $data['password'] = bcrypt($data['password']);

      $photo = $request->file('photo');
      $ktp = $request->file('ktp');

      unset($data['password_confirmation']);
      unset($data['_token']);

      foreach($data as $key => $val) {
        $user->{$key} = $val;
        $user->status = 1;
      }

      if($user->save()) {
        $success['token'] =  $user->createToken('nApp')->accessToken;
        $success['name'] =  $user->name;
        $updateUser = User::find($user->id);
        
        if(isset($photo)) {
          $fileExt = $photo->extension();
          $fileName = "USR-FOTO-".$user->id.".".$fileExt;
          $path = 'user/FOTO';
          $updateUser->photo = $fileName;
          $urlImg[] = url('user/FOTO/').$fileName;
          $photo->move($path, $fileName);
        }

        if(isset($ktp)) {
          $fileExt = $ktp->extension();
          $fileName = "USR-KTP-".$user->id.".".$fileExt;
          $path = 'user/KTP';
          $updateUser->ktp = $fileName;
          $urlImg[] = url('user/KTP/').$fileName;
          $ktp->move($path, $fileName);
        }

        $updateUser->token = $success['token'];
        $updateUser->save();
        
          if($request->id_level == 3){
                $kurir = new MasterKurir;
                $cekKurir = MasterKurir::where('id_users', $user->id)->first();
                if(null == $cekKurir){
                    $kurir->id_users = $user->id;
                    $kurir->status = 0;
                    $kurir->notif = 0;
                    $kurir->save();
                }
            }else if($request->id_level == 5){
                $toko = new MasterToko;
                $cekToko = MasterToko::where('id_user', $user->id)->first();
                if(!isset($cekToko)){
                    $toko->id_user = $user->id;
                    $toko->status = 1;
                    $toko->save();
                }
            }
      };

      return response()->json([
        'meta' =>  ["code" => 200, "message" => "Success", "status" => "Ok"],
      ], 200);
  }

  public function updateProfile(Request $request)
  {
      $validator = Validator::make($request->all(), [
          'name' => 'required',
          'email' => 'required',
          'password' => 'required'
      ]);
        
      if ($validator->fails()) {
        return response()->json([
          'meta' =>  ["code" => 400, "message" => "Failed", "status" => "Fail"],
        ], 400);      
      }

      $user = User::find(Auth::user()->id);
      
      $data = $request->all();
      $data['password'] = bcrypt($data['password']);
      $photo = $request->file('photo');
      $ktp = $request->file('ktp');

    //   unset($data['password_confirmation']);
    //   unset($data['_token']);

      foreach($data as $key => $val) {
        $user->{$key} = $val;
      }

      if($user->save()) {
        
        if(isset($photo)) {
          $fileExt = $photo->extension();
          $fileName = "USR-FOTO-".$user->id.".".$fileExt;
          $path = 'user/FOTO';
          $user->photo = $fileName;
          $urlImg[] = url('user/FOTO/').$fileName;
          $photo->move($path, $fileName);
        }

        if(isset($ktp)) {
          $fileExt = $ktp->extension();
          $fileName = "USR-KTP-".$user->id.".".$fileExt;
          $path = 'user/KTP';
          $user->ktp = $fileName;
          $urlImg[] = url('user/KTP/').$fileName;
          $ktp->move($path, $fileName);
        }
        $user->save();
      }
      
            $userNew = User::where('email', $request->email)->first();
            $userNew->ktp = $user->ktp ? url('/user/KTP/'.$user->ktp) : url('/uploads/produk/nia3.png');
            $userNew->photo = $user->photo ? url('/user/FOTO/'.$user->photo) : url('/uploads/produk/nia3.png');
            if($userNew->id_level == 3){
                  $data = User::select('users.*', 'master_kurir.id as id_kurir', 'master_kurir.status as status_kurir', 'master_kurir.notif')->join('master_kurir', 'users.id', 'master_kurir.id_users')->where('master_kurir.id_users','=',$user->id)->first();
                      $data->ktp = $data->ktp ? url('/user/KTP/'.$data->ktp) : url('/uploads/produk/nia3.png');
                      $data->photo = $data->photo ? url('/user/FOTO/'.$data->photo) : url('/uploads/produk/nia3.png');
                       return response()->json([
                  'meta' => ["code" => 200, "message" => "Success", "status" => "OK"],
                  'data' => $data
                ], 200);
            }else{
                return response()->json([
                'meta' => ["code" => 200, "message" => "Success", "status" => "OK"],
                  'data' => $userNew
                ], 200);
            }
     
  }
  
  public function details()
  {
      $user = Auth::user();
      return response()->json([
        'meta' =>  ["code" => 200, "message" => "Success", "status" => "Ok"],
        'data' => $user
      ], 200);
  }
  
   public function updateFcm(Request $request){
    if($request->isMethod('POST')) {
      $user = Auth::user();
      $userDetail = User::where('id',$user->id)->first();
      
      $current_date_time = Carbon::now()->toDateTimeString(); 
      
      if(isset($userDetail)){
        $userDetail->updated_at = $current_date_time;
        $userDetail->id_fcm_android = $request->id_fcm;
        if($userDetail->save()){
          return response()->json(['meta' =>  ["code" => 200, "message" => "Success", "status" => "Ok"]], 200);
        }else{
          return response()->json(['meta' =>  ["code" => 500, "message" => "Gagal Update", "status" => "Fail"]], 500);
        }
      }else{
          return response()->json(['meta' =>  ["code" => 404, "message" => "User tidak ditemukan", "status" => "Fail"]], 404);
      }
    }else{
      return response()->json(['meta' =>  ["code" => 405, "message" => "Method salah", "status" => "Fail"]], 405);
    }
  }

}
