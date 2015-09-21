$('input, textarea').placeholder();
$('#snippetCategory').selectpicker();

var leftSideHidden = false;
$('#leftSideToggle').click(function(e) {
    e.preventDefault();
    if (leftSideHidden == false) {
        $('#rightSide').addClass('pull-right');
        $('#leftSide').hide().fadeOut(500, expandRightSide);
        leftSideHidden = true;
    } else {
        $('#rightSide').switchClass("col-md-12", "col-md-9", 500, "easeInOutQuad", showLeftSide);
        $('#leftSideToggleIcon').switchClass("fa-indent", "fa-outdent");
        leftSideHidden = false;
    }
});

function showLeftSide() {
    $('#leftSide').show().fadeIn(500, removePullRight);
}

function expandRightSide() {
    $('#rightSide').switchClass("col-md-9", "col-md-12", 500, "easeInOutQuad");
    $('#leftSideToggleIcon').switchClass("fa-outdent", "fa-indent");
}

function removePullRight() {
    $('#rightSide').removeClass('pull-right');
}
$('#leftSideToggle').tooltip({
    placement: 'bottom'
});

$('#codeInput').autogrow();

$('#codeInput').keyup(function() {
    var result = $.countLines("#codeInput");
    $('#lineCount').text(result.visual);
    $('#caretLine').text(getLineOfCaret(document.getElementById("codeInput")));
    $("#charCount").text($(this).val().length + " characters");
});
$('#codeInput').click(function() {
    $('#caretLine').text(getLineOfCaret(document.getElementById("codeInput")));
});

function getLineOfCaret(el) {
    var pos = 0;
    if (el.selectionEnd) {
        pos = el.selectionEnd;
    } else if (document.selection) {
        el.focus();

        var r = document.selection.createRange();
        if (r == null) {
            pos = 0;
        } else {

            var re = el.createTextRange(),
                rc = re.duplicate();
            re.moveToBookmark(r.getBookmark());
            rc.setEndPoint('EndToStart', re);

            pos = rc.text.length;
        }
    }
    return el.value.substr(0, pos).split("\n").length;
}

function urlencode(str) {
  str = (str + '').toString();
  return encodeURIComponent(str).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').replace(/\)/g, '%29').replace(/\*/g, '%2A').replace(/%20/g, '+');
}
function htmlEntities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}
function krEncodeEntities(s){
    return $("<div/>").text(s).html();
}
function krDencodeEntities(s){
    return $("<div/>").html(s).text();
}

// view_snippet.php
function viewSnippet() {
    $("#charCount").text($('#snippetOutput div pre code').text().length + " characters");
    hljs.highlightBlock(document.getElementById('codeOutput'));
}

// edit_snippet.php
function editSnippet() {
    var result = $.countLines("#codeInput");
    $('#lineCount').text(result.visual);
    $('#caretLine').text(getLineOfCaret(document.getElementById("codeInput")));
    $("#charCount").text($('#codeInput').val().length + " characters");
    $('#codeInput').autogrow({
        onInitialize: true
    });
}

function getQueryVariable(variable) {
    var query = window.location.search.substring(1);
    var vars = query.split("&");
    for (var i = 0; i < vars.length; i++) {
        var pair = vars[i].split("=");
        if (pair[0] == variable) {
            return pair[1];
        }
    }
    return (false);
}

function todayDate(id) {
    date = new Date;
    year = date.getFullYear();
    month = date.getMonth();
    months = new Array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
    d = date.getDate();
    day = date.getDay();
    days = new Array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
    result = '' + days[day] + ', ' + months[month] + ' ' + d + ', ' + year;
    document.getElementById(id).innerHTML = result;
    setTimeout('todayDate("' + id + '");', '86400000');
    return true;
}
function currentTime(id) {
    date = new Date;
    h = date.getHours();
    if (h < 10) {
        h = "0" + h;
    }
    m = date.getMinutes();
    if (m < 10) {
        m = "0" + m;
    }
    s = date.getSeconds();
    if (s < 10) {
        s = "0" + s;
    }
    result = h + ':' + m + ':' + s;
    document.getElementById(id).innerHTML = result;
    setTimeout('currentTime("' + id + '");', '1000');
    return true;
}

function totalSnippets(id) {
	$.get( "processing.php?get=totalSnippets", function( data ) {
  		$('#' + id).text(data.totalSnippets);
	}, "json" );
	setTimeout('totalSnippets("' + id + '");', '1000');
	return true;
}

function equalizeMinHeight() {
    //var clientHeight = document.getElementById('catList').clientHeight;
    //document.getElementById("content").style.minHeight = (clientHeight + 10) + 'px';
    var leftside = $('#leftSide div.panel-body').height();
    $('#rightSide div.panel-body').css('min-height', leftside-10);
}
equalizeMinHeight();

$('a.back').click(function(){
    parent.history.back();
    return false;
});