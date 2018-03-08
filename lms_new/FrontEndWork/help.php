<?php
	session_start();
	require_once 'Library.php';
	require_once 'attendenceFunctions.php';
	require_once 'generalFunctions.php';
	error_reporting("E_ALL");
	$db=connectToDB();
?>
<html>
	<body>
		<div class="panel panel-primary">
			<div class="panel-heading text-center">
				<strong style="font-size:20px;">ECI Leave Management System FAQ</strong>
			</div>
			<div class="panel-body">
			<div class="panel-group" id="accordion">
				<div class="panel panel-info"><!-- login details panel info start -->
			        <div class="panel-heading accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" data-target="#collapseOne">
			            <h4 class="panel-title">How to login into ECI Leave management system?</h4>
					</div>
			        <div id="collapseOne" class="panel-collapse collapse">
			            <div class="panel-body">
			              	<strong>The steps involved in login into ECI Leave Management System:</strong>
								<ul>
									<li>One can login into this web app using<strong> windows credentials</strong>.</li>
									<li><strong>ECI_DOMAIN </strong>is not needed in the username field.</li>
									<li><strong>LMS link will work</strong> only when you are in the ECI Domin.</li>
								</ul>
								<img src="public/HelpImage/login.jpg" class="img-responsive" alt="ECI Login" align="middle" height="50%" width="70%">
			            </div>
			         </div>
			    </div><!-- login details panel info closed -->
				
			    <div class="panel panel-info"><!-- employee can do with LMS panel info start -->
			       	<div class="panel-heading accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" data-target="#collapseTwo">
			            	<h4 class="panel-title">What an employee can do with LMS?</h4>
					</div>
			        <div id="collapseTwo" class="panel-collapse collapse">
			            <div class="panel-body">
							<strong>An employee can:</strong>
							<ul>
								<li>Apply Leave.</li>
								<li>View ECI Holidays.</li>
								<li>View his access details.</li>
								<li>View his team member's PTO.</li>
								<li>View his leave history.</li>
								<li>Modify his Pending leaves before approval.</li>
							</ul>
			            </div>
			        </div>
			    </div><!-- employee can do with LMS panel info end -->
				
			    <div class="panel panel-info"><!-- type of leaves panel info start -->
					<!--panel hedaing start-->
			        <div class="panel-heading accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" data-target="#collapseThree">
			            <h4 class="panel-title">What type of leaves an employee can apply in LMS?</h4>
					</div><!--panel heading close-->
			        <div id="collapseThree" class="panel-collapse collapse">
			            <div class="panel-body"><!--panel body start-->
							<ol>	
								<li><Strong>Regular Leave:</Strong><br>
									<ul>
										<li>Full Day PTO</li>
										<li>Half Day PTO (First Half or Second Half)</li>
										<li>WFH (First Half or Second Half or FullDay)</li>
										<li>First Half - Half Day PTO and second Half - WFH</li>
										<li>First Half - WFH and Second Half - Half Day PTO</li> 	
									</ul>
									<br>
								</li>
								
								<li><strong>Special Leave:</strong><br>
									<p>An employee can select special leave as mentioned below:</p>
									<ul>
										<li>The corresponding PTO's for each leave type will not be deducted from the total 
														leave balance of the employee.</li>
										<li>These are the extra leaves provided by the company.</li>
										<li>Once employee uses the special leave and got approval from manager, he can't use 
														the same special leave another time.</li>
										<li>Once the leave is approved, that option will 
														be removed from the selection box.</li>	
									</ul><br>
									<table class="table table-bordered " style="width:70%;">
										<tr>
											<th>Leave Type</th>
											<th>PTO Days Permitted</th>
										</tr>
										<tr>
											<td>Wedding</td>
											<td class="align">4</td>
										</tr>
										<tr>
											<td>Paternity Leave</td>
											<td class="align">2</td>
										</tr>
										<tr>
											<td>Death of spouse or life companion</td>
											<td class="align">4</td>
										</tr>
										<tr>
											<td>Death of immediate family member</td>
											<td class="align">3</td>
										</tr>
									</table>
								</li>
							</ol>
			            </div><!--panel body close-->
			        </div>
			    </div><!-- type of leaves panel info end -->
				
			    <div class="panel panel-info"><!--apply a Regular PTO panel info start-->
					<!--panel heading start-->	
			        <div class="panel-heading accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" data-target="#collapseFour">
			            <h4 class="panel-title">How to apply a Regular PTO?</h4>
					</div><!--panel heading close-->
			        <div id="collapseFour" class="panel-collapse collapse">
			            <div class="panel-body"><!--panel body start--> 
			            	<ol>
								<li>	
									Click on <b>Apply leave</b> in home page. A page appears on <b>right side</b>, where one can select the <b>date and specify reason</b>. Then click on next button.
								<br>
								<img src="public/HelpImage/applyleave.jpg" class="img-responsive" alt="ECI Login" align="middle" height="50%" width="70%">
								</li> 
															
								<li>In the next page, one can select PTO types as shown below:
									<br><img src="public/HelpImage/appliedLeave.jpg" class="img-responsive" alt="ECI Login" height="50%" width="80%">
								</li>
								<li>
									In the next page, one can select First Half or second half based on the <b>PTO selected</b> in 
									the previous page. Then click on <b>"Apply and Send Mail"</b> button to apply the selected PTO's 
									and the same will be <b>mailed</b> to the manager and the employee.
									<br><img src="public/HelpImage/shiftType.jpg" class="img-responsive" alt="ECI Login" height="50%" width="85%">
								</li>
							</ol>
			            </div><!--panel body close-->
			        </div>
			    </div><!--apply a Regular PTO panel info close-->
				
			    <div class="panel panel-info"><!--special leave panel info start-->
				<!--panel heading start-->
					<div class="panel-heading accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" data-target="#collapseFive">
			            <h4 class="panel-title">How to apply a Special Leave?</h4>
					</div><!--panel heading close-->
			        <div id="collapseFive" class="panel-collapse collapse">
						<div class="panel-body">
						<!--panel body start-->
			            	<ol>
								<li>Click on <b>Apply leave</b> on left plane, and select <b>"special Leave"</b> from right plane. Then 
									select special leave type and click next.
									<img src="public/HelpImage/specialLeave.jpg" class="img-responsive" alt="ECI Login" height="70%" width="90%">
								</li>
								<li>In next page, the <b>special leave </b>will be populated as shown below:
									<br><img src="public/HelpImage/splLeave.jpg" class="img-responsive" alt="ECI Login" height="70%" width="90%">
								</li>
								<li>
									Here <b>"Wedding Leave"</b> is selected. So, first <b>four dates</b> are populated with Wedding Leave 
									(as specified in the table above) and the remaining days, we can select <b>any regular leave type </b>
									and click "next" button.
								</li>
							</ol>
			            </div><!--panel body close-->
			        </div>
			    </div><!--special leave panel info end-->
				
			    <div class="panel panel-info"><!--carry forward leave panel info start-->
					<!--panel heading start-->
			        <div class="panel-heading accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" data-target="#collapseSix">
			            <h4 class="panel-title">How to see carry forwarded leaves for previous year and balance leaves for present year?</h4>
					</div><!--panel heading close-->
			        <div id="collapseSix" class="panel-collapse collapse">
						<div class="panel-body"><!--panel body start-->
			            	One can see his <b>balance leaves</b> and <b>carry forwarded leaves</b> information by clicking on <b>"Balance Leaves"</b>
							option in top right corner.<br>
							<img src="public/HelpImage/carryForward.jpg" class="img-responsive" alt="ECI Login" align="middle" height="70%" width="70%">
			            </div><!--panel body end-->
			        </div>
			    </div><!--carry forward leave panel info close-->
			    <?php 
					if(strtoupper($_SESSION['user_dept'])=="HR") {?>
			    <div class="panel panel-info"><!--hr section panel info start-->
					<!--panel hading start-->
			        <div class="panel-heading accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" data-target="#collapseEight">
			            <h4 class="panel-title">HR Section?</h4>
					</div><!--panel heading end-->
			        <div id="collapseEight" class="panel-collapse collapse">
						<div class="panel-body"><!--panel body start-->
			            	<b>HR will have access to the following tasks in HR section in left plane.</b><br>
							<ol>
								<li>HR can add/modify/delete/view employee details.</li>
								<li>HR can add/modify/delete/view department details.</li>
								<li>Apply leave for any employee in the company.</li>
								<li>Modify "Approved Leaves" for any employee in the company.</li>
								<li>Can take print out of PTO report for the whole team.
								<br><img src="public/HelpImage/hrsection.jpg" class="img-responsive" alt="ECI Login" align="middle" height="70%" width="70%"></li>
							</ol>	
			            </div><!--panel body close-->
			        </div>
			    </div><!--hr section panel info close-->
				<?php }elseif(strtoupper($_SESSION['user_desgn'])=="MANAGER") {?>
			    <div class="panel panel-info"><!--manager section panel info start-->
					<!--panel heading start-->
			       	<div class="panel-heading accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" data-target="#collapseSeven">
			            <h4 class="panel-title">Manager Section?</h4>
					</div><!--panel heading end-->
			        <div id="collapseSeven" class="panel-collapse collapse">
			            <div class="panel-body"><!--panel body start-->
			            	<b> A manager can perform the following additional tasks along with the tasks specified above, when he enters into LMS.</b><br>
							<ol>
								<li>View his team member's access details along with his/her access details (in attendance section)</li>
								<li>Approve/reject his team member's PTO request (By clicking on "Leave Approval" in left plane)</li>
								<li>Can modify his/her team member's "Approved PTO's" (By clicking on "Manager Section" in left plane).</li>
								<li>Can apply leave for his team member's on behalf of his team member.(By clicking on "Apply Leave For Team" in left plane)</li>
								<li>Can view his peers PTO's along with his team member's PTO's (By clicking on "Leave Calendar" in top plane)</li>
							</ol>
							<img src="public/HelpImage/managerjob.jpg" class="img-responsive" alt="ECI Login" height="70%" width="90%">
			            </div><!--panel body close-->
			        </div>
			    </div><!--manager section panel info close-->
			    <?php }?>
			    <div class="panel panel-info"><!--voe form panel info start-->
					<!--panel heading start-->
			        <div class="panel-heading accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" data-target="#collapseNine">
			            <h4 class="panel-title">VOE Form?</h4>
					</div><!--panel heading close-->
			        <div id="collapseNine" class="panel-collapse collapse">
			            <div class="panel-body"><!--panel body start-->
							<b>Procedure for applying VOE:</b><br>
							<ol>
								<li>An employee can check their claim period for previous months by changing the claim period.<br><img src="HelpImages/voe.jpg" alt="ECI Login" height="90%" width="90%"></li>
								<li>In VOE, claim period field will show only current month and previous two month </li>
								<li>Add Day button will work only for previous month.
									<br><img src="public/HelpImage/voe2.jpg" class="img-responsive" alt="ECI Login" height="90%" width="90%"></li>
								<li>Days which are added by user will show with different background colour</li>
								<li>An employee can add a day only when he forget's his id card or when access machine is not functional</li>
								<li>Day which you want add using ADD DAY button should not be  a leave or WFH</li>
								<li>After submission of VOE form we cannot edit details but if we can delete and again we can submit the details.</li>
								<li>After submission only you will get the print,delete options.
									<br><img src="public/HelpImage/voe3.jpg" class="img-responsive" alt="ECI Login" height="90%" width="90%"></li>
							</ol>	
			            </div><!--panel body close-->
			        </div>
			    </div><!--voe form panel info close-->
				
			    <div class="panel panel-info"><!--comp off panel info start-->
					<!--panel heading start-->
			       	<div class="panel-heading accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" data-target="#collapseTen">
			           	<h4 class="panel-title">Comp Off Leave?</h4>
					</div><!--panel heading close-->
			        <div id="collapseTen" class="panel-collapse collapse">
			           	<div class="panel-body"><!--panel body start-->
			            	<b>Employee can apply for Comp Off leave:</b>
							<br><img src="public/HelpImage/applycompoff.jpg" class="img-responsive" alt="ECI Login" height="60%" width="90%">
							<ol>
								<li>An employee need to mention the worked holiday date to take the compoff leave.
								<br><img src="public/HelpImage/compoff1.jpg" class="img-responsive" alt="ECI Login" height="60%" width="90%"></li>
								<li>By clicking on "Add Comp Off leaves" button we can apply more compoff leaves.
								<br><img src="public/HelpImage/compoff2.jpg" class="img-responsive" alt="ECI Login" height="60%" width="90%"></li>
								<li>An employee can not modify the applied compoff leave but he can delete it. 	
								<br><img src="public/HelpImage/compoff3.jpg" class="img-responsive" alt="ECI Login" height="60%" width="90%"></li>
							</ol>	
			            </div><!--panel body close-->
			        </div>
			    </div><!--comp off panel info close-->
			     <div class="panel panel-info"><!--team member's PTO panel info start-->
					<!--panel heading start-->
			       	<div class="panel-heading accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" data-target="#collapseEleven">
			           	<h4 class="panel-title">How to see team member's PTO?</h4>
					</div><!--panel heading close-->
			        <div id="collapseEleven" class="panel-collapse collapse">
			           	<div class="panel-body"><!--panel body start-->
			           	<ul>
			            	<li>One can see team member's PTO by clicking on leave calendar option in the top plane:</li>
						 </ul>
						 <br><img src="public/HelpImage/leavecalendar.jpg" class="img-responsive" alt="ECI Login" height="60%" width="90%">
			            </div><!--panel body close-->
			        </div>
			    </div><!--team member's PTO panel info close-->
			     <div class="panel panel-info"><!--Leave History panel info start-->
					<!--panel heading start-->
			       	<div class="panel-heading accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" data-target="#collapseTwelve">
			           	<h4 class="panel-title">How to see Leave History?</h4>
					</div><!--panel heading close-->
			        <div id="collapseTwelve" class="panel-collapse collapse">
			           	<div class="panel-body"><!--panel body start-->
			           		<ul>
			            	<li>One can see their own leave history by clicking on leave history option in the left plane:</li>
							</ul>
							<br><img src="public/HelpImage/leavehistory.jpg" class="img-responsive" alt="ECI Login" height="70%" width="90%">
			            </div><!--panel body close-->
			        </div>
			    </div><!--Leave History panel info close-->
			    <div class="panel panel-info"><!--Team Leave Report panel info start-->
					<!--panel heading start-->
			       	<div class="panel-heading accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" data-target="#collapseThirteen">
			           	<h4 class="panel-title">How to see Team Leave Report?</h4>
					</div><!--panel heading close-->
			        <div id="collapseThirteen" class="panel-collapse collapse">
			           	<div class="panel-body"><!--panel body start-->
			           		<?php
							if(strtoupper($_SESSION['user_dept'])=="HR") {?>
			           		<ul>
			            	<li>HR can see their own leave history by clicking on team Report option in the left plane:</li>
							</ul>
							<br><img src="public/HelpImage/hrteamreport.jpg" class="img-responsive" alt="ECI Login" height="70%" width="90%">
							<?php }elseif(strtoupper($_SESSION['user_desgn'])=="MANAGER") {?>
							<ul>
			            	<li>Manager can see their own leave history by clicking on team Report option in the left plane:</li>
							</ul>
							<br><img src="public/HelpImage/managerteamreport.jpg" class="img-responsive" alt="ECI Login" height="70%" width="90%">
							<?php }?>
			            </div><!--panel body close-->
			        </div>
			    </div><!--Team Leave Report panel info close-->
			     <div class="panel panel-info"><!--Leave History panel info start-->
					<!--panel heading start-->
			       	<div class="panel-heading accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" data-target="#collapseForteen">
			           	<h4 class="panel-title">How to generate Report?</h4>
					</div><!--panel heading close-->
			        <div id="collapseForteen" class="panel-collapse collapse">
			           	<div class="panel-body"><!--panel body start-->
			           	
			           		<?php
							if(strtoupper($_SESSION['user_dept'])=="HR") {?>
							<ul>
			            	<li>HR can generate report for a particular employee/Sub department based/ Main department based/All Employee by clicking on respective radio button in generate report option in the left plane:</li>
							</ul>
							<img src="public/HelpImage/hrgenerateReport.jpg" class="img-responsive" alt="ECI Login" height="70%" width="90%">
							<?php }elseif(strtoupper($_SESSION['user_desgn'])=="MANAGER" || strtoupper($_SESSION['user_desgn'])=="USER") {?>
							<ul>
							<li>One can generate their own report by clicking on generate report option in the left plane:</li>
							</ul>
							<br><img src="public/HelpImage/generateManagerReport.jpg" class="img-responsive" alt="ECI Login" height="70%" width="90%">
							<?php }?>
			            </div><!--panel body close-->
			        </div>
			    </div><!--Leave History panel info close-->
			   	<div class="panel panel-info"><!--Employee Profile Picture panel info start-->
					<!--panel heading start-->
			       	<div class="panel-heading accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" data-target="#collapseFifteen">
			           	<h4 class="panel-title">How to update Profile Picture?</h4>
					</div><!--panel heading close-->
			        <div id="collapseFifteen" class="panel-collapse collapse">
			           	<div class="panel-body"><!--panel body start-->
			           		<ul>
			            	<li>One can update their profile picture by hover mouse over the image, camera will appear, by clicking on camera one can select any image for profile picture:</li>
							</ul>
							<br><img src="public/HelpImage/profilepicture.jpg" class="img-responsive" alt="ECI Login" height="70%" width="90%">
			            </div><!--panel body close-->
			        </div>
			    </div><!--Employee Profile Picture panel info close-->
			    <div class="panel panel-info"><!--Employee info panel info start-->
					<!--panel heading start-->
			       	<div class="panel-heading accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" data-target="#collapseSixteen">
			           	<h4 class="panel-title">How to view Personal and Official Info?</h4>
					</div><!--panel heading close-->
			        <div id="collapseSixteen" class="panel-collapse collapse">
			           	<div class="panel-body"><!--panel body start-->
			           		<ul>
			            	<li>One can view their personal and official info and update their personal info:</li>
							</ul>
							<br><img src="public/HelpImage/empinfo.jpg" class="img-responsive" alt="ECI Login" height="70%" width="90%">
			            </div><!--panel body close-->
			        </div>
			    </div><!--Employee info panel info close-->
			</div><!--panel group div close-->
		</div>
		</div>
	</body>
</html>