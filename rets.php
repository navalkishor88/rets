
<?php
$rets_login_url = 'http://greenville.rets.fnismls.com/rets/fnisrets.aspx/GREENVILLE/login?rets-version=rets/1.5';
$rets_username = 'DBweb_D';
$rets_password = 'rets_dbwg2000151';
$rets_modtimestamp_field = "LIST_87";
$previous_start_time = "1980-01-01T00:00:00";
//////////////////////////////
require_once("rets.php");
// start rets connection
$rets = new phRETS;
// only enable this if you know the server supports the optional RETS feature called 'Offset'
$rets->SetParam("offset_support", true);
echo "+ Connecting to {$rets_login_url} as {$rets_username}<br>\n";
$connect = $rets->Connect($rets_login_url, $rets_username, $rets_password);
if ($connect) {
       echo "  + Connected<br>\n";
	   
// $data_property 		= "select * from wp_property_photo";
// $de   				= mysql_query($data_property);
// while($row = mysql_fetch_array($de))
// {
// $post_id 	=	$row['post_id'];
// $img_name	=	$row['photo_name'];
// $url 		=	$_SERVER['DOCUMENT_ROOT'];
// $url.='/rets/'; /* have to remove online */
// $path1		=  "$url/property_photo/largeimage/$img_name"; 
// if (file_exists($path1)) {
   // unlink($path1);
// } 
// }


// Specify the target directory and add forward slash
$url 		=	$_SERVER['DOCUMENT_ROOT'];
$url.='/rets/'; /* have to remove online */
$path1		=  "$url/property_photo/largeimage/";
echo $path = $path1;
// Loop over all of the files in the folder
foreach(glob($path ."*.*") as $file) {
    unlink($file); // Delete each file through the loop
}

die();

$delete_post_data  			 = mysql_query("Delete from wp_posts where ID >= 1100");
$delete_post_meta_data       = mysql_query("Delete from wp_postmeta where meta_id >= 1100");
$query 			   			 = mysql_query("TRUNCATE TABLE wp_property_photo");

$listing_id_query 	=  mysql_query("SELECT * FROM wp_master_id LIMIT 0 , 200");
$l_id = 1;
while($data_listing_id = mysql_fetch_array($listing_id_query))
{
	if($l_id == 1)
	{
	$lsiting_ids = $data_listing_id['listing_id'];
	}
	else
	{
	$lsiting_ids .= ",";
	$lsiting_ids .= $data_listing_id['listing_id'];
	}
$l_id++;
}   
//echo "$lsiting_ids";
}
else {
       echo "  + Not connected:<br>\n";
       print_r($rets->Error());
       exit;
}
$classes = $rets->GetMetadataClasses("Property");
foreach ($classes as $class) {
	$data_class_name[] = $class['ClassName'];
}
//print_R($data_class_name);
//$property_classes = $data_class_name;
$i=1;
$post_id = 1100;
$property_classes = array('RE_1');
//print_R($property_classes);
foreach ($property_classes as $class) {
       echo "+ Property:{$class}<br>\n";
       $maxrows = true;
       $offset = 1;
       $limit = 1000;
       $fields_order = array();

       while ($maxrows) {
              $query = "({$rets_modtimestamp_field}={$previous_start_time}+)";
		// run RETS search
        // echo "   + Query: {$query}  Limit: {$limit}  Offset: {$offset}<br>\n";
		
		/* Take the system name / human name and place in an array */
			// $fields = $rets->GetMetadataTable("Property","RE_1");
			// $table = array();
			// foreach($fields as $field) {
			// $table[$field['SystemName']] = $field['LongName'];
			// }
			// echo "<pre>";
			// print_r($table);
			// echo "</pre>"; 
			// echo "hi"; 
			//die('field are attached from hrer');
		/* Display output */
		$search = $rets->SearchQuery("Property","RE_1","(L_ListingID=$lsiting_ids)",array('Format'    => 'COMPACT-DECODED','Select'=>'L_ListingID,L_Type_,LR_remarks22,LFD_HEATINGSYSTEM_10,LFD_GARAGEFEATURES_30,L_Keyword4,LM_Int4_1,L_Keyword1,L_Keyword2,L_City,L_Address,LFD_FLOORS_12,LM_Char10_2,L_State,L_AskingPrice,LFD_STYLE_1,LM_Char10_3,LFD_BASEMENT_3,LFD_FOUNDATION_28,LFD_LOTDESCRIPTION_2,LFD_FIREPLACE_9,LFD_EXTERIORFINISH_27,LFD_EXTERIORFEATURES_29,LFD_SEWER_14,LFD_WATER_13,LFD_AMENITIESINCLUDE_24,LR_remarks33,L_NumAcres'));
		echo "    + Total found: {$rets->TotalRecordsFound()}<br>\n";
		//die();
		while ($listing = $rets->FetchRow($search))
		{
			// echo "<pre>";
			// print_R($listing); 
			// die('hi');
	
		/* large image */
		$property_id 	=   $listing['L_ListingID'];
		/* data insert here */
		$post_title		=	$listing['L_Type_'];
		$post_content 	= 	addslashes($listing['LR_remarks22']);
		$type 			= $listing['LFD_STYLE_1'];
		$area           = $listing['LM_Char10_2'];
		$county 		= $listing['LM_Char10_3'];
		$acres			= $listing['L_NumAcres'];
		// equipemnt
				$basement   		= 	$listing['LFD_BASEMENT_3'];
				$foundation 		=   $listing['LFD_FOUNDATION_28'];
				$lot		  		= 	$listing['LFD_LOTDESCRIPTION_2'];
				$fireplace 			=   $listing['LFD_FIREPLACE_9'];
				$exteriorfinish   	= 	$listing['LFD_EXTERIORFINISH_27'];
				$exteriorfeatures 	=   $listing['LFD_EXTERIORFEATURES_29'];
				$sewer 				=   $listing['LFD_SEWER_14'];
				$water   			= 	$listing['LFD_WATER_13'];
				$amenities 			=   $listing['LFD_AMENITIESINCLUDE_24'];
		// details
		$extra_content = $listing['LR_remarks33'];
		$guid ="http://localhost/rets/?p=$post_id";
		//echo "area";
		//echo $post_content;
		//$page = array( 'ID'=>$post_id,'post_title' =>$post_title, 'post_content' => $post_content, 'post_status' => 'publish', 'post_author' => 1, 'post_type' => 'realty', 'post_parent' => 0 ,'guid'=>$guid); 
		// $query = "insert into wp_posts (ID) values ($post_id)";
		// $insert = mysql_query($query);
		// $insert = wp_update_post($page);
		
		// $array_data      		 	= $page;
		// $table              	 	= 'wp_posts';
		// $result          			= insert_query($array_data,$table) ;
		$page = array( 'ID'=>$post_id,'post_title' =>$post_title, 'post_content' => $post_content, 'post_status' => 'publish', 'post_author' => 1, 'post_type' => 'realty', 'post_parent' => 0 ); 
		$query = "insert into wp_posts (ID) values ($post_id)";
		$insert = mysql_query($query);
		$insert = wp_update_post($page);
		
		$contract =  "";
		$elevator  = "";
		$data_post_meta  = array('heat_system'=>$listing['LFD_HEATINGSYSTEM_10'],'garage'=>$listing['LFD_GARAGEFEATURES_30'],'parking'=>$listing['L_Keyword4'],'year'=>$listing['LM_Int4_1'],'badrooms'=>$listing['L_Keyword1'],'bathrooms'=>$listing['L_Keyword2'],'contract'=>$contract,'realty_city'=>$listing['L_City'],'realty_address'=>$listing['L_Address'],'floors'=>$listing['LFD_FLOORS_12'],'realty_area'=>$area,'realty_citypart'=>$county,'realty_price'=>$listing['L_AskingPrice'],'elevator'=>$elevator,'property_id'=>$property_id,'type'=>$type,'county'=>$county,'basement'=>$basement,'foundation'=>$foundation,'lot'=>$lot,'fireplace'=>$fireplace,'exteriorfinish'=>$exteriorfinish,'exteriorfeatures'=>$exteriorfeatures,'sewer'=>$sewer,'water'=>$water,'amenities'=>$amenities,'extra_content'=>$extra_content,'acres'=>$acres);
		// echo sizeOf($data_post_meta);
		// print_R($data_post_meta);
		// die();
		$npost_id = $post_id;
		$field    = 30*$i;
		$meta_id = $npost_id+$field;
		foreach($data_post_meta as $meta_key => $meta_value)
		{
			$query = "insert into wp_postmeta (meta_id,post_id,meta_key,meta_value) values ($meta_id,$post_id,'$meta_key','$meta_value')";
			$insert = mysql_query($query);
			$meta_id++;
			//update_post_meta($post_id, $meta_key, $meta_value,true);
		}
		$lphotos = $rets->GetObject("Property", "Photo", $property_id);
		//print_R($lphotos);
		//die();
		$photo_count =1;
		foreach ($lphotos as $photo) {
		if ($photo['Success'] == true) {
				// echo "<pre>".print_r($photo, true)."</pre>";
				// die();
		$url 	=   $_SERVER['DOCUMENT_ROOT'].'/rets/property_photo';
		//$url 	=   $_SERVER['DOCUMENT_ROOT'].'/property_photo';
		file_put_contents("$url/largeimage/{$photo['Content-ID']}-{$photo['Object-ID']}.jpg", $photo['Data']);
		$property_photo_name		= $photo['Content-ID']."-".$photo['Object-ID'].".jpg";
		$array_data      		 	= array('post_id'=>$post_id,'photo_name'=>$property_photo_name);
		$table              	 	= 'wp_property_photo';
		$result          			= insert_query($array_data,$table) ;
		}
		/* large image */
		/* data insertion end here */
		}
		//die('die only with 1st post');
		echo $post_id;
			if($post_id>=1103)
			{
				die('Everything has been died');
			}
			$query = "insert into wp_ivalue (id) values ($i)";
			$insert = mysql_query($query);
			$i++;
			$post_id++;
		}
		
$maxrows = $rets->IsMaxrowsReached();
echo "    + Total found: {$rets->TotalRecordsFound()}<br>\n";
               $rets->FreeResult($search);
       }
       fclose($fh);
       echo "  - done<br>\n";
}

echo "+ Disconnecting<br>\n";
$rets->Disconnect();
