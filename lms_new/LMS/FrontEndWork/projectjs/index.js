$('#loadingmessage').show();
$('document').ready(function() {
	$('#loadingmessage').hide();
	$("#loadempleavestatus").load('selfleavestatus.php');
	$("#loadmyprofile").load('userDashboard.php');
	$("#balanceleavesid").load('balanceleaves.php?getleaves=1');
	function hidealldiv(div) {
		var myCars=new Array("loadmyprofile","loadpersonalinfo","loadofficialinfo","loadempapplyleave","loadempleavestatus","loadempleavehistory",
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
		$("#loadempleavestatus").load('selfleavestatus.php');
		$("#balanceleavesid").load('balanceleaves.php?getleaves=1');
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
		$("#loadmanagersection").load('modifyempapprovedleaves.php?role=manager');
	});
	
	$("#extrawfhhrid").click(function(){
		hidealldiv('loadextrawfhhr');
		$("#loadextrawfhhr").load('wfhhours/linkwfh.php');
	});
	$("#applyleaveid").click(function(){
		hidealldiv('loadempapplyleave');
		$("#loadempapplyleave").load('applyleave.php?leaveform=1');
	});
	
	$("#selfleavehistoryid").click(function(){
		hidealldiv('loadempleavehistory');
		$("#loadempleavehistory").load('selfleavehistory.php');
	});

	$("#editprofileid").click(function(){
		hidealldiv('loadempeditprofile');
		$("#loadempeditprofile").load('editprofile.php');
	});
	$("#addwfh").click(function(){
		hidealldiv('loadwfhhr');
		$("#loadwfhhr").load('wfhhours/addwfh.php?addWFHhrForm=1');
	});
	
	$("#editwfh").click(function(){
		hidealldiv('loadwfhhr');
		$("#loadwfhhr").load('wfhhours/viewwfh.php');
	});
	$("#teamLeavereport").click(function(){
		hidealldiv('loadteamleavereport');
		$("#loadteamleavereport").load('teamleavereport.php');
	});
	
	$("#empreport").click(function(){
		hidealldiv('loadempleavereport');
		$("#loadempleavereport").load('empleavereport.php');
	});
	$("#applyteammemberleaveid").click(function(){
		hidealldiv('loadapplyteammemberleave');
		$("#loadapplyteammemberleave").load('applyteammemberleave.php?getEmp=1');
	});
	
	$("#optionalLeaveStatus").click(function(){
		hidealldiv('loadoptionalleave');
		$("#loadoptionalleave").load('optionalleave.php');
	});
	
	$("#leaveapprovalid").click(function(){
		hidealldiv('loadteamleaveapproval');
		$("#loadteamleaveapproval").load('teamleaveapproval.php');
	});
	
	$("#holidays").click(function(){
		hidealldiv('loadholidays');
		$("#loadholidays").load('Holidays.php');
	});
	
	$("#calender").click(function(){
		hidealldiv('loadcalender');
		$("#footer").hide();
		$("#loadcalender").load('leavecalender.php');
	});
	
	$("#attendance").click(function(){
		hidealldiv('loadattendance');
		$("#loadattendance").load('attendance.php');
	});
	
	$("#trackattendance").click(function(){
         hidealldiv('loadtrackattendance');
         $("#loadtrackattendance").load('trackLeaves.php');
        });
	
	$("#help").click(function(){
		hidealldiv('loadhelp');
		$("#loadhelp").load('help.php');
	});
	
	$("#myprofile").click(function(){
		hidealldiv('loadmyprofile');
		$("#loadmyprofile").load('userDashboard.php');
	});
	
	$("#personalinfo").click(function(){
		hidealldiv('loadpersonalinfo');
		$("#loadpersonalinfo").load('personalinfo.php');
	});
	
	$("#officialinfo").click(function(){
		hidealldiv('loadofficialinfo');
		$("#loadofficialinfo").load('officialinfo.php');
	});
	
	$("#applyleave").click(function(){
		hidealldiv('loadempapplyleave');
		$("#loadempapplyleave").load('applyLeave.php');
	});
	
	$("#managersection").click(function(){
		hidealldiv('loadmanagersection');
		$("#loadmanagersection").load('manager.php');
	});
	
	$("#hrsection").click(function(){
		hidealldiv('loadhrsection');
		$("#loadhrsection").load('hr.php?hrlinks=1');
	});
	
	$("#department").click(function(){
		hidealldiv('loadhrsection');
		$("#loadhrsection").load('DepartmentActionbyHR.php');
	});
	
	$("#voe").click(function(){
		hidealldiv('loadvoeform');
		$("#loadvoeform").load('voe.php');
	});
	
	$("#compoffleaveid").click(function(){
		hidealldiv('loadcompoffleave');
		$("#loadcompoffleave").load('ApplyCompoffLeave.php?compoffleave=1');
	});
});