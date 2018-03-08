$('#divbody').on('focus','#addrow',function()
{
		var currentYear = (new Date).getFullYear();
		var currentMonth = (new Date).getMonth();
		var months = ['January', 'February', 'March', 'April', 'May', 'June',
		        	  'July', 'August', 'September', 'October', 'November', 'December'];
		for(var i=0;i<=12;i++) 
			if(i==currentMonth)
		  		if(months[i]+','+currentYear==$("#claim_period").val())
				{
				  $('#addrow').attr("disabled", true);
				  BootstrapDialog.alert("You can not add days for current month");
		  		}
});	

$("#vehicle_type").change(function () {
	if($("#vehicle_type").find('option:selected').text()=="Two Wheeler") {
		$("#drivers_salary").attr('readonly','true');
		$("#drivers_salary").val(0);	
	}
	if($("#vehicle_type").find('option:selected').text()=="Four Wheeler") {
		$("#drivers_salary").removeAttr('readonly');
		$("#drivers_salary").val(0);
        }
});

$( "#claim_period" ).datepicker({
	changeMonth: true,
	changeYear: true,
	showButtonPanel: true,
	dateFormat: 'MM,yy',
	minDate:"-12M",
	maxDate:"+0D",
	showButtonPanel: true,
	showOn: 'both',
	buttonImageOnly: true,	
    onClose: function(dateText, inst) { 
    	var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
    	var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
    	$(this).datepicker('setDate', new Date(year, month, 1));
        change(); 
    }
});

$("#claim_period").focus(function () {
    $(".ui-datepicker-calendar").hide();
    $("#ui-datepicker-div").position({
        my: "center top",
        at: "center bottom",
        of: $(this)
    });
});

$("#formvoe").validate({
	errorClass: "errormsg",
	rules: {
		distance_from_residenceto_office: "required",
	    vehicle_milage: "required",
	    fuelcost_perlitre:"required",
	    repairs_maintence_expenses:{
	    	required: true
	    },
	    driver_salary: {
	    	required: true,
	    	max:15000
        },
	    vehicle_regno:"required",
	    vehicle_model:"required",
		vehicle_type:"required",
	    fuel_nature:"required",
	    original_cost:"required"
	},
	messages: {
		distance_from_residenceto_office: "Please specify distance",
	    vehicle_milage: "Please specify milage of vehicle",
	    fuelcost_perlitre:"Please specify fuel cost per litre",
	    repairs_maintence_expenses:{
	    	required: "Please specify repairs maintence expenses"
	    },
	    driver_salary: {
	    	required: "Please specify driver's salary",
	    	max: "value should not be greater than 15000"
       },
	   vehicle_regno:"Please specify registration number",
	   vehicle_model:"Please specify vechicle model",
	   vehicle_type:"Please specify vechicle type",
	   fuel_nature:"Please specify nature of fuel",
	   original_cost:"Please specify cost of vehicle"
	},
	submitHandler: function() {
		$.ajax({ 
			data: $('#formvoe').serialize(), 
		    type: $('#formvoe').attr('method'), 
		    url:  $('#formvoe').attr('action'), 
		    success: function(response) { 
		    	var patt = new RegExp("dontSubmit","g");
		 	   	var match = response.match(patt);
		        if(match && match[0]=="dontSubmit") {
		        	BootstrapDialog.alert("You can not submit voe form before completion of month. Please select previous month.");
		        	$("#loadvoeform").load("voe.php");
		        } else {
		        	$("#formOf").show();
		        	$("#verification").show();
		        	$("#sig").show();
		        	$(".submit").hide();
			        $("#addrow").remove();
			        $("#print").show();
			        $("#delete").show();
			        BootstrapDialog.alert("VOE form submitted successfully");
		        }
		    }
		});
		return false;
	}
});
$("#firstform").validate({
	errorClass: "errormsg",
    rules: {
    	residentialAddress: "required",
	    phoneNo: "required",
	    father_name: "required"
    },
    messages: {
    	residentialAddress: "Please specify residential Address",
	    phoneNo: "Please specify Phone Number",
	    father_name: "please specify father name"
    },
	submitHandler: function() {
		$.ajax({ 
	       data: $('#firstform').serialize(), 
	       type: $('#firstform').attr('method'), 
	       url:  $('#firstform').attr('action'), 
	       success: function(response) { 
	           $('#loadvoeform').load("voe.php"); 
	       }
		});
		return false;
	  }
	  });

	// validations
	
	 $(".input").keypress(function (e) {
			 if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)&& e.which != 46) 
			 {
			
	            $(this).parent().find('span').html("Digits Only").show().fadeOut(3000);
	            e.preventDefault();  
		      } 
	 });
	
	$("#father_name").keypress(function (e) {
                if((e.which<65||e.which>122)&&(e.which!=32)&&(e.which!= 8&& e.which != 0) && e.which != 46)
                {       
                 $(this).parent().find('span').html("Alphabets Only").show().fadeOut(3000);
                 e.preventDefault();    
                }       

         });
 
	$('#mytable').load("system.php?table=1");
	$('#divbody').on('click','.addingrow',function()
     {
		var dateVal=$("#claim_period").val(); 
		var split= dateVal.split(",");
	    var days=getDaysInMonth(getMonthNumber(split[0]), split[1]);
	    $(this).find("input").datepicker ({    	
	    	showButtonPanel: true,
	    	buttonImage: 'public/js/datepicker/datepickerImages/calendar.gif',
	    	showButtonPanel: true,
	    	showOn: 'both',
	    	buttonImageOnly: true,
	    	dateFormat: 'd/MM/yy',
	    	minDate:'01/'+split[0]+'/'+split[1],
	    	maxDate:days+'/'+split[0]+'/'+split[1],
	    	onSelect: function(dateText, ect)
	    	{
	    		var date = $(this).val();
	    		var rows = $("#mytable tr td:nth-child(1)"); 
	            var employee_numberVal=$("#employee_number").val();
	    		$.ajax
	    		({
	    			context: this,
	    	    	type: "POST",
	    	    	url: "system.php?table1=1&specific_date="+date+"&employee_number="+employee_numberVal,
	    		    success: function(data)
	    		    {
	    		    	if ($.inArray('%', data)> 0)
		    		     { 
		    		    	//alert(data); 
	    		    		var selectedElement=$(this);
	    		    		var selectedElementparent=$(this).parent().next().find("input");
	    		    		var selectedElementparent1=$(this).parent().next().next().find("input");
	    		    		var selectedElementparent2=$(this).parent().next().next().next().find("input");
	    		    		var selectedElementparent3=$(this).parent().next().next().next().next().find("input");
	    		    		var isfound=false;
		    	    		$(".calender").not(this).each(function() {
		    	    			if($(this).val()==selectedElement.val())
		    		    		{
	                           		BootstrapDialog.alert("Selected date is already present in table");
	                           		
	                           		selectedElement.val("");
	                           		selectedElementparent.val("");
	                           		selectedElementparent1.val("");
	                           		selectedElementparent2.val("");
	                           		selectedElementparent3.val("");
	                           		totalVoe();
	                           		isfound=true;
	                           	}

		    	    		});
		    		    	
			    		    	if(isfound==false){
			    		    		var split= data.split("%");
			    		    		var distance=$("#mytable tr td:nth-child(3)").find("input").val()
				    		        var fuel_cost=$("#mytable tr td:nth-child(4)").find("input").val()
				    		        var total_cost=$("#mytable tr td:nth-child(5)").find("input").val()
				    		        $(this).parent().next().find("input").val(split[1]);
		    		    	    	$(this).parent().next().next().find("input").val(distance);
			    		        	$(this).parent().next().next().next().find("input").val(fuel_cost);
			    		        	$(this).parent().next().next().next().next().find("input").val(total_cost);
			    		        	totalVoe();
		    		    	  }
			    		      
		    		     }
	    		    	else {
		    		        BootstrapDialog.alert(data);
			    		    $(this).val("");
			    		    $(this).parent().next().find("input").val("");
    		        		$(this).parent().next().next().find("input").val("");
    		        		$(this).parent().next().next().next().find("input").val("");
    		        		$(this).parent().next().next().next().next().find("input").val("");
		    		    }
	    	         }
	    		}); 
	            }
	         });
	    });


function getDaysInMonth(month, year) {

    return new Date(year, month, 0).getDate();
}

function getMonthNumber(monthName) {
	  var months = ['January', 'February', 'March', 'April', 'May', 'June',
	  'July', 'August', 'September', 'October', 'November', 'December'];
	  for(var i=0;i<=12;i++)
	  
		  if(months[i]==monthName)
			  return (i+1);
	  
	};

function deleteDay() {
	  $(".cancel").click(function() {
	  	$(this).parent().remove();
	  	totalVoe();
  	  });
}

function addRow()
{
	
	var append="<tr class='add'><td class='addingrow'><input class='calender' type='text' readonly name='specific_date[]'/></td><td class='day'><input type='text' readonly/></td><td><input type='text'></td><td><input readonly type='text' /></td><td><input readonly type='text'/></td><td class='cancel'><img src='images/cancel.png' name='cancel' onclick='deleteDay();'/></td></tr>";
	$('#mytable tr:last').before(append);
	
	
}
function change() {
	var dateVal=$("#claim_period").val();
    var employee_numberVal=$("#employee_number").val();
	// To get table info
	 $.ajax({
         type: "POST",
         url: "system.php?table=1&date="+dateVal+"&employee_number="+employee_numberVal,
             success: function(data){
            	 $("#mytable").html(data);
             }
	});

  	// To get data info
	 $.ajax({
         type: "POST",
         url: "system.php?data=1&date="+dateVal+"&employee_number="+employee_numberVal,
         success: function(data){
         	if (data=="\r\n") {     
 		   	    $("#distance_from_residenceto_office").val("");
        	 	$("#vehicle_milage").val("");
 		    	$("#fuelcost_perlitre").val("");
 		    	$("#expenses").val("");
		        $("#wear_tear_cost").val("");
		        $("#drivers_salary").val("");
		        $("#total_voe").val("");
		       
 		    	$("#distance_from_residenceto_office").change(function() {
 		    		distance();
		    	});
 		    	$("#fuelcost_perlitre").change(function() {
 		    		fuelCost();
 		    		
 		    	});
 		    	$("#total_voe").click(function() {
 		    	  totalVoe();
 		    	});
 		    	
         } else {
        	     var split= data.split("%");
        	    $("#employee_name").val(split[2]);
        	    $("#residential_address").val(split[3]);
     		    $("#official_address").val(split[4]);
     		    $("#father_name").val(split[5])
     		    $("#vehicle_regno").val(split[6]);
     		    $("#vehicle_model").val(split[7]);
    		    $("#fuel_nature").val(split[8]);
    		    $("#vehiclecost").val(split[9]);
    	        $("#distance_from_residenceto_office").val(split[10]);
    		    $("#vehicle_milage").val(split[11]);
    		    $("#fuelcost_perlitre").val(split[12]);
    		    if(split[13]==undefined){split[13]=0;}
    		    $("#expenses").val(split[13]);
   		        $("#wear_tear_cost").val(split[14]);
   		        if(split[15]==undefined){split[15]=0;}
   		        $("#drivers_salary").val(split[15]);
   		        $("#total_voe").val(split[16]);
   		        voe= $("#total_voe").val();
   		        if(voe=="") {
   		        	$('.submit').show();
	   		    	$("#vehicle_regno").removeAttr('readonly');
	   		    	$("#vehicle_model").removeAttr('readonly');
		  		    $("#fuel_nature").removeAttr('disabled');
	  		    	$("#vehiclecost").removeAttr('readonly');
		  	        $("#distance_from_residenceto_office").removeAttr('readonly');
	  		    	$("#vehicle_milage").removeAttr('readonly');
	  		    	$("#fuelcost_perlitre").removeAttr('readonly');
	  		    	$("#expenses").removeAttr('readonly');
	  		        $("#wear_tear_cost").removeAttr('readonly');
	 		        $("#drivers_salary").removeAttr('readonly');
			        $("#total_voe").removeAttr('readonly');
  			        $( "#total_voe" ).click(function() { 
 	   		    	    totalVoe();
 	   		   		});
   		       		$('#delete').hide();
   		       	   $('#print').hide();
   		            $('#formOf').hide();
   		           $('#verification').hide();
   		           $('#sig').hide();
   		       } else {
   		    	    $("#total_voe").attr('value',split[16]);
			    $("#claim_period").attr('value',$("#claim_period").val());
   		        	$('.submit').hide();
   		        	$('#employee_name').attr('readonly','true');
   		     	    $('#father_name').attr('readonly','true');
   		        	$("#residential_address").attr('readonly','true');
   	     		    $("#official_address").attr('readonly','true');
   	     		    $("#vehicle_regno").attr('readonly','true');
   	     		    $("#vehicle_model").attr('readonly','true');
			    $("#vehicle_type").attr('readonly','true');
   	    		    $("#fuel_nature").attr('disabled','true');
   	    		    $("#vehiclecost").attr('readonly','true');
   	    	        $("#distance_from_residenceto_office").attr('readonly','true');
   	    		    $("#vehicle_milage").attr('readonly','true');
   	    		    $("#fuelcost_perlitre").attr('readonly','true');
   	    		    $("#expenses").attr('readonly','true');
   	   		        $("#wear_tear_cost").attr('readonly','true');
   	   		        $("#drivers_salary").attr('readonly','true');
   	   		        $("#total_voe").attr('readonly','true');
   	   		        $( "#total_voe" ).removeAttr("onclick"); 
		   	   	    $('#delete').show();
		   	   	    $('#print').show();
		   	   	    $('#formOf').show();
		           $('#verification').show();
		           $('#sig').show();
		   		}
         }
     }
     }); 
}

function deletion() {
    var dateVal=$("#claim_period").val();
    var employee_numberVal=$("#employee_number").val();
 
    $.ajax({
        type: "POST",
        url: "system.php?delete=1&date="+dateVal+"&employee_number="+employee_numberVal,
        success: function(data)
        {
            BootstrapDialog.alert("Deleted successfully");
            $('#loadvoeform').load("voe.php"); 
        }
    });
}

function distance()
{
	$('#total_voe').val('');
	$('input[id^="toandfrokms"]').val(2*(Number($("#distance_from_residenceto_office").val())).toFixed(2));
	totalCost();
	totalVoe();
	weartearCost();	
}

function fuelCost()
{
	if ($('#fuelcost_perlitre').val()!="" || $('#vehicle_milage').val()!="") {
		$('input[id^="fuel_cost_per_km"]').val((Number($('#fuelcost_perlitre').val()) / Number($('#vehicle_milage').val())).toFixed(2));
		totalCost();
		totalVoe();
	}
}

function totalCost(){
		$('input[id^="total_cost"]').val((Number($('input[id^="toandfrokms"]').val()) * Number($('input[id^="fuel_cost_per_km"]').val())).toFixed(2));
		totalVoe();
};

function weartearCost() {
	 var vehicle_cost= $("#vehiclecost").val();
	 wear_tear_cost=(vehicle_cost*(0.06))/12;
	 $("#wear_tear_cost").val(wear_tear_cost.toFixed(2));
};

function totalVoe()
{
	
	var rows = $('#mytable tr td:first-child').find('input:text[value != ""]').length;
	// var rows=rowCount-2;
	 var total_cost=$('input[id^="total_cost"]').val(); 
	 var toandfrokms=$('input[id^="toandfrokms"]').val();
	 $("#total_days").val(rows);
	 $("#overall_cost").val((rows*total_cost).toFixed(2));
	 if($("#vehicle_type").find('option:selected').text()=="Four Wheeler") {
                if($("#overall_cost").val()>15000) {
			BootstrapDialog.alert("After Calculation of fuel expenditure for whole month, your total fuel expenditure is: "+$("#overall_cost").val()+"\nYour vehicle type is "+$("#vehicle_type").find('option:selected').text()+"\nYour overall fuel expenditure can't exceed 15000");
			$("#voeSubmit").hide()
		} else {
			$("#voeSubmit").show()
		}
         }
	 if($("#vehicle_type").find('option:selected').text()=="Two Wheeler") {
                if($("#overall_cost").val()>10000) {
                        BootstrapDialog.alert("After Calculation of fuel expenditure for whole month, your total fuel expenditure is: "+$("#overall_cost").val()+"\nYour vehicle type is "+$("#vehicle_type").find('option:selected').text()+"\nYour overall fuel expenditure can't exceed 10000");
			$("#voeSubmit").hide()
                } else {
			$("#voeSubmit").show()
		}
         }
     $("#totaltoandfrokms").val(rows*toandfrokms);
     $('#total_voe').val((Number($('#overall_cost').val())+ Number($('#expenses').val())+Number($('#wear_tear_cost').val())+ Number($('#drivers_salary').val())).toFixed(2));
}; 
