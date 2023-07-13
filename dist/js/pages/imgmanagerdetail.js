window.entries = [];
window.page = 1;
window.filtererror = false;
window.tstart = moment('2015-01-01 00:00', 'YYYY-DD-MM HH:mm');
window.tend = moment('2015-01-01 23:59', 'YYYY-DD-MM HH:mm');

function createImages() {
    var fname = getFolderDate();
    $.ajax({
        type: "POST",
        url: "/camimgmanager-imglist",
        data: "foldername="+fname,
        cache: false,
        async: true,
        success: function(rsp) {
            var images = [];
            var tempimg = $.parseJSON(rsp);
            $.each(tempimg, function(k, v) {
                images.push(v);
            });
            window.entries = images;
            setPage(window.page);
        }
    });
} //endfunction createImages

function setPageLimit(limit) {
    $('#countselect').html(limit + ' <span class="fa fa-caret-down"></span>');
    setPage(window.page);
} //endfunction setPageLimit

function enableTimeError() {
    window.filtererror = true;
    $('.fltr_errormsg').show();
    $('.fltr_label').css('color', '#dd4b39');
    $('.fltr_withborder').css('border-color', '#dd4b39');
    $('.fltr_withborder').css('color', '#dd4b39');
} //endfunction enableTimeError

function disableTimeError() {
    window.filtererror = false;
    $('.fltr_errormsg').hide();
    $('.fltr_label').css('color', '#333333');
    $('.fltr_withborder').css('border-color', '#cccccc');
    $('.fltr_withborder').css('color', '#555555');
} //endfunction disableTimeError

function setPage(page) {
    window.page = page;
    renderEntries();
    renderPaging();
} //endfunction setPage

function selectAll() {
    var total = $('.ckbox').length;
    var checked = $('.ckbox input:checked').length;
    var unchecked = total - checked;
    if (checked > unchecked) {
        $('.ckbox input').prop('checked', false);
    } else {
        $('.ckbox input').prop('checked', true);
    } //endif
} //endfunction selectAll

function getPageResults() {
    var results = window.entries;
    var resultcount = results.length;
    var page = parseInt(window.page);
    var entriesperpage = parseInt($('#countselect').text());
    var from = (page * entriesperpage) - entriesperpage;
    var to = (page * entriesperpage) - 1;

    if ((to + 1) > resultcount) {
        to = resultcount - 1;
    } //endif

    var sort = $.trim($('#sortselect').text());
    var retVal = new Array();
    var count = 0;
    if (sort == 'Absteigend') {
        for (var i=from; i<(to+1); i++) {
            retVal[count] = results[i];
            count++;
        } //endfor
    } else {
        for (var i=to; i>(from-1); i--) {
            retVal[count] = results[i];
            count++;
        } //endfor
    } //endif

    return retVal;
} //endfunction getPageResults

function renderPaging() {
    $('.pagingcont').empty();
    var results = getPageResults();
    if (results.length > 0) {
        var entriesperpage = parseInt($('#countselect').text());
        var page = parseInt(window.page);
        var resultcount = entries.length;
        var pagecount = parseInt(resultcount / entriesperpage);
        if (pagecount < resultcount / entriesperpage) {
            pagecount++;
        } //endif

        var html = '';

        html += '<div class="text-center" style="margin-left: 15px;">' +
        '<ul class="pagination pagination-large default">';

        // Vorherige Seite
        if (page == 1) {
            html += '<li><span>«</span></li>';
        } else {
            html += '<li><a href="javascript: setPage('+ (page - 1) +');">«</a></li>';
        } //endif

        if (pagecount <= 10) {
            for (i=0; i<pagecount; i++) {
                html += '<li';
                if (page == (i+1)) {
                    html += ' class="active"';
                } //endif
                html += '><a href="javascript: setPage('+(i+1)+');">'+(i+1)+'</a></li>';
            } //endfor
        } else if (pagecount > 10) {
            if (page <= 6) {
                for (i=0; i<8; i++) {
                    if (page == (i+1)) {
                        html += '<li class="active"><span>' + (i+1) + '</span></li>';
                    } else {
                        html += '<li><a href="javascript: setPage('+(i+1)+');">' + (i+1) + '</a></li>';
                    } //endif
                } //endfor
                html += '<li class="disabled"><span>...</span></li>';

                html += '<li><a href="javascript: setPage('+(pagecount - 1)+');">' + (pagecount - 1) + '</a></li>';
                html += '<li><a href="javascript: setPage('+pagecount+');">' + pagecount + '</a></li>';
            } else if (page >= (pagecount - 5)) {
                html += '<li><a href="javascript: setPage(1);">1</a></li>';
                html += '<li><a href="javascript: setPage(2);">2</a></li>';
                html += '<li class="disabled"><span>...</span></li>';
                for (i=(pagecount-7); i<(pagecount+1); i++) {
                    if (page == i) {
                        html += '<li class="active"><span>' + i + '</span></li>';
                    } else {
                        html += '<li><a href="javascript: setPage('+i+');">' + i + '</a></li>';
                    } //endif
                } //endfor
            } else {
                html += '<li><a href="javascript: setPage(1);">1</a></li>';
                html += '<li><a href="javascript: setPage(2);">2</a></li>';
                html += '<li class="disabled"><span>...</span></li>';

                html += '<li><a href="javascript: setPage('+(page - 3)+');">' + (page-3) + '</a></li>';
                html += '<li><a href="javascript: setPage('+(page - 2)+');">' + (page-2) + '</a></li>';
                html += '<li><a href="javascript: setPage('+(page - 1)+');">' + (page-1) + '</a></li>';
                html += '<li class="active"><span>' + page + '</span></li>';
                html += '<li><a href="javascript: setPage('+(page + 1)+');">' + (page+1) + '</a></li>';
                html += '<li><a href="javascript: setPage('+(page + 2)+');">' + (page+2) + '</a></li>';
                html += '<li><a href="javascript: setPage('+(page + 3)+');">' + (page+3) + '</a></li>';

                html += '<li class="disabled"><span>...</span></li>';
                html += '<li><a href="javascript: setPage('+(pagecount - 1)+');">'+ (pagecount-1) +'</a></li>';
                html += '<li><a href="javascript: setPage('+pagecount+');">'+ pagecount +'</a></li>';
            } //endif
        } //endif

        // Naechste Seite
        if (page == pagecount) {
            html += '<li><span>»</span></li>';
        } else {
            html += '<li><a href="javascript: setPage('+(page + 1)+');">»</a></li>';
        } //endif

        html +=     '</ul>' +
        '</div>';

        $('.pagingcont').html(html);
    } //endif
} //endfunction renderPaging

function getFolderDate() {
    return moment($('.breadcrumb .active').text(), "DD.MM.YYYY").format('DD-MM-YY');
} //endfunction formatFolderDate

function getImageElementHTML(directory, name) {
    var idstring = name.split('.');
    idstring = idstring[0];

    var timestring = '';
    timestring = name.split('.');
    timestring = timestring[0];
    timestring = timestring.split('-');
    timestring = timestring[0] + ':' + timestring[1] + ':' + timestring[2];

    var html = '<div class="col-xs-6 col-sm-4 col-md-4 col-lg-3 document" id="' + idstring + '">' +
        '<div class="thmb">' +
        '<div class="ckbox ckbox-default hidel" style="display: none;">' +
        '<input id="check-'+idstring+'" type="checkbox" name="names[]" value="'+directory+'/'+name+'">' +
        '<label for="check-'+idstring+'"></label>' +
        '</div>' +
        '<div class="btn-group fm-group hidel" style="display: none;">' +
        '<a href="javascript: delImage(\''+name+'\');" class="btn btn-default imgdelbtn" title="Bild l&ouml;schen">' +
        '<i class="fa fa-trash"></i>' +
        '</a>' +
        '</div>' +
        '<div class="thmb-prev">' +
        '<a href="/ftp/'+directory+'/'+name+'" title="'+name+' - '+$('.breadcrumb .active').text()+', '+timestring+' Uhr" data-gallery><img class="img-responsive" alt="" src="/ftp/'+directory+'/'+name+'"></a>' +
        '</div>' +
        '<h4 class="fm-title">' +
        name +
        '</h4>' +
        '<small class="text-muted">'+'<i class="fa fa-clock-o"></i> ' + timestring + ' Uhr' + '</small>' +
        '</div>' +
        '</div>';

    return html;
} //endfunction getImageElementHTML

function renderEntries() {
    var entries = getPageResults();
    $('#imagelist').empty();
    var fname = getFolderDate();
    var timestring = '';
    for (var j=0; j<entries.length; j++) {
        timestring = entries[j].split('.');
        timestring = timestring[0];
        timestring = timestring.split('-');
        timestring = timestring[0] + ':' + timestring[1];
        timestring = '2015-01-01 ' + timestring;
        timestring = moment(timestring, 'YYYY-DD-MM HH:mm');
        if (window.tstart <= timestring && timestring <= window.tend) {
            $('#imagelist').append(getImageElementHTML(fname, entries[j]));
        } //endif
    } //endfor

    $('.document').hover(function () {
        $(this).find(".hidel").show();
    }, function () {
        $(this).find(".hidel").hide();
    });

    $('.document').find(".ckbox input").change(function () {
        if ($(this).is(':checked')) {
            $(this).parent().parent().addClass('checked');
        } else {
            $(this).parent().parent().removeClass('checked');
        } //endif
    });
} //endfunction renderEntries

function delImage(name) {
    var fname = getFolderDate();

    var idstring = name.split('.');
    idstring = idstring[0];

    $.ajax({
        type: "POST",
        url: "/camimgmanager-del-image",
        data: "folder="+fname+"&name="+name,
        cache: false,
        async: true,
        success: function(rsp) {
            if (rsp == "1") {
                $('#' + idstring).remove();
            } //endif
        }
    });
} //endfunction delImage

function delImages() {
    $.ajax({
        type: "POST",
        url: "/camimgmanager-del-images",
        data: $('.ckbox input:checked').serialize(),
        cache: false,
        async: true,
        success: function (rsp) {
            $('#delete-modal').modal('hide');
            createImages();
        }
    });
} //endfunction delImages

function downloadImages() {
    if ($('.thmb .ckbox input:checked').length > 0) {
        $.ajax({
            type: "POST",
            url: "/camimgmanager-get-zippedfiles",
            data: $('.ckbox input:checked').serialize(),
            cache: false,
            async: true,
            success: function (rsp) {
                window.location.href = '/imgzip/' + rsp;
            }
        });
    } //endif
} //endfunction downloadImages

function showDelImages() {
    if ($('.thmb .ckbox input:checked').length > 0) {
        $('#modal-del-button').prop("href", "javascript: delImages();");
        $('#delete-modal .modal-title').html('<i class="fa fa-trash"></i>&nbsp;&nbsp;Bilder l&ouml;schen');
        $('#delete-modal .modal-text').text('Wollen Sie die markierten Bilder wirklich löschen?');
        $('#delete-modal').modal('show');
    } //endif
} //endfunction showDelImages

function setSort(type) {
    if (type == 'asc') {
        type = 'Aufsteigend';
    } else {
        type = 'Absteigend';
    } //endif

    $('#sortselect').html(type + ' <span class="fa fa-caret-down"></span>')
    setPage(window.page);
} //endfunction setSort

function checkFilterTime() {
    var start = moment('2015-01-01 ' + $('#tpstart1').val(), 'YYYY-DD-MM HH:mm');
    var end = moment('2015-01-01 ' + $('#tpend1').val(), 'YYYY-DD-MM HH:mm');
    if (start > end) {
        if (window.filtererror == false) {
            enableTimeError();
        } //endif
    } else {
        window.tstart = start;
        window.tend = end;
        setPage(window.page);
        if (window.filtererror == true) {
            disableTimeError();
        } //endif
    } //endif
} //endfunction checkFilterTime

$(document).ready(function() {
    $(".tpstart").timepicker({
        showInputs: false,
        showMeridian: false,
        defaultTime: '00:00 AM'
    });
    $(".tpend").timepicker({
        showInputs: false,
        showMeridian: false,
        defaultTime: '23:59 AM'
    });

    $('#tpstart1').change(function() {
        var val = $('#tpstart1').val();
        $('#tpstart2').val(val);
        checkFilterTime();
    });
    $('#tpend1').change(function() {
        var val = $('#tpend1').val();
        $('#tpend2').val(val);
        checkFilterTime();
    });
    $('#tpstart2').change(function() {
        var val = $('#tpstart2').val();
        $('#tpstart1').val(val);
        checkFilterTime();
    });
    $('#tpend2').change(function() {
        var val = $('#tpend2').val();
        $('#tpend1').val(val);
        checkFilterTime();
    });

    $('.fltr_reset_button').click(function() {
        $('.tpstart').val('00:00');
        $('.tpend').val('23:59');
        window.tstart = moment('2015-01-01 00:00', 'YYYY-DD-MM HH:mm');
        window.tend = moment('2015-01-01 23:59', 'YYYY-DD-MM HH:mm');
        setPage(window.page);
    });

    createImages();
});