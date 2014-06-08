<?php 
	global $user_ID; 
    global $wpdb;
    	include (dirname(__FILE__) ."/../app/casting.class.php");
 

// GET HEADER  
	echo $rb_header = RBAgency_Common::rb_header();

				$Job_ID = ""; 
			 	$Job_Title = ""; 
				$Job_Text = "";
				$Job_Date_Start = "";
				$Job_Date_End = "";
				$Job_Location = "";
				$Job_Region = "";
				$Job_Offering = "";
				$Job_Talents = "";
				$Job_Visibility = "";
				$Job_Criteria = "";
				$Job_Type = "";
				$Job_Talents_Hash = "";	
				$Job_Audition_Date_Start = "";
				$Job_Audition_Date_End = "";
				$Job_Audition_Venue = "";
				$Job_Audition_Time = "";
		  
	            $castingcartJobHash = get_query_var("target");
	            $castingcartProfileHash = get_query_var("value");


           if(isset($castingcartJobHash)){
		 	
		 	$sql =  "SELECT * FROM ".table_agency_casting_job." WHERE Job_Talents_Hash= %s ";
		 	$data = current($wpdb->get_results($wpdb->prepare($sql, $castingcartJobHash)));

		 	if(!empty($data)){
		 		$Job_ID = $data->Job_ID; 
			 	$Job_Title = $data->Job_Title; 
				$Job_Text = $data->Job_Text;
				$Job_Date_Start = $data->Job_Date_Start;
				$Job_Date_End = $data->Job_Date_End;
				$Job_Location = $data->Job_Location;
				$Job_Region = $data->Job_Region;
				$Job_Offering = $data->Job_Offering;
				$Job_Talents = $data->Job_Talents;
				$Job_Visibility = $data->Job_Visibility;
				$Job_Criteria = $data->Job_Criteria;
				$Job_Type = $data->Job_Type;
				$Job_Talents_Hash = $data->Job_Talents_Hash;	
				$Job_Audition_Date_Start = $data->Job_Audition_Date_Start;
				$Job_Audition_Date_End = $data->Job_Audition_Date_End;
				$Job_Audition_Venue = $data->Job_Audition_Venue;
				$Job_Audition_Time = $data->Job_Audition_Time;

		   }

		 }
		

	
	echo "<div id=\"container\" class=\"one-column\">\n";
	echo "    <div id=\"content\" role=\"main\" class=\"transparent\">\n";
	  
	 		$has_permission = explode(",",$Job_Talents_Hash);

	 	    $profile_access_id = current($wpdb->get_results("SELECT * FROM ".table_agency_castingcart_profile_hash." WHERE CastingProfileHash = '".$castingcartProfileHash."' AND CastingProfileHashJobID ='".$castingcartJobHash."' ",ARRAY_A));

	 	    $query = "SELECT  profile.*,media.* FROM ". table_agency_profile ." profile, ". table_agency_profile_media ." media WHERE profile.ProfileID = media.ProfileID AND media.ProfileMediaType = \"Image\" AND media.ProfileMediaPrimary = 1 AND profile.ProfileID = %d ORDER BY profile.ProfileContactNameFirst ASC";
			
			 $data = current($wpdb->get_results($wpdb->prepare($query,$profile_access_id["CastingProfileHashProfileID"]), ARRAY_A));

			
    	if(isset($_POST["action"]) && $_POST["action"] == "availability"){
    			    $availability = "available";
    			    $Availability = "Available";
    				if($_POST["availability"] == "No, not Available"){
    					$availability = "notavailable";
    					$Availability = "Not Available";
    				}
    				  $query = "INSERT INTO ".table_agency_castingcart_availability." (CastingAvailabilityProfileID, CastingAvailabilityStatus, CastingAvailabilityDateCreated, CastingJobID)
							SELECT * FROM (SELECT '".$data["ProfileID"]."','".esc_attr($availability)."','".date("y-m-d h:i:s")."','".$Job_ID."') AS tmp
							WHERE NOT EXISTS (
							    SELECT CastingAvailabilityProfileID, CastingJobID FROM ".table_agency_castingcart_availability." WHERE CastingAvailabilityProfileID='".$data["ProfileID"]."' AND CastingJobID='".$Job_ID."'
							) LIMIT 1;"; 
					  
					   $wpdb->query($query);

					   $link = admin_url("admin.php?page=rb_agency_castinbjobs&action=informTalent&Job_ID=".$Job_ID);
					   
					   RBAgency_Casting::sendEmailCastingAvailability($data["ProfileContactDisplay"],$Availability,$Job_Title,$link);
					
					   echo ('<div id="message" class="updated"><p>Submitted successfully!</p></div>');
		       
		}
		   		   $result = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".table_agency_castingcart_availability." WHERE CastingJobID = %d AND CastingAvailabilityProfileID = %d",$Job_ID,$data["ProfileID"]));
		    $has_submitted = $wpdb->num_rows;

		  
           /*var_dump($has_permission);
            var_dump($data["ProfileID"])*/
			// if( in_array($data["ProfileID"], $has_permission) || current_user_can( 'manage_options' )  ){

				
			 ?>
		     <form method="post" action="" style="width: 900px;">
			  <h2>You have been submitted for a job</h2>
			  <strong>We are simply confirming that you are "Available" or "Not Available" for the job dates.</strong>
			  <div style="clear:both;"></div>

		     <div style="width:20%;float:left;margin-top:20px;margin-right:30px;padding-bottom:30px;">
		        <div style="width:100%;height:220px;padding:10px;text-align:center;background:#ccc;overflow:hidden;">
		        <?php if(!empty($data['ProfileMediaURL'])):?>
		        <?php echo "<img style=\"width: 100%;height:99.9% \" src=\"". get_bloginfo("url")."/wp-content/plugins/rb-agency/ext/timthumb.php?src=".rb_agency_UPLOADDIR . $data["ProfileGallery"] ."/". $data['ProfileMediaURL'] ."&w=180&h=220\" />"; ?>
		        <?php else:?>
		        		No Image Available.
		        <?php endif; ?>
		        </div>
		        <h2><?php echo $data["ProfileContactNameFirst"]." ".$data["ProfileContactNameLast"] ?></h2>	
		       
		     </div>    
		      <table style="margin-top:20px;">
		      <?php  if(empty($has_submitted)){ // if not submitted ?>
      				<tr>
		            <td>
		      		<input type="submit" name="availability" value="Yes, Available" class="button-primary"/>
		      		</td>
		      		<td>
		         	<input type="submit" name="availability" value="No, not Available" class="button-primary" />
		         	</td>
		         	</tr>
		       <?php }else{ ?>
		      		<tr>
		            <td style="text-align:right;padding-right:20px;">
		      			Availability
		      		</td>
		      		<td>
		         	 <?php 
						       $query = "SELECT CastingAvailabilityStatus as status FROM ".table_agency_castingcart_availability." WHERE CastingAvailabilityProfileID = %d AND CastingJobID = %d";
							$prepared = $wpdb->prepare($query,$data["ProfileID"],$Job_ID);
						$availability = current($wpdb->get_results($prepared));
						      $count2 = $wpdb->num_rows;
							if($count2 <= 0){
								echo "<span style=\"text-align:center;color:#5505FF;font-weight:bold;width:80%;padding:10px;display:block;\">Unconfirmed</span>\n";
							}else{
								if($availability->status == "available"){
									echo "<span style=\"text-align:center;color:#2BC50C;font-weight:bold;width:80%;padding:10px;display:block;\">Available</span>\n";
								}else{
									echo "<span style=\"text-align:center;color:#EE0F2A;font-weight:bold;width:80%;padding:10px;display:block;\">Not Available</span>\n";
								}
							}
					?>
		         	</td>
		         	</tr>
		       <?php } ?>
		        <?php if(!empty( $Job_Title )):?>
			      	<tr>
			      	<td style="text-align:right;padding-right:20px;">Job Title:</td>
			      	<td><?php echo $Job_Title; ?></td>
			      	</tr>
		      	<?php endif;?>
		      	<?php if(!empty( $Job_Type )):?>
			      	<tr>
			      	<td style="text-align:right;padding-right:20px;">Job Type:</td>
			      	<td>
			      	<?php $get_job_type = $wpdb->get_results("SELECT * FROM " . table_agency_casting_job_type); // or die(mysql_error()
									if(count($get_job_type)){
										foreach($get_job_type as $jtype){
											if($jtype->Job_Type_ID == $Job_Type){
												echo $jtype->Job_Type_Title;
											}
										}
									}
					?>				
			      	</td>
			      	</tr>
				<?php endif;?>
		      	 <?php if(!empty($Job_Date_Start)):?>
			      	<tr>
			      	<td style="text-align:right;padding-right:20px;">Job Date Start:</td>
			      	<td><?php echo date("M d, Y",strtotime($Job_Date_Start)); ?></td>
			      	</tr>
			     <?php endif;?>
			     	<?php if(!empty($Job_Date_End)):?>
			      	<tr>
			      	<td style="text-align:right;padding-right:20px;">Job Date End:</td>
			      	<td><?php echo date("M d, Y",strtotime($Job_Date_End)); ?></td>
			      	</tr>
			     <?php endif;?>

		      	
		      	<?php if(!empty($Job_Audition_Date_Start)):?>
			      	<tr>
			      	<td style="text-align:right;padding-right:20px;">Audition Date Start:</td>
			      	<td><?php echo date("M d, Y",strtotime($Job_Audition_Date_Start)); ?></td>
			      	</tr>
			     <?php endif;?>
			     	<?php if(!empty($Job_Audition_Date_End)):?>
			      	<tr>
			      	<td style="text-align:right;padding-right:20px;">Audition Date End:</td>
			      	<td><?php echo date("M d, Y",strtotime($Job_Audition_Date_End)); ?></td>
			      	</tr>
			     <?php endif;?>
			     <?php if(!empty($Job_Audition_Venue)):?>
			      	<tr>
			      	<td style="text-align:right;padding-right:20px;">Auditon Venue:</td>
			      	<td><?php echo $Job_Audition_Venue; ?></td>
			      	</tr>
			      <?php endif;?>
			     <?php if(!empty($Job_Audition_Time)):?>
				    <tr>
			      	<td style="text-align:right;padding-right:20px;">Audition Time</td>
			      	<td><?php echo $Job_Audition_Time; ?></td>
			      	</tr>
			     <?php endif;?>
			    
			    

			      <?php if(!empty($Job_Offering)):?>
				    <tr>
			      	<td  style="text-align:right;padding-right:20px;">Role Fee($)</td>
			      	<td><?php echo $Job_Offering; ?></td>
			      	</tr>
			     <?php endif;?>
			     
			     <?php if(!empty($Job_Text)):?>
			     	<tr>
			      	<td  style="text-align:right;padding-right:20px;">Description</td>
			      	<td><?php echo $Job_Text; ?></td>
			      	</tr>
			     <?php endif;?>

			      <?php if(!empty($Job_Location)){?>
			      	<tr>
			      	<td  style="text-align:right;padding-right:20px;vertical-align: top;">Location:</td>
			      	<td style="width:600px;"><?php echo $Job_Location; ?><br/>
			      	
					      	 <strong>Location Map</strong>
					      	  <?php echo do_shortcode("[pw_map address='". $Job_Location."']"); ?>
					      	
			      	</td>
			      	</tr>
			     <?php }?>
			   
		      </table>
		      <input type="hidden" name="action" value="availability">
		      </form>
			 <?php 
			/*}else{
				echo "You're not allowed to view this Job.";
			}*/

	
	echo "  </div>\n";
	echo "</div>\n";

	echo $rb_footer = RBAgency_Common::rb_footer(); 

	?>