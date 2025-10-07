<?php

namespace Fickrr\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Fickrr\Http\Controllers\Controller;
use Fickrr\Models\Settings;
use Fickrr\Models\Items;
use Fickrr\Models\Members;
use Fickrr\Models\Pages;
use Fickrr\Models\Blog;
use Helper;

class ReportsController extends Controller
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
	public function search_report(Request $request)
	{
	
	  
	  $from_date = $request->input('from_date'); 
	  $to_date = $request->input('to_date'); 
	  
			if($from_date != "" || $to_date != "")
			{
			
			   $visitorcount = Members::SelectedVisitors($from_date,$to_date);
			   $totalvendor = Members::SelectedMembers($from_date,$to_date);
			   $totalitems = Items::SelectedItems($from_date,$to_date);
			   $successorder = Items::SelectedsuccessOrder($from_date,$to_date);
			   $failedorder = Items::SelectedfailedOrder($from_date,$to_date);
			   if(!empty($successorder))
				{   
					$totalamount = 0;
					foreach(Items::selectedtotalAmount($from_date,$to_date) as $count)
					{
					   $totalamount += $count->total;
					}
				
				}
				else
				{
				  $totalamount = 0;
				}
				$totalnewvendor = Members::Selectedtotalnewvendors($from_date,$to_date);
				$totalnewitems = Items::Selectedtotalnewitems($from_date,$to_date);
				$totalorder = Items::Selectedtotalorderdata($from_date,$to_date);
				$totalneworder = Items::Selectedtotalneworder($from_date,$to_date);
			
			}
			else
			{
				$visitorcount = Members::Visitors();
				$totalvendor = Members::getmemberData();
				$totalitems = Items::totalitemCheck();
				$successorder = Items::totalsuccessOrder();
				$failedorder = Items::totalfailedOrder();
				if(!empty($successorder))
				{   
					$totalamount = 0;
					foreach(Items::totalAmount() as $count)
					{
					   $totalamount += $count->total;
					}
				
				}
				else
				{
				  $totalamount = 0;
				}
				$totalnewvendor = Members::totalnewVendors();
				$totalnewitems = Items::totalnewItems();
				$totalorder = Items::totalorderData();
				$totalneworder = Items::totalnewOrder();
				
			}
	  
	  
	  if($this->custom() != 0)
	  {
	  return view('admin.reports', [ 'visitorcount' => $visitorcount, 'totalvendor' => $totalvendor, 'totalitems' => $totalitems, 'successorder' => $successorder, 'failedorder' => $failedorder, 'totalamount' => $totalamount, 'from_date' => $from_date, 'to_date' => $to_date, 'totalnewvendor' => $totalnewvendor, 'totalnewitems' => $totalnewitems, 'totalorder' => $totalorder, 'totalneworder' => $totalneworder]);
	  }
	  else
	  {
		  return redirect('/admin/license');
	  }
	
	}
	
    public function view_reports()
    {
        
		
		
		$visitorcount = Members::Visitors();
		$totalvendor = Members::getmemberData();
		$totalitems = Items::totalitemCheck();
		$successorder = Items::totalsuccessOrder();
		$failedorder = Items::totalfailedOrder();
		if(!empty($successorder))
		{   
		    $totalamount = 0;
			foreach(Items::totalAmount() as $count)
			{
			   $totalamount += $count->total;
			}
		
		}
		else
		{
		  $totalamount = 0;
		}
		
		
		$from_date = "";
		$to_date = "";
		$totalnewvendor = Members::totalnewVendors();
		$totalnewitems = Items::totalnewItems();
		$totalorder = Items::totalorderData();
		$totalneworder = Items::totalnewOrder();
		if($this->custom() != 0)
	    {
		return view('admin.reports', [ 'visitorcount' => $visitorcount, 'totalvendor' => $totalvendor, 'totalitems' => $totalitems, 'successorder' => $successorder, 'failedorder' => $failedorder, 'totalamount' => $totalamount, 'from_date' => $from_date, 'to_date' => $to_date, 'totalnewvendor' => $totalnewvendor, 'totalnewitems' => $totalnewitems, 'totalorder' => $totalorder, 'totalneworder' => $totalneworder]);
		}
		else
		{
			  return redirect('/admin/license');
		}
		
		
    }
}
