<?php

namespace Fickrr\Models;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Auth;
use Fickrr\Models\Import;
use Fickrr\Models\Items;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ImportProduct implements ToModel, WithStartRow
{
	
	
   public function startRow(): int
   {
       return 2;
   }	
   public function model(array $row)
    {
	     
	    
           $data = Items::findProduct($row[2]);
		   if($row[10] == ""){ $item_file = ""; } else { $item_file = $row[10]; }
		   if($row[16] == ""){ $item_category_parent = ""; } else { $item_category_parent = $row[16]; }
		   if($row[24] == ""){ $seller_money_back = 0; } else { $seller_money_back = $row[24]; }
		   if($row[25] == ""){ $seller_money_back_days = 0; } else { $seller_money_back_days = $row[25]; }
		   if($row[32] == ""){ $item_views = 0; } else { $item_views = $row[32]; }
		   if($row[33] == ""){ $free_download = 0; } else { $free_download = $row[33]; }
		   if($row[36] == ""){ $future_update = 0; } else { $future_update = $row[36]; }
		   if($row[37] == ""){ $item_support = 0; } else { $item_support = $row[37]; }
		   if($row[40] == ""){ $download_count = 0; } else { $download_count = $row[40]; }
		   if($row[41] == ""){ $item_flash = 0; } else { $item_flash = $row[41]; }
		   if($row[42] == ""){ $item_flash_request = 0; } else { $item_flash_request = $row[42]; }
		   if($row[48] == ""){ $item_status = 0; } else { $item_status = $row[48]; }
		   
		   
          if (empty($data)) {
          
					  return new Import([
					   'user_id'    => $row[1], 
					   'item_token' => $row[2],
					   'subscription_item' => $row[3],
					   'item_name' => $row[4],
					   'item_slug' => $row[5],
					   'item_desc' => $row[6],
					   'item_shortdesc' => $row[7],
					   'item_thumbnail' => $row[8],
					   'item_preview' => $row[9],
					   'item_file' => $item_file,
					   'file_type' => $row[11],
					   'item_file_link' => $row[12],
					   'item_delimiter' => $row[13],
					   'item_serials_list' => $row[14],
					   'item_category' => $row[15],
					   'item_category_parent' => $item_category_parent,
					   'item_category_type' => $row[17],
					   'item_type_cat_id' => $row[18],
					   'item_type' => $row[19],
					   'item_type_id' => $row[20],
					   'regular_price' => $row[21],
					   'extended_price' => $row[22],
					   'seller_refund_term' => $row[23],
					   'seller_money_back' => $seller_money_back,
					   'seller_money_back_days' => $seller_money_back_days,
					   'demo_url' => $row[26],
					   'video_preview_type' => $row[27],
					   'video_file' => $row[28],
					   'video_url' => $row[29],
					   'item_tags' => $row[30],
					   'item_liked' => $row[31],
					   'item_views' => $item_views,
					   'free_download' => $free_download,
					   'item_featured' => $row[34],
					   'item_sold' => $row[35],
					   'future_update' => $future_update,
					   'item_support' => $item_support,
					   'created_item' => $row[38],
					   'updated_item' => $row[39],
					   'download_count' => $download_count,
					   'item_flash' => $item_flash,
					   'item_flash_request' => $item_flash_request,
					   'item_allow_seo' => $row[43],
					   'item_seo_keyword' => $row[44],
					   'item_seo_desc' => $row[45],
					   'audio_file' => $row[46],
					   'drop_status' => $row[47],
					   'item_status' => $item_status,
					   'item_reviewer' => $row[49],
					   
					]);
		  
		  
              } 
     
	    
	
        
    }
   
   
  
  
}
