<?php

namespace Fickrr\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Fickrr\Http\Controllers\Controller;
use Session;
use Fickrr\Models\Category;
use Fickrr\Models\Settings;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Helper;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
		
    }
	
	public function custom()
	{
	    $dw_v = Helper::version_no();
		$custom = Settings::customSettings();
		return $custom->$dw_v;
	} 
	public function seo_slug($string)
	{
	    
		$spaceRepl = "-";
		$string = str_replace("&", "and", $string);
		$string = preg_replace("/[^a-zA-Z0-9 _-]/", "", $string);
		$string = preg_replace("/".$spaceRepl."+/", "", $string);
		$string = strtolower($string);
		$string = preg_replace("/[ ]+/", " ", $string);
		$string = str_replace(" ", $spaceRepl, $string);
		return $string;	
    
	}
	
	public function non_seo_slug($string)
	{
	    $spaceRepl = "-";
		$string = preg_replace("/[ ]+/", " ", $string);
        $string = str_replace(" ", $spaceRepl, $string);
        return $string;	
    
	}
	
	/* category */
	
	public function category()
    {
        
		
		$categoryData['category'] = Category::getcategoryData();
		if($this->custom() != 0)
	    {
		return view('admin.category',[ 'categoryData' => $categoryData]);
		}
	   	else
	   	{
		  return redirect('/admin/license');
	   	}
    }
    
	
	public function add_category()
	{
	   if($this->custom() != 0)
	   {
	   return view('admin.add-category');
	   }
	   else
	   {
		  return redirect('/admin/license');
	   }
	}
	
	public function category_slug($text)
	{
    // replace non letter or digits by -
    $text = str_replace(' ', '-', $text);

    return $text;
    }

	
	/*public function category_slug($string){
		   $slug=preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
		   return $slug;
    }*/
	
	
	
	public function save_category(Request $request)
	{
 
    
         $category_name = $request->input('category_name');
		 $additional['settings'] = Settings::editAdditional();
	     if($additional['settings']->site_url_rewrite == 1)
		 {
		   $category_slug = $this->seo_slug($category_name);
		 }
		 else
		 {
		   $category_slug = $this->non_seo_slug($category_name);
		 }
		 $category_status = $request->input('category_status');
		 if(!empty($request->input('menu_order')))
		 {
		 $menu_order = $request->input('menu_order');
		 }
		 else
		 {
		   $menu_order = 0;
		 }
		 $category_allow_seo = $request->input('category_allow_seo');
		 if($request->input('category_seo_keyword') != "")
		 {
		 $category_seo_keyword = $request->input('category_seo_keyword');
		 }
		 else
		 {
		 $category_seo_keyword = "";
		 }
		 if($request->input('category_seo_desc') != "")
		 {
		 $category_seo_desc = $request->input('category_seo_desc');
		 }
		 else
		 {
		 $category_seo_desc = "";
		 }
		 
		 
         
		 $request->validate([
							'category_name' => 'required',
							'category_status' => 'required',
							
         ]);
		 $rules = array(
				'category_name' => ['required', 'max:255', Rule::unique('category') -> where(function($sql){ $sql->where('drop_status','=','no');})],
				
	     );
		 
		 $messsages = array(
		      
	    );
		 
		$validator = Validator::make($request->all(), $rules,$messsages);
		
		if ($validator->fails()) 
		{
		 $failedRules = $validator->failed();
		 return back()->withErrors($validator);
		} 
		else
		{
		
		
		 
		$data = array('category_name' => $category_name, 'category_slug' => $category_slug, 'category_status' => $category_status, 'menu_order' => $menu_order, 'category_allow_seo' => $category_allow_seo, 'category_seo_keyword' => $category_seo_keyword, 'category_seo_desc' => $category_seo_desc);
        Category::insertcategoryData($data);
		return redirect('/admin/category')->with('success', 'Insert successfully.');
            
            
 
       } 
     
    
  }
  
  public function all_delete_category(Request $request)
	{
	   $data = array('drop_status'=>'yes');
	   $cat_id = $request->input('cat_id');
	   foreach($cat_id as $id)
	   {
	      Category::deleteCategorydata($id,$data);
	   }
	   return redirect()->back()->with('success','Delete successfully.');
	
	}
  
  public function delete_category($cat_id){

      $data = array('drop_status'=>'yes');
	  
        
      Category::deleteCategorydata($cat_id,$data);
	  
	  return redirect()->back()->with('success', 'Delete successfully.');

    
  }
  
  
  public function edit_category($cat_id)
	{
	   
	   $edit['category'] = Category::editcategoryData($cat_id);
	   if($this->custom() != 0)
	   {
	   return view('admin.edit-category', [ 'edit' => $edit, 'cat_id' => $cat_id]);
	   }
	   else
	   {
		  return redirect('/admin/license');
	   }
	}
	
	
	
	public function update_category(Request $request)
	{
	
	    $category_name = $request->input('category_name');
		 $additional['settings'] = Settings::editAdditional();
	     if($additional['settings']->site_url_rewrite == 1)
		 {
		   $category_slug = $this->seo_slug($category_name);
		 }
		 else
		 {
		   $category_slug = $this->non_seo_slug($category_name);
		 }
		 $category_status = $request->input('category_status');
		 if(!empty($request->input('menu_order')))
		 {
		 $menu_order = $request->input('menu_order');
		 }
		 else
		 {
		   $menu_order = 0;
		 }
		 $category_allow_seo = $request->input('category_allow_seo');
		 if($request->input('category_seo_keyword') != "")
		 {
		 $category_seo_keyword = $request->input('category_seo_keyword');
		 }
		 else
		 {
		 $category_seo_keyword = "";
		 }
		 if($request->input('category_seo_desc') != "")
		 {
		 $category_seo_desc = $request->input('category_seo_desc');
		 }
		 else
		 {
		 $category_seo_desc = "";
		 }
		 
         $cat_id = $request->input('cat_id');
		 $request->validate([
							'category_name' => 'required',
							'category_status' => 'required',
							
         ]);
		 $rules = array(
				'category_name' => ['required', 'max:255', Rule::unique('category') ->ignore($cat_id, 'cat_id') -> where(function($sql){ $sql->where('drop_status','=','no');})],
				
	     );
		 
		 $messsages = array(
		      
	    );
		 
		$validator = Validator::make($request->all(), $rules,$messsages);
		
		if ($validator->fails()) 
		{
		 $failedRules = $validator->failed();
		 return back()->withErrors($validator);
		} 
		else
		{
		
		
		$data = array('category_name' => $category_name, 'category_slug' => $category_slug, 'category_status' => $category_status, 'menu_order' => $menu_order, 'category_allow_seo' => $category_allow_seo, 'category_seo_keyword' => $category_seo_keyword, 'category_seo_desc' => $category_seo_desc);
        Category::updatecategoryData($cat_id, $data);
        return redirect('/admin/category')->with('success', 'Update successfully.');	
            
 
       } 
     
       
	
	
	}
	
	
	/* category */
	
	
	/* subcategory */
	
	
	public function subcategory()
    {
        
		
		$subcategoryData['subcategory'] = Category::getsubcategoryData();
		if($this->custom() != 0)
	    {
		return view('admin.sub-category',[ 'subcategoryData' => $subcategoryData]);
		}
	   	else
	   	{
		  return redirect('/admin/license');
	   	}
    }
	
	
	public function add_subcategory()
	{
	   $categoryData['category'] = Category::allcategoryData();
	   if($this->custom() != 0)
	   {
	   return view('admin.add-subcategory',[ 'categoryData' => $categoryData]);
	   }
	   else
	   {
		  return redirect('/admin/license');
	   }
	}
	
	
	
	public function save_subcategory(Request $request)
	{
 
    
         $cat_id = $request->input('cat_id');
		 $subcategory_name = $request->input('subcategory_name');
		 $additional['settings'] = Settings::editAdditional();
	     if($additional['settings']->site_url_rewrite == 1)
		 {
		   $subcategory_slug = $this->seo_slug($subcategory_name);
		 }
		 else
		 {
		   $subcategory_slug = $this->non_seo_slug($subcategory_name);
		 }
		 $subcategory_status = $request->input('subcategory_status');
		 $subcategory_order = $request->input('subcategory_order');
		 $subcategory_allow_seo = $request->input('subcategory_allow_seo');
		 if($request->input('subcategory_seo_keyword') != "")
		 {
		 $subcategory_seo_keyword = $request->input('subcategory_seo_keyword');
		 }
		 else
		 {
		 $subcategory_seo_keyword = "";
		 }
		 if($request->input('subcategory_seo_desc') != "")
		 {
		 $subcategory_seo_desc = $request->input('subcategory_seo_desc');
		 }
		 else
		 {
		 $subcategory_seo_desc = "";
		 }
		 
         
		 $request->validate([
							'cat_id' => 'required',
							'subcategory_name' => 'required',
							'subcategory_status' => 'required',
							
         ]);
		 $rules = array(
				/*'subcategory_name' => ['required', 'max:255', Rule::unique('subcategory') -> where(function($sql){ $sql->where('drop_status','=','no');})],*/
				
	     );
		 
		 $messsages = array(
		      
	    );
		 
		$validator = Validator::make($request->all(), $rules,$messsages);
		
		if ($validator->fails()) 
		{
		 $failedRules = $validator->failed();
		 return back()->withErrors($validator);
		} 
		else
		{
		
		
		 
		$data = array('cat_id' => $cat_id, 'subcategory_name' => $subcategory_name, 'subcategory_slug' => $subcategory_slug, 'subcategory_status' => $subcategory_status, 'subcategory_order' => $subcategory_order, 'subcategory_allow_seo' => $subcategory_allow_seo, 'subcategory_seo_keyword' => $subcategory_seo_keyword, 'subcategory_seo_desc' => $subcategory_seo_desc);
        Category::insertsubcategoryData($data);
        return redirect('/admin/sub-category')->with('success', 'Insert successfully.');   
 
       } 
     
    
  }
  
    public function all_delete_subcategory(Request $request)
	{
	   $data = array('drop_status'=>'yes');
	   $subcat_id = $request->input('subcat_id');
	   foreach($subcat_id as $id)
	   {
	      Category::deleteSubcategorydata($id,$data);
	   }
	   return redirect()->back()->with('success','Delete successfully.');
	
	}
  
	public function delete_subcategory($subcat_id){

      $data = array('drop_status'=>'yes');
	  
        
      Category::deleteSubcategorydata($subcat_id,$data);
	  
	  return redirect()->back()->with('success', 'Delete successfully.');

    
  }
  
  
  
  public function edit_subcategory($subcat_id)
	{
	   $categoryData['category'] = Category::allcategoryData();
	   $edit['subcategory'] = Category::editsubcategoryData($subcat_id);
	   if($this->custom() != 0)
	   {
	   return view('admin.edit-subcategory', [ 'edit' => $edit, 'subcat_id' => $subcat_id, 'categoryData' => $categoryData]);
	   }
	   else
	   {
		  return redirect('/admin/license');
	   }
	}
	
	
	
	public function update_subcategory(Request $request)
	{
	
	    $cat_id = $request->input('cat_id');
		 $subcategory_name = $request->input('subcategory_name');
		 $additional['settings'] = Settings::editAdditional();
	     if($additional['settings']->site_url_rewrite == 1)
		 {
		   $subcategory_slug = $this->seo_slug($subcategory_name);
		 }
		 else
		 {
		   $subcategory_slug = $this->non_seo_slug($subcategory_name);
		 }
		 $subcategory_status = $request->input('subcategory_status');
		 $subcategory_order = $request->input('subcategory_order');
		 
		 $subcat_id = $request->input('subcat_id');
		 $subcategory_allow_seo = $request->input('subcategory_allow_seo');
		 if($request->input('subcategory_seo_keyword') != "")
		 {
		 $subcategory_seo_keyword = $request->input('subcategory_seo_keyword');
		 }
		 else
		 {
		 $subcategory_seo_keyword = "";
		 }
		 if($request->input('subcategory_seo_desc') != "")
		 {
		 $subcategory_seo_desc = $request->input('subcategory_seo_desc');
		 }
		 else
		 {
		 $subcategory_seo_desc = "";
		 }
         
		 $request->validate([
							'cat_id' => 'required',
							'subcategory_name' => 'required',
							'subcategory_status' => 'required',
							
         ]);
		 $rules = array(
				/*'subcategory_name' => ['required', 'max:255', Rule::unique('subcategory') ->ignore($subcat_id, 'subcat_id') -> where(function($sql){ $sql->where('drop_status','=','no');})],*/
				
	     );
		 
		 $messsages = array(
		      
	    );
		 
		$validator = Validator::make($request->all(), $rules,$messsages);
		
		if ($validator->fails()) 
		{
		 $failedRules = $validator->failed();
		 return back()->withErrors($validator);
		} 
		else
		{
		
		$data = array('cat_id' => $cat_id, 'subcategory_name' => $subcategory_name, 'subcategory_slug' => $subcategory_slug, 'subcategory_status' => $subcategory_status, 'subcategory_order' => $subcategory_order, 'subcategory_allow_seo' => $subcategory_allow_seo, 'subcategory_seo_keyword' => $subcategory_seo_keyword, 'subcategory_seo_desc' => $subcategory_seo_desc);
		
        Category::updatesubcategoryData($subcat_id, $data);
        return redirect('/admin/sub-category')->with('success', 'Update successfully.');    
 
       } 
     
       
	
	
	}
	
  
	/* subcategory */
	
	
}
