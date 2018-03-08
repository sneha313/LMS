function hidealldiv(div) {
    var myCars=new Array("loadempapplyleave","loadempleavestatus","loadempleavehistory",
                            "loadempleavereport","loadempeditprofile","loadholidays",
                            "loadempleavereport","loadteamleavereport","loadhelp",
                            "loadteamleaveapproval","loadattendance","loadcalender","loadoptionalleave","loadvoeform",
                            "loadpendingstatus","loadhrsection","loadmanagersection","loadapplyteammemberleave",
                            "loadcompoffleave","loadtrackattendance", "loadAttd","loadinout");
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


