<?php

namespace Fickrr\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Fickrr\Http\Controllers\Controller;
use Session;
use Fickrr\Models\Blog;
use Fickrr\Models\Category;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Fickrr\Models\Settings;
use Image;
use Helper;

class BlogController extends Controller
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
	
	public function blog_category()
    {
        
		
		$categoryData['category'] = Blog::getblogcategoryData();
		if($this->custom() != 0)
	    {
		return view('admin.blog-category',[ 'categoryData' => $categoryData]);
		}
	    else
	    {
		  return redirect('/admin/license');
	    }
    }
    
	
	public function add_blog_category()
	{
	   if($this->custom() != 0)
	   {
	   return view('admin.add-blog-category');
	   }
	   else
	   {
		  return redirect('/admin/license');
	   }
	}
	
	
	
	
	
	
	public function save_blog_category(Request $request)
	{
 
    
         $blog_category_name = $request->input('blog_category_name');
		 $additional['settings'] = Settings::editAdditional();
	     if($additional['settings']->site_url_rewrite == 1)
		 {
		   $blog_category_slug = $this->seo_slug($blog_category_name);
		 }
		 else
		 {
		   $blog_category_slug = $this->non_seo_slug($blog_category_name);
		 }
		 $blog_category_status = $request->input('blog_category_status');
		 $request->validate([
							'blog_category_name' => 'required',
							'blog_category_status' => 'required',
							
         ]);
		 $rules = array(
				'blog_category_name' => ['required', 'max:255', Rule::unique('blog_category') -> where(function($sql){ $sql->where('drop_status','=','no');})],
				
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
		
		
		 
		$data = array('blog_category_name' => $blog_category_name, 'blog_category_slug' => $blog_category_slug, 'blog_category_status' => $blog_category_status);
        Blog::saveblogcategoryData($data);
        return redirect('/admin/blog-category')->with('success', 'Insert successfully.');    
 
       } 
     
    
  }
  
  public function all_delete_blog_category(Request $request)
	{
	   $data = array('drop_status'=>'yes');
	   $blog_cat_id = $request->input('blog_cat_id');
	   foreach($blog_cat_id as $id)
	   {
	      Blog::deleteBlogcategorydata($id,$data);
	   }
	   return redirect()->back()->with('success','Delete successfully.');
	
	}
  
  public function delete_blog_category($blog_cat_id){

      $data = array('drop_status'=>'yes');
	  
        
      Blog::deleteBlogcategorydata($blog_cat_id,$data);
	  
	  return redirect()->back()->with('success', 'Delete successfully.');

    
  }
  
  
  public function edit_blog_category($blog_cat_id)
	{
	   
	   $edit['category'] = Blog::editblogcategoryData($blog_cat_id);
	   if($this->custom() != 0)
	   {
	   return view('admin.edit-blog-category', [ 'edit' => $edit, 'blog_cat_id' => $blog_cat_id]);
	   }
	   else
	   {
		  return redirect('/admin/license');
	   }
	}
	
	
	
	public function update_blog_category(Request $request)
	{
	
	    $blog_category_name = $request->input('blog_category_name');
		$additional['settings'] = Settings::editAdditional();
	     if($additional['settings']->site_url_rewrite == 1)
		 {
		   $blog_category_slug = $this->seo_slug($blog_category_name);
		 }
		 else
		 {
		   $blog_category_slug = $this->non_seo_slug($blog_category_name);
		 }
		 $blog_category_status = $request->input('blog_category_status');
		 
		 
         $blog_cat_id = $request->input('blog_cat_id');
		 $request->validate([
							'blog_category_name' => 'required',
							'blog_category_status' => 'required',
							
         ]);
		 $rules = array(
				'blog_category_name' => ['required', 'max:255', Rule::unique('blog_category') ->ignore($blog_cat_id, 'blog_cat_id') -> where(function($sql){ $sql->where('drop_status','=','no');})],
				
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
		
		
		$data = array('blog_category_name' => $blog_category_name, 'blog_category_slug' => $blog_category_slug, 'blog_category_status' => $blog_category_status);
		Blog::updatecatBlogData($blog_cat_id, $data);
		return redirect('/admin/blog-category')->with('success', 'Update successfully.');  
            
 
       } 
     
       
	
	
	}
	
	
	/* category */
	
	
	
	/* posts */
	
	
	public function posts()
    {
        
		
		$postData['post'] = Blog::getpostData();
		$comments = Blog::getcountcommentData();
		if($this->custom() != 0)
	    {
		return view('admin.post',[ 'postData' => $postData, 'comments' => $comments]);
		}
	   	else
	   	{
		  return redirect('/admin/license');
	   	}
    }
	
	
	public function add_post()
	{
	   
	   
	   $catData['view'] = Blog::getpostcategoryData();
	   	if($this->custom() != 0)
	   	{
		return view('admin.add-post',[ 'catData' => $catData]);
		}
	   	else
	   	{
		  return redirect('/admin/license');
	   	}
	}
	
	
	
	
	
	public function save_post(Request $request)
	{
 
    
         $post_title = $request->input('post_title');
		 $post_short_desc = $request->input('post_short_desc');
		 $post_desc = htmlentities($request->input('post_desc'));
		 $additional['settings'] = Settings::editAdditional();
	     if($additional['settings']->site_url_rewrite == 1)
		 {
		   $post_slug = $this->seo_slug($post_title);
		 }
		 else
		 {
		   $post_slug = $this->non_seo_slug($post_title);
		 }
         $post_status = $request->input('post_status');
		 $blog_cat_id = $request->input('blog_cat_id');
		 $post_tags = $request->input('post_tags');
		 
		 
		$allsettings = Settings::allSettings();
		  $image_size = $allsettings->site_max_image_size;
		 
		 $post_allow_seo = $request->input('post_allow_seo');
		 if($request->input('post_seo_keyword') != "")
		 {
		 $post_seo_keyword = $request->input('post_seo_keyword');
		 }
		 else
		 {
		 $post_seo_keyword = "";
		 }
		 if($request->input('post_seo_desc') != "")
		 {
		 $post_seo_desc = $request->input('post_seo_desc');
		 }
		 else
		 {
		 $post_seo_desc = "";
		 }
		 $additional = Settings::editAdditional();
		 
         
		 $request->validate([
							'post_title' => 'required',
							'post_short_desc' => 'required',
							'post_desc' => 'required',
							'post_image' => 'required|mimes:jpeg,jpg,png|max:'.$image_size,
							'post_status' => 'required',
							'blog_cat_id' => 'required',
							
         ]);
		 $rules = array(
				
				
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
		
		$post_date = date('Y-m-d');
		
		if ($request->hasFile('post_image')) {
		     
				   
			$image = $request->file('post_image');
			$img_name = time() . '.'.$image->getClientOriginalExtension();
			$destinationPath = public_path('/storage/post');
			$imagePath = $destinationPath. "/".  $img_name;
			$image->move($destinationPath, $img_name);
			$img=Image::make(public_path('/storage/post/'.$img_name));
			$img->save(base_path('public/storage/post/'.$img_name),$additional->image_quality);
			$post_image = $img_name;
		  }
		  else
		  {
		     $post_image = "";
		  }
		 
		$data = array('post_title' => $post_title, 'post_slug' => $post_slug, 'post_short_desc' => $post_short_desc, 'post_image' => $post_image, 'post_desc' => $post_desc, 'post_date' => $post_date, 'post_status' => $post_status, 'blog_cat_id' => $blog_cat_id, 'post_tags' => $post_tags, 'post_allow_seo' => $post_allow_seo, 'post_seo_keyword' => $post_seo_keyword, 'post_seo_desc' => $post_seo_desc);
        Blog::insertpostData($data);
		return redirect('/admin/post')->with('success', 'Insert successfully.');
        
            
 
       } 
     
    
  }
  
	public function all_delete_post(Request $request)
	{
	   
	   $post_id = $request->input('post_id');
	   foreach($post_id as $id)
	   {
	      Blog::deletePostdata($id);
	   }
	   return redirect()->back()->with('success','Delete successfully.');
	
	}
	
	public function delete_post($post_id){

      
	  
      Blog::deletePostdata($post_id);
	  
	  return redirect()->back()->with('success', 'Delete successfully.');

    
  }
  
  
  public function edit_post($post_id)
	{
	   
	   $edit['post'] = Blog::editpostData($post_id);
	   $catData['view'] = Blog::getpostcategoryData();
	   if($this->custom() != 0)
	   {
	   return view('admin.edit-post', [ 'edit' => $edit, 'post_id' => $post_id, 'catData' => $catData]);
	   }
	   else
	   {
		  return redirect('/admin/license');
	   }
	}
	
	
	
	public function update_post(Request $request)
	{
	
	   $post_title = $request->input('post_title');
		 $post_short_desc = $request->input('post_short_desc');
		 $post_desc = htmlentities($request->input('post_desc'));
		 $additional['settings'] = Settings::editAdditional();
	     if($additional['settings']->site_url_rewrite == 1)
		 {
		   $post_slug = $this->seo_slug($post_title);
		 }
		 else
		 {
		   $post_slug = $this->non_seo_slug($post_title);
		 }
         $post_status = $request->input('post_status');
		 $blog_cat_id = $request->input('blog_cat_id');
		 
		 $save_post_image = $request->input('save_post_image');
		 $post_id = $request->input('post_id');
		 $post_tags = $request->input('post_tags');
		 
		 $allsettings = Settings::allSettings();
		  $image_size = $allsettings->site_max_image_size;
		 
         $post_allow_seo = $request->input('post_allow_seo');
		 if($request->input('post_seo_keyword') != "")
		 {
		 $post_seo_keyword = $request->input('post_seo_keyword');
		 }
		 else
		 {
		 $post_seo_keyword = "";
		 }
		 if($request->input('post_seo_desc') != "")
		 {
		 $post_seo_desc = $request->input('post_seo_desc');
		 }
		 else
		 {
		 $post_seo_desc = "";
		 }
		 $additional = Settings::editAdditional();
		 
		 
		 $request->validate([
							'post_title' => 'required',
							'post_short_desc' => 'required',
							'post_desc' => 'required',
							'post_image' => 'mimes:jpeg,jpg,png|max:'.$image_size,
							'post_status' => 'required',
							'blog_cat_id' => 'required',
							
         ]);
		 $rules = array(
				
				
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
		
		
		$post_date = date('Y-m-d');
		
		if ($request->hasFile('post_image')) 
		{
		     
			Blog::dropBlogimage($post_id);	   
			$image = $request->file('post_image');
			$img_name = time() . '.'.$image->getClientOriginalExtension();
			$destinationPath = public_path('/storage/post');
			$imagePath = $destinationPath. "/".  $img_name;
			$image->move($destinationPath, $img_name);
			$img=Image::make(public_path('/storage/post/'.$img_name));
			$img->save(base_path('public/storage/post/'.$img_name),$additional->image_quality);
			$post_image = $img_name;
		  }
		  else
		  {
		     $post_image = $save_post_image;
		  }
		 
		$data = array('post_title' => $post_title, 'post_slug' => $post_slug, 'post_short_desc' => $post_short_desc, 'post_image' => $post_image, 'post_desc' => $post_desc, 'post_date' => $post_date, 'post_status' => $post_status, 'blog_cat_id' => $blog_cat_id, 'post_tags' => $post_tags, 'post_allow_seo' => $post_allow_seo, 'post_seo_keyword' => $post_seo_keyword, 'post_seo_desc' => $post_seo_desc);
		
		
		
        Blog::updatepostData($post_id, $data);
        return redirect('/admin/post')->with('success', 'Update successfully.');
            
 
       } 
     
       
	
	
	}
	
	
	/* posts */
	
	
	/* comments */
	
	
	public function comments($post_id)
	{
	  $commentData['post'] = Blog::getcommentData($post_id);
	  if($this->custom() != 0)
	  {
	  return view('admin.comment',[ 'commentData' => $commentData]);
	  }
	  else
	  {
		  return redirect('/admin/license');
	  }
	}
	
	
	public function delete_comment($delete,$comment_id){

      
	  
      Blog::deleteCommentdata($comment_id);
	  
	  return redirect()->back()->with('success', 'Delete successfully.');

    
  }
  
  public function comment_status($status,$comment_id)
  {
     if($status == 0)
	 {
	   $status_value = 1;
	 }
	 else
	 {
	   $status_value = 0;
	 }
	 
	 $data = array( 'comment_status' => $status_value);
	 
	 Blog::updatecommentData($comment_id, $data);
     return redirect()->back()->with('success', 'Update successfully.');
  
  }
  
  
	/* comments */
	
	
	
	
	
}
