$('#loadingmessage').html("<center><img src='public/images/spinload.jpg' class='img-responsive' style='margin-top: 10px;'/></center>");
$(window).load(function() {
    $('#loadingmessage').hide();
 });
$('document').ready(function() {
	//$("#loadempleavestatus").load('selfleavestatus.php');
	$("#loadmyprofile").load('userDashboard.php');
	//$("#loadbalanceleavesid").load('balanceleaves.php?getleaves=1');
	function hidealldiv(div) {
		var myCars=new Array("loadLeaveDeduction","loadinout","loadgenerateReport","loadbalanceleavesid","loadallleavehis","loadleaveinfo","loadDepartment","loadmyprofile","loadpersonalinfo","loadofficialinfo","loadempapplyleave","loadempleavestatus","loadempleavehistory",
							 "loadempleavereport","loadempeditprofile","loadholidays",
							 "loadempleavereport","loadteamleavereport","loadhelp",
							 "loadteamleaveapproval","loadattendance","loadcalender","loadoptionalleave","loadvoeform",
							 "loadpendingstatus","loadhrsection","loadmanagersection","loadapplyteammemberleave",
							 "loadcompoffleave","loadtrackattendance", "loadAttd", "loadwfhhr", "loadextrawfhhr");
		var hidedivarr=removeByValue(myCars,div);
		hidediv(hidedivarr);
		showdiv(div);
	}
	
	function hidediv(arr) {
		$("#footer").show();
		for(var i=0; i<arr.length; i++) {
				$("#"+arr[i]).hide();
				$("#"+arr[i]).html("");
	        }
	}
	function showdiv(div) {
		$("#"+div).show();
	}
	function removeByIndex(arr, index) {
	    arr.splice(index, 1);
	}
	
	function removeByValue(arr, val) {
	    for(var i=0; i<arr.length; i++) {
	        if(arr[i] == val) {
	            arr.splice(i, 1);
	            break;
	        }
	    }
	    return arr;
	}
	
	$(document).mousemove(function(e){
		$('#status').html(e.pageX +', '+ e.pageY);
	}); 
	
	$("#HomeButton").click(function(){
		hidealldiv('loadempleavestatus');
		$('#loadingmessage').html("<center><img src='public/images/spinload.jpg' class='img-responsive' style='margin-top: 10px;'/></center>");
		$("#loadempleavestatus").load('userDashboard.php');
	});

	$("#myprofile").click(function(){
		hidealldiv('loadbalanceleavesid');
		$('#loadingmessage').html("<center><img src='public/images/spinload.jpg' class='img-responsive' style='margin-top: 10px;'/></center>");
		$("#loadbalanceleavesid").load('balanceleaves.php?getdetailedleaves=1');
	});
	
	$("#detailleaves").click(function(){
		$("#balanceDialog").load('balanceleaves.php?getdetailedleaves=1');
		var p = $(this).position();
		$( "#balanceDialog" ).dialog({
				position: [p.left+30,p.top+20]		
		});
	});
	$("#managermodifyempapprovedleaves").click(function(){
		hidealldiv('loadmanagersection');
		$('#loadingmessage').html("<center><img src='public/images/spinload.jpg' class='img-responsive' style='margin-top: 10px;'/></center>");
		$("#loadmanagersection").load('modifyempapprovedleaves.php?role=manager');
	});
	
	$("#allleavehis").click(function(){
		hidealldiv('loadallleavehis');
		$('#loadingmessage').html("<center><img src='public/images/spinload.jpg' class='img-responsive' style='margin-top: 10px;'/></center>");
		$("#loadallleavehis").load('leaveHis.php');
	});
	
	$("#editprofileid").click(function(){
		hidealldiv('loadempeditprofile');
		$('#loadingmessage').html("<center><img src='public/images/spinload.jpg' class='img-responsive' style='margin-top: 10px;'/></center>");
		$("#loadempeditprofile").load('personalinfo.php?personalinfo=1');
	});
	
	$("#empreport").click(function(){
		hidealldiv('loadempleavereport');
		$('#loadingmessage').html("<center><img src='public/images/spinload.jpg' class='img-responsive' style='margin-top: 10px;'/></center>");
		$("#loadempleavereport").load('empleavereport.php');
	});
	
	$("#teamLeavereport").click(function(){
		hidealldiv('loadteamleavereport');
		$('#loadingmessage').html("<center><img src='public/images/spinload.jpg' class='img-responsive' style='margin-top: 10px;'/></center>");
		$("#loadteamleavereport").load('teamreport.php?report=1');
	});
	
	$("#generateReportHR").click(function(){
		hidealldiv('loadgenerateReport');
		$('#loadingmessage').html("<center><img src='public/images/spinload.jpg' class='img-responsive' style='margin-top: 10px;'/></center>");
		$("#loadgenerateReport").load('generateReport.php');
	});
	
	$("#generateReportManager").click(function(){
		hidealldiv('loadgenerateReport');
		$('#loadingmessage').html("<center><img src='public/images/spinload.jpg' class='img-responsive' style='margin-top: 10px;'/></center>");
		$("#loadgenerateReport").load('generateReport.php?managerUser=1');
	});
	
	$("#holidays").click(function(){
		hidealldiv('loadholidays');
		$('#loadingmessage').html("<center><img src='public/images/spinload.jpg' class='img-responsive' style='margin-top: 10px;'/></center>");
		$("#loadholidays").load('Holidays.php');
	});
	
	$("#leaveinfo").click(function(){
		hidealldiv('loadleaveinfo');
		$('#loadingmessage').html("<center><img src='public/images/spinload.jpg' class='img-responsive' style='margin-top: 10px;'/></center>");
		$("#loadleaveinfo").load('leaveinfo.php');
	});
	
	$("#calender").click(function(){
		hidealldiv('loadcalender');
		$('#loadingmessage').html("<center><img src='public/images/spinload.jpg' class='img-responsive' style='margin-top: 10px;'/></center>");
		$("#footer").hide();
		$("#loadcalender").load('leavecalender.php');
	});
	
	$("#leaveapprovalid").click(function(){
		hidealldiv('loadteamleaveapproval');
		$('#loadingmessage').html("<center><img src='public/images/spinload.jpg' class='img-responsive' style='margin-top: 10px;'/></center>");
		$("#loadteamleaveapproval").load('teamleaveapproval.php');
	});
	
	$("#attendance").click(function(){
		hidealldiv('loadattendance');
		$('#loadingmessage').html("<center><img src='public/images/spinload.jpg' class='img-responsive' style='margin-top: 10px;'/></center>");
		$("#loadattendance").load('attendance.php');
	});
	
	$("#trackattendance").click(function(){
		  hidealldiv('loadtrackattendance');
		  $('#loadingmessage').html("<center><img src='public/images/spinload.jpg' class='img-responsive' style='margin-top: 10px;'/></center>");
         $("#loadtrackattendance").load('trackLeaves.php');
        });
	
	$("#help").click(function(){
		hidealldiv('loadhelp');
		$('#loadingmessage').html("<center><img src='public/images/spinload.jpg' class='img-responsive' style='margin-top: 10px;'/></center>");
		$("#loadhelp").load('help.php');
	});
	
	$("#managersection").click(function(){
		hidealldiv('loadmanagersection');
		$('#loadingmessage').html("<center><img src='public/images/spinload.jpg' class='img-responsive' style='margin-top: 10px;'/></center>");
		$("#loadmanagersection").load('manager.php');
	});
	
	$("#hrsection").click(function(){
		hidealldiv('loadhrsection');
		$('#loadingmessage').html("<center><img src='public/images/spinload.jpg' class='img-responsive' style='margin-top: 10px;'/></center>");
		$("#loadhrsection").load('hr.php?hrlinks=1');
	});
	
	$("#voe").click(function(){
		hidealldiv('loadvoeform');
		$('#loadingmessage').html("<center><img src='public/images/spinload.jpg' class='img-responsive' style='margin-top: 10px;'/></center>");
		$("#loadvoeform").load('voe.php');
	});
	
});