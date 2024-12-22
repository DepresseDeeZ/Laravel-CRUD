<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    //this method will show prooduct page
    public function index()
    {
        $products = Product::orderBy('created_at','DESC')->get();
        return view("products.list",["products"=> $products]);

    }
    //this method will show create product page
    public function create()
    {
        return view("products.create");

    }
    //this method will store product or insert prooduct in db
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|min:5',
            'sku' => 'required|min:3',
            'price' => 'required|numeric',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($request->image != ""){
            $rules['image'] = 'image';
        }

        if ($validator->fails())
        {
            return redirect()->route('products.create')->withInput()->withErrors($validator);
        }

        //here we will instert product in db
        $product = new Product();
        $product->name = $request->name;
        $product->sku = $request->sku;
        $product->price = $request->price;
        $product->description = $request->description;
        $product->save();


        if($request->image != " "){
             //here we will store Images in db
        $image = $request->image;
        $ext = $image->getClientOriginalExtension();
        $imageName = time().'.'.$ext; //Unique image name eg 123456.jpg

        //Save  image Public -> Product directory
        $image->move(public_path('uploads/products'), $imageName);


        //save image in database
        $product->image = $imageName;
        $product->save();
        }

       

        return redirect()->route('products.index')->with('success','Products added successfully');
        

    }
    //this method will show edit product page
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('products.edit',['product'=> $product]);

    }
    //this method will update product in db
    public function update($id, Request $request)
    {
        $product = Product::findOrFail($id);

        $rules = [
            'name' => 'required|min:5',
            'sku' => 'required|min:3',
            'price' => 'required|numeric',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($request->image != ""){
            $rules['image'] = 'image';
        }

        if ($validator->fails())
        {
            return redirect()->route('products.edit',$product->id)->withInput()->withErrors($validator);
        }

        //here we will update product in db
        $product->name = $request->name;
        $product->sku = $request->sku;
        $product->price = $request->price;
        $product->description = $request->description;
        $product->save();


        if($request->image != " "){

        //delete the old image
        File::delete(public_path("uploads/product/".$product->image));
        //here we will store Images in db
        $image = $request->image;
        $ext = $image->getClientOriginalExtension();
        $imageName = time().'.'.$ext; //Unique image name eg 123456.jpg

        //Save  image Public -> Product directory
        $image->move(public_path('uploads/products/'), $imageName);


        //save image in database
        $product->image = $imageName;
        $product->save();
        }

       

        return redirect()->route('products.index')->with('success','Products Updated successfully');        
        

    }
    //this method will delete product from db
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        //delete the old image
        File::delete(public_path('uploads/products/'.$product->image));

        //here we will delete product from db
        $product->delete();
        return redirect()->route('products.index')->with('success','Product deleted successfully');

    }

}
