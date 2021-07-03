<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Produk;
use App\Kategori;
use App\Brand;
use Auth;
use Validator;

class ProdukController extends Controller
{
  public function getList(Request $request) {
    if($request->isMethod('GET')) {
      $data = $request->all();
      $whereField = 'product_code, product_name, brand_name, category_name';
      $whereValue = (isset($data['where_value'])) ? $data['where_value'] : '';
      $customerList = Produk::join('master_brand', 'master_produk.brand_code', 'master_brand.id')
                      ->join('master_category', 'master_produk.category_code', 'master_category.id')
                      ->leftjoin('master_variasi', 'master_category.id_variasi', 'master_variasi.id')
                      ->where(function($query) use($whereField, $whereValue) {
                        if($whereValue) {
                          foreach(explode(', ', $whereField) as $idx => $field) {
                            $query->orWhere($field, 'LIKE', "%".$whereValue."%");
                          }
                        }
                      })
                      ->where('master_produk.isdelete', 0)
                      ->where('master_produk.status', 1)
                      ->select('master_produk.*', 'master_brand.brand_name', 'master_category.category_name', 'master_variasi.name as variasi_name')
                      ->orderBy('updated_at', 'DESC')
                      ->paginate($request->limit);
                      
      foreach($customerList->items() as $row) {
        $row->image1 = ($row->image1) ? url('uploads/produk/'.$row->image1) :url('uploads/produk/nia3.png');
        $row->image2 = ($row->image2) ? url('uploads/produk/'.$row->image2) :url('uploads/produk/nia3.png');
        $row->image3 = ($row->image3) ? url('uploads/produk/'.$row->image3) :url('uploads/produk/nia3.png');
        $row->image4 = ($row->image4) ? url('uploads/produk/'.$row->image4) :url('uploads/produk/nia3.png');
        $row->image5 = ($row->image5) ? url('uploads/produk/'.$row->image5) :url('uploads/produk/nia3.png');
        $result[] = $row;
      }

      $meta = [
        'code' => 200,
        'message' => 'Success',
        'status' => 'OK'
      ];

      $data = [
        'page' => $customerList->currentPage(),
        'total_results' => $customerList->total(),
        'total_pages' => $customerList->total(),
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

  public function getNewProduct(Request $request) {
    if($request->isMethod('GET')) {
      $data = $request->all();
      $whereField = 'product_code, product_name, brand_code, category_code';
      $whereValue = (isset($data['where_value'])) ? $data['where_value'] : '';
      $customerList = Produk::join('master_brand', 'master_produk.brand_code', 'master_brand.id')
                      ->join('master_category', 'master_produk.category_code', 'master_category.id')
                      ->leftjoin('master_variasi', 'master_category.id_variasi', 'master_variasi.id')
                      ->where(function($query) use($whereField, $whereValue) {
                        if($whereValue) {
                          foreach(explode(', ', $whereField) as $idx => $field) {
                            $query->orWhere($field, 'LIKE', "%".$whereValue."%");
                          }
                        }
                      })
                      ->where('master_produk.isdelete', 0)
                      ->where('master_produk.status', 1)
                      ->select('master_produk.*', 'master_brand.brand_name', 'master_category.category_name', 'master_variasi.name as variasi_name')
                      ->orderBy('updated_at', 'DESC')->take(6)->get();
                      
      foreach($customerList as $row) {
        $row->image1 = ($row->image1) ? url('uploads/produk/'.$row->image1) :url('uploads/produk/nia3.png');
        $row->image2 = ($row->image2) ? url('uploads/produk/'.$row->image2) :url('uploads/produk/nia3.png');
        $row->image3 = ($row->image3) ? url('uploads/produk/'.$row->image3) :url('uploads/produk/nia3.png');
        $row->image4 = ($row->image4) ? url('uploads/produk/'.$row->image4) :url('uploads/produk/nia3.png');
        $row->image5 = ($row->image5) ? url('uploads/produk/'.$row->image5) :url('uploads/produk/nia3.png');
        $result[] = $row;
      }

      $meta = [
        'code' => 200,
        'message' => 'Success',
        'status' => 'OK'
      ];

      $data = 
        // 'page' => $customerList->currentPage(),
        // 'total_results' => $customerList->total(),
        // 'total_pages' => $customerList->total(),
         $result
      ;

      $res['meta'] = $meta;
      $res['data'] = $data;

      return response()->json($res
      , 201);
      
    } else {
      return response()->json([
        'status' => false,
        'message' => "<strong>failed') !</strong> method_not_allowed"
      ], 405);
    }
  }

  public function getListKategori(Request $request) {
    if($request->isMethod('GET')) {
      $data = $request->all();
      $whereField = 'category_name';
      $result = [];
      $whereValue = (isset($data['where_value'])) ? $data['where_value'] : '';
      $customerList = Kategori::where(function($query) use($whereField, $whereValue) {
                        if($whereValue) {
                          foreach(explode(', ', $whereField) as $idx => $field) {
                            $query->orWhere($field, 'LIKE', "%".$whereValue."%");
                          }
                        }
                      })
                      ->where('master_category.isdelete', 0)
                      ->where('master_category.status', 1)
                      ->orderBy('category_name', 'ASC')->get();
      if($customerList != null){
          foreach($customerList as $row) {
            $row->category_icon = ($row->category_icon) ? url('uploads/category/'.$row->category_icon) :url('uploads/produk/nia3.png');

            $result[] = $row;
          }
      }else{
        $result = null;
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
      return response()->json([
        "meta" => [
          'code' => 405,
          'message' => 'fail',
          'status' => 'fail'
        ],
        "data" => null 
      ], 405);
    }
  }

  public function getListBrand(Request $request) {
    if($request->isMethod('GET')) {
      $data = $request->all();
      $whereField = 'brand_name';
      $result = [];
      $whereValue = (isset($data['where_value'])) ? $data['where_value'] : '';
      $customerList = Brand::where(function($query) use($whereField, $whereValue) {
                        if($whereValue) {
                          foreach(explode(', ', $whereField) as $idx => $field) {
                            $query->orWhere($field, 'LIKE', "%".$whereValue."%");
                          }
                        }
                      })
                      ->where('master_brand.isdelete', 0)
                      ->where('master_brand.status', 1)
                      ->orderBy('brand_name', 'ASC')->get();
      if($customerList != null){
          foreach($customerList as $row) {
            $row->brand_icon = ($row->brand_icon) ? url('uploads/brand/'.$row->brand_icon) :url('uploads/produk/nia3.png');
            $result[] = $row;
          }
      }else{
        $result = null;
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
      return response()->json([
        "meta" => [
          'code' => 405,
          'message' => 'fail',
          'status' => 'fail'
        ],
        "data" => null 
      ], 405);
    }
  }

  public function uploadImg(Request $request) {
    if($request->isMethod('POST')) {
      $data = $request->all();
      $img = $request->file('images');
      $produk = Produk::find($request->id);
      
      foreach($img as $key => $row) {
        $index = $key + 1;
        $fileExt = $row->extension();
        $fileName = "IMG-PRODUCT-".$request->code_produk."-".$request->id."-".$index.".".$fileExt;
        $path =  public_path().'/uploads/produk/' ;
        $produk->{'image'.$index} = $fileName;
        
        $urlImg[] = $path.$fileName;

        $row->move($path, $fileName);
      }

      if($produk->save()) {
        $meta = [
          'code' => 200,
          'message' => 'Success',
          'status' => 'OK'
        ];
        $data = [
          'url' => $urlImg
        ];
        $result['meta'] = $meta;
        $result['data'] = $data;
        return response()->json($result
        , 200);
      
      }else {
        $meta = [
          'code' => 405,
          'message' => 'Fail',
          'status' => 'Fail'
        ];
        $data = null;
        return response()->json($result
        , 405);
      }
    } else {
        $meta = [
          'code' => 405,
          'message' => 'Fail',
          'status' => 'Fail'
        ];
        $data = null;
        return response()->json($result
        , 405);
    }
  }


  public function showImg(Request $request) {
    if($request->isMethod('GET')) {
      $data = $request->all();
      $produk = Produk::find($request->id);
      
      if(!$produk) {
        return response()->json([
          'status' => false,
          'message' => "<strong>failed') !</strong> Product Not Found"
        ], 405);
      }
      
      for ($i=1; $i <= 5; $i++) {
        $path =  base_path('storage/produk/');

        if($produk->{'image'.$i}) {
          $urlImg[] = $path.$produk->{'image'.$i};
        }
      }

      if($produk->save()) {
        return response()->json([
          'status' => true,
          'responses' => $urlImg
        ], 201);
      };

    } else {
      return response()->json([
        'status' => false,
        'message' => "<strong>failed') !</strong> method_not_allowed"
      ], 405);
    }
  }
}
