<?php

namespace App\Http\Controllers;


use App\Catalog1;
use App\Catalog2;
use App\Catalog3;
use App\CatalogProduct;
use App\ProductType;
use App\User;
use Illuminate\Http\Request;
use Auth;
use DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;


class CatalogProductController extends Controller
{
    public function __construct()
    {
        $this->current_menu = 'catalog_product';
        $this->current_dir = 'catalogProduct';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        $company_id = Auth::user()->company_id;
        $product_name_id = $request->product_name;
        $catalog_1_id = $request->catalog_1;
        $catalog_0_id = $request->catalog_0;
        $product_name_array = array();
        $catalog_1_array = array();
        $catalog_0_array = array();
        $query = CatalogProduct::join('catalog_2','catalog_2.id','=','catalog_product.catalog_id')
            ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
            ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
            ->where('catalog_0.company_id',$company_id)
            ->where('catalog_1.company_id',$company_id)
            ->where('catalog_2.company_id',$company_id)
            ->where('catalog_product.company_id',$company_id)
            ->where($this->current_menu.'.status', '!=', '9');
        # search functionality
        if (!empty($request->search)) {
            $q = $request->search;
            $query->where(function ($subq) use ($q) {
                $subq->where($this->current_menu.'.name', 'LIKE', $q . '%');
            });
        }

        if(!empty($product_name_id))
        {
            $query->whereIn('catalog_product.catalog_id',$product_name_id);

            $product_name_array = DB::table('catalog_2')
                                ->where('company_id',$company_id)
                                ->whereIn('id',$product_name_id)
                                ->where('status',1)
                                ->pluck('id')->toArray();
        }
        if(!empty($catalog_1_id))
        {
            $query->whereIn('catalog_1.id',$catalog_1_id);

            $catalog_1_array = DB::table('catalog_1')
                                ->where('company_id',$company_id)
                                ->whereIn('id',$catalog_1_id)
                                ->where('status',1)
                                ->pluck('id')->toArray();
        }
        if(!empty($catalog_0_id))
        {
            $query->whereIn('catalog_0.id',$catalog_0_id);

            $catalog_0_array = DB::table('catalog_0')
                                ->where('company_id',$company_id)
                                ->whereIn('id',$catalog_0_id)
                                ->where('status',1)
                                ->pluck('id')->toArray();
        }

        $data = $query->select($this->current_menu.'.*','catalog_2.name as cat3','catalog_2.id as cat3id','catalog_1.id as cat2id','catalog_1.name as cat2','catalog_0.id as cat1id','catalog_0.name as cat1')
            ->orderBy('id', 'desc')
            ->paginate('500');
            // ->get();

        // $product_type_array = DB::table('product_type')->where('status',1)->where('company_id',$company_id)->pluck('name','id');

        $product_name = DB::table('catalog_2')->where('status',1)->where('company_id',$company_id)->pluck('name','id')->toArray();
        $catalog_1 = DB::table('catalog_1')->where('status',1)->where('company_id',$company_id)->pluck('name','id')->toArray();
        $catalog_0 = DB::table('catalog_0')->where('status',1)->where('company_id',$company_id)->pluck('name','id')->toArray();

        $descImageCount = DB::table('sku_description_images')
                        ->where('company_id',$company_id)
                        ->groupBy('product_id')
                        ->pluck(DB::raw("COUNT(DISTINCT id) as data"),'product_id')->toArray();

        return view($this->current_dir.'.index',
            [
                'records' => $data,
                'product_name'=> $product_name,
                'catalog_1'=> $catalog_1,
                'catalog_0'=> $catalog_0,
                'product_name_array'=>$product_name_array,
                'catalog_1_array'=>$catalog_1_array,
                'catalog_0_array'=>$catalog_0_array,
                'current_menu' => $this->current_menu,
                // 'product_type_array'=> $product_type_array,
                'descImageCount'=> $descImageCount,
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        #Catalog 1 data
        $company_id = Auth::user()->company_id;
        $flag = 'FALSE';
        $product_type = array();
        $product_type_primary = array();
        $cat1 = Catalog1::where('status', '=', '1')->where('company_id',$company_id)->pluck('name', 'id');

        #Catalog 2 data
        $cat2 = Catalog2::where('status', '=', '1')->where('company_id',$company_id)->pluck('name', 'id');
        
        #Catalog 2 data
        $cat3 = Catalog3::where('status', '=', '1')->where('company_id',$company_id)->pluck('name', 'id');

        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',4)->first();
        if(!empty($check))
        {
            $product_type = ProductType::where('status', '=', '1')->where('flag_neha',2)->where('company_id',$company_id)->pluck('name', 'id');
            $product_type_primary = ProductType::where('status', '=', '1')->where('flag_neha',1)->where('company_id',$company_id)->pluck('name', 'id');
            $flag = 'TRUE';
        }


        $finalProductType = DB::table('product_type')->where('status', '=', '1')->where('company_id',$company_id)->pluck('name', 'id');

        return view($this->current_dir.'.create',
            ['current_menu' => $this->current_menu,
                'cat1' => $cat1,
                'cat2' => $cat2,
                'cat3' => $cat3,
                'flag'=> $flag,
                'company_id'=> $company_id,
                'product_type_primary'=> $product_type_primary,
                'product_type'=> $product_type,
                'finalProductType'=> $finalProductType,
            ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'item_code' => 'required|min:2|max:20',
            'item_name' => 'required|min:2|max:255',
            'weight' => 'required|min:1|max:50',
            'hsn' => 'required|min:1|max:20',
            'catalog_1' => 'required',
            'catalog_2' => 'required',
            'catalog_3' => 'required',
            'quantity_per_case' => 'required',
        ]);

        $company_id = Auth::user()->company_id;
        // dd($request);
        DB::beginTransaction();
        /**
         * @des: save data in Project table
         */
        $myArr = [
            'itemcode' => trim($request->item_code),
            'name' => trim($request->item_name),
            'weight' => trim($request->weight),
            'hsn_code' => trim($request->hsn),
            'catalog_id' => trim($request->catalog_3),
            'company_id' => $company_id,
            'product_type_primary' => trim(!empty($request->primary_product_type)?$request->primary_product_type:'0'),
            'quantity_per_case' => trim($request->quantity_per_case),
            'product_type' => trim(!empty($request->product_type)?$request->product_type:'0'),
            'quantiy_per_other_type'=> trim(!empty($request->quantiy_per_other_type)?$request->quantiy_per_other_type:'0'),
            'gst_percent' => trim($request->gst),
            'product_sequence' => trim($request->p_sequence),
            'final_product_type' => trim($request->finalProductType),

            'description_eng' => (!empty($request->description_eng)?$request->description_eng:'-'),
            'description_hind' => (!empty($request->description_hind)?$request->description_hind:'-'),
            'brand_details' => (!empty($request->brand_details)?$request->brand_details:'-'),

            'ingredriants_details' => !empty($request->ingredriants_details)?$request->ingredriants_details:'',

            // 'packing_type' => trim($request->pack_type),
            'created_by' => Auth::user()->id,
            'status' => 1
        ];

        $catalog_product = CatalogProduct::create($myArr);

        if ($request->hasFile('imageFile')) {
            if($request->file('imageFile')->isValid()) {
                try {
                    $file = $request->file('imageFile');
                    $name = date('YmdHis') . '.' . $file->getClientOriginalExtension();

                    # save to DB
                    $personImage = CatalogProduct::where('id',$catalog_product->id)->update(['image_name' => 'sku_images/'.$name]);

                    $request->file('imageFile')->move("sku_images", $name);
                } catch (Illuminate\Filesystem\FileNotFoundException $e) {

                }
            }
        }


         if ($request->hasFile('videoFile')) {
            if($request->file('videoFile')->isValid()) {
                try {
                    $file = $request->file('videoFile');
                    $name = date('YmdHis') . '.' . $file->getClientOriginalExtension();

                    # save to DB
                    $personImage = CatalogProduct::where('id',$catalog_product->id)->update(['video_name' => 'sku_description_video/'.$name]);

                    $request->file('videoFile')->move("sku_description_video", $name);
                } catch (Illuminate\Filesystem\FileNotFoundException $e) {

                }
            }
        }


         if ($request->imageFileDesc) {
            foreach ($request->imageFileDesc as $ikey => $ivalue) {
                    if($ivalue->isValid()) {

                        try {
                            $file = $ivalue;
                            $name = $catalog_product->id.date('YmdHis') . '.' . $file->getClientOriginalExtension();
                            $finalName = $ikey.$name;
                            $insArray = [
                                'company_id' => $company_id,
                                'product_id' => $catalog_product->id,
                                'image' => $finalName,
                                'created_at' => date('Y-m-d H:i:s')
                            ];


                            # save to DB
                            $personImageDes = DB::table('sku_description_images')->insert($insArray);

                            $ivalue->move("sku_description_images", $finalName);
                        } catch (Illuminate\Filesystem\FileNotFoundException $e) {

                        }
                    }
            }

            


        }


        if ($catalog_product) {
            DB::commit();
            Session::flash('message', Lang::get('common.'.$this->current_menu).' created successfully');
            Session::flash('class', 'success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('class', 'danger');
        }

        return redirect()->intended($this->current_menu);

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        #decrypt id
        $uid = Crypt::decryptString($id);
        $company_id = Auth::user()->company_id;
        $product_type = array();
        $product_type_primary = array();
        $flag = 'FALSE';
        #fetch Project
        $catalog_data = CatalogProduct::join('catalog_2','catalog_product.catalog_id','=','catalog_2.id')
        ->join('catalog_1','catalog_1.id','=','catalog_2.catalog_1_id')
        ->join('catalog_0','catalog_0.id','=','catalog_1.catalog_0_id')
        ->where('catalog_0.company_id',$company_id)
        ->where('catalog_1.company_id',$company_id)
        ->where('catalog_2.company_id',$company_id)
        ->where('catalog_product.company_id',$company_id)
        ->select('catalog_product.*','catalog_1.catalog_0_id','catalog_2.catalog_1_id')
        ->where('catalog_product.id',$uid)->first();;

        #Catalog 1 data
        $cat1 = Catalog1::where('status', '=', '1')->where('company_id',$company_id)->pluck('name', 'id');

        #Catalog 2 data
        $cat2 = Catalog2::where('status', '=', '1')->where('company_id',$company_id)->pluck('name', 'id');
        #Catalog 3 data
        $cat3 = Catalog3::where('status', '=', '1')->where('company_id',$company_id)->pluck('name', 'id');

        $check = DB::table('app_other_module_assign')->where('company_id',$company_id)->where('module_id',4)->first();
        if(!empty($check))
        {
            $product_type = ProductType::where('status', '=', '1')->where('flag_neha',2)->where('company_id',$company_id)->pluck('name', 'id');
            $product_type_primary = ProductType::where('status', '=', '1')->where('flag_neha',1)->where('company_id',$company_id)->pluck('name', 'id');

            $flag = 'TRUE';
        }
        // $product_type = ProductType::where('status', '=', '1')->where('company_id',$company_id)->pluck('name', 'id');

        $finalProductType = DB::table('product_type')->where('status', '=', '1')->where('company_id',$company_id)->pluck('name', 'id');


        return view($this->current_dir.'.edit',
            [
                'catalog_data' => $catalog_data,
                'cat1' => $cat1,
                'cat2' => $cat2,
                'cat3' => $cat3,
                'encrypt_id' =>$id,
                'flag'=> $flag,
                'product_type'=> $product_type,
                'product_type_primary'=> $product_type_primary,
                'finalProductType'=> $finalProductType,
                'company_id'=> $company_id,
                'current_menu'=>$this->current_menu
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validate = $request->validate([
            'item_code' => 'required|min:2|max:20',
            'item_name' => 'required|min:2|max:255',
            'weight' => 'required|min:1|max:50',
            'hsn' => 'required|min:1|max:20',
            'catalog_1' => 'required',
            'catalog_2' => 'required',
            'catalog_3' => 'required',
            'quantity_per_case' => 'required',
        ]);

        $id = Crypt::decryptString($id);
        $company_id = Auth::user()->company_id;
        $catalog_data = CatalogProduct::where('company_id',$company_id)->findOrFail($id);

        DB::beginTransaction();
        /**
         * @des: save data in Project table
         */
        $myArr = [
            'itemcode' => trim($request->item_code),
            'name' => trim($request->item_name),
            'weight' => trim($request->weight),
            'hsn_code' => trim($request->hsn),
            'created_by' => Auth::user()->id,
            'catalog_id' => trim($request->catalog_3),
            'product_type_primary' => trim(!empty($request->primary_product_type)?$request->primary_product_type:'0'),
            'product_type' => trim(!empty($request->product_type)?$request->product_type:'0'),
            'quantity_per_case' => trim($request->quantity_per_case),
            'quantiy_per_other_type'=> trim(!empty($request->quantiy_per_other_type)?$request->quantiy_per_other_type:'0'),
            'gst_percent' => trim($request->gst),
            'final_product_type' => trim($request->finalProductType),
            'product_sequence' => trim($request->p_sequence),

            'description_eng' => (!empty($request->description_eng)?$request->description_eng:'-'),
            'description_hind' => (!empty($request->description_hind)?$request->description_hind:'-'),
            'brand_details' => (!empty($request->brand_details)?$request->brand_details:'-'),

            'ingredriants_details' => !empty($request->ingredriants_details)?$request->ingredriants_details:'',

        ];

        $catalog = $catalog_data->update($myArr);

        if ($request->hasFile('imageFile')) {
            if($request->file('imageFile')->isValid()) {
                try {
                    $file = $request->file('imageFile');
                    $name = date('YmdHis') . '.' . $file->getClientOriginalExtension();

                    # save to DB
                    $personImage = CatalogProduct::where('id',$id)->where('company_id',$company_id)->update(['image_name' => 'sku_images/'.$name]);

                    $request->file('imageFile')->move("sku_images", $name);
                } catch (Illuminate\Filesystem\FileNotFoundException $e) {

                }
            }
        }


        if ($request->hasFile('videoFile')) {
            if($request->file('videoFile')->isValid()) {
                try {
                    $file = $request->file('videoFile');
                    $name = date('YmdHis') . '.' . $file->getClientOriginalExtension();

                    # save to DB
                    $personImage = CatalogProduct::where('id',$id)->where('company_id',$company_id)->update(['video_name' => 'sku_description_video/'.$name]);

                    $request->file('videoFile')->move("sku_description_video", $name);
                } catch (Illuminate\Filesystem\FileNotFoundException $e) {

                }
            }
        }



        if ($request->imageFileDesc) {
            $deleteDataForCatalogDesc = DB::table('sku_description_images')->where('company_id',$company_id)->where('product_id',$id)->delete();
            foreach ($request->imageFileDesc as $ikey => $ivalue) {
                    if($ivalue->isValid()) {

                        try {
                            $file = $ivalue;
                            $name = $id.date('YmdHis') . '.' . $file->getClientOriginalExtension();
                            $finalName = $ikey.$name;
                            $insArray = [
                                'company_id' => $company_id,
                                'product_id' => $id,
                                'image' => $finalName,
                                'created_at' => date('Y-m-d H:i:s')
                            ];
                            # save to DB
                            $personImageDes = DB::table('sku_description_images')->insert($insArray);

                            $ivalue->move("sku_description_images", $finalName);
                        } catch (Illuminate\Filesystem\FileNotFoundException $e) {

                        }
                    }
            }
        }



        if ($catalog) {
            DB::commit();
            Session::flash('message', Lang::get('common.'.$this->current_menu).' updated successfully');
            Session::flash('class', 'success');
        } else {
            DB::rollback();
            Session::flash('message', 'Something went wrong!');
            Session::flash('class', 'danger');
        }

        return redirect()->intended($this->current_menu);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function productTypeName(Request $request)
    {
        $id = $request->id;
        $company_id = Auth::user()->company_id;

        $type_name = DB::table('product_type')->where('id',$id)->where('company_id',$company_id)->first();
        if(!empty($type_name))
        {
            $data['code'] = 200;
            $data['result'] = ($type_name->name);
            $data['message'] = 'success'; 
        }
        else
        {
            $data['code'] = 401;
            $data['result'] = '';
            $data['message'] = 'unauthorized request';
        }
        return json_encode($data);

        
    }
}
