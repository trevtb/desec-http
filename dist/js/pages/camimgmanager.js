window.entries = [];
window.page = 1;

function createFolders() {
    $.ajax({
        type: "GET",
        url: "/camimgmanager-folderlist",
        cache: false,
        async: true,
        success: function(rsp) {
            var folders = [];
            var tempfolders = $.parseJSON(rsp);
            $.each(tempfolders, function(k, v) {
                folders.push(v);
            });
            window.entries = folders;
            setPage(window.page);

            /**var folders = $.parseJSON(rsp);
            var folderhtml = '';
            $('.media-manager').empty();

            var count = 0;
            $.each(folders, function(k, v) {
                var response = $.ajax({
                    type: "POST",
                    url: "/camimgmanager-imglist",
                    data: "foldername="+v,
                    cache: false,
                    async: false
                }).responseText;

                if (isWithinTimespan(v)) {
                    folderhtml = getFolderElementHTML(v, response);
                    $('#folderlist').append(folderhtml);
                    $("#doc-" + v).hover(function () {
                        $("#doc-" + v + " .hidel").show();
                    }, function () {
                        $("#doc-" + v + " .hidel").hide();
                    });

                    $("#doc-" + v + " .thmb-prev img").click(function () {
                       window.location.href = '/camimgmanager-detail/'+v;
                    });

                    $("#check-" + v).change(function () {
                        if ($(this).is(':checked')) {
                            $("#doc-" + v + " .thmb").addClass('checked');
                        } else {
                            $("#doc-" + v + " .thmb").removeClass('checked');
                        } //endif
                    });

                    count++;
                } //endif
            });
            if (count == 0) {
                $('.media-manager').html('<p class="lead" style="padding-left: 15px;">F&uuml;r den gew&auml;hlten Zeitraum existieren keine Aufnahmen.</p>')
            } //endif**/
        }
    });
} //endfunction createFolders

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

        html += '<div class="text-center" style="margin-left: 5px;">' +
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

function setPage(page) {
    window.page = page;
    renderEntries();
    renderPaging();
} //endfunction setPage

function setPageLimit(limit) {
    $('#countselect').html(limit + ' <span class="fa fa-caret-down"></span>');
    setPage(window.page);
} //endfunction setPageLimit

function renderEntries() {
    var entries = getPageResults();
    $('#folderlist').empty();

    var count = 0;
    for (var i=0; i<entries.length; i++) {
        if (isWithinTimespan(entries[i])) {
            var response = $.ajax({
                type: "POST",
                url: "/camimgmanager-imglist",
                data: "foldername=" + entries[i],
                cache: false,
                async: false
            }).responseText;
            folderhtml = getFolderElementHTML(entries[i], response);
            $('#folderlist').append(folderhtml);

            count++;
        } //endif
    } //endfor

    if (count == 0) {
        $('#folderlist').html('<p class="lead" style="padding-left: 15px;">F&uuml;r den gew&auml;hlten Zeitraum existieren keine Aufnahmen.</p>')
    } else {
        $('.document').hover(function () {
            $(this).find(".hidel").show();
        }, function () {
            $(this).find(".hidel").hide();
        });

        $('.document .thmb-prev img').click(function () {
            var foldername = $(this).prop('src');
            foldername = foldername.split('/');
            foldername = foldername[foldername.length - 2];
            window.location.href = '/camimgmanager-detail/' + foldername;
        });

        $('.document').find(".ckbox input").change(function () {
            if ($(this).is(':checked')) {
                $(this).parent().parent().addClass('checked');
            } else {
                $(this).parent().parent().removeClass('checked');
            } //endif
        });
    } //endif
} //endfunction renderEntries

function showDelFolder(name) {
    $('#modal-del-button').prop("href", "javascript: delFolder('"+name+"');");
    $('#delete-modal .modal-title').html('<i class="fa fa-trash"></i>&nbsp;&nbsp;Ordner l&ouml;schen');
    $('#delete-modal .modal-text').text('Wollen Sie den Ordner wirklich löschen? Alle darin enthaltenen Bilder gehen verloren.');
    $('#delete-modal').modal('show');
} //endfunction showDelFolder

function showDelFolders() {
    if ($('.thmb .ckbox input:checked').length > 0) {
        $('#modal-del-button').prop("href", "javascript: delFolders();");
        $('#delete-modal .modal-title').html('<i class="fa fa-trash"></i>&nbsp;&nbsp;Ordner l&ouml;schen');
        $('#delete-modal .modal-text').text('Wollen Sie die markierten Ordner wirklich löschen? Alle darin enthaltenen Bilder gehen verloren.');
        $('#delete-modal').modal('show');
    } //endif
} //endfunction showDelFolders

function delFolder(name) {
    $.ajax({
        type: "POST",
        url: "/camimgmanager-del-folder",
        data: "name="+name,
        cache: false,
        async: true,
        success: function (rsp) {
            $('#delete-modal').modal('hide');
            createFolders();
        }
    });
} //endfunction delFolder

function delFolders() {
    $.ajax({
        type: "POST",
        url: "/camimgmanager-del-folders",
        data: $('.ckbox input:checked').serialize(),
        cache: false,
        async: true,
        success: function (rsp) {
            $('#delete-modal').modal('hide');
            createFolders();
        }
    });
} //endfunction delFolders

function downloadFolder(name) {
    $.ajax({
        type: "POST",
        url: "/camimgmanager-get-zipfolder",
        data: 'name='+name,
        cache: false,
        async: true,
        success: function (rsp) {
            window.location.href = '/imgzip/'+rsp;
        }
    });
} //endfunction downloadFolder

function downloadFolders() {
    if ($('.thmb .ckbox input:checked').length > 0) {
        $.ajax({
            type: "POST",
            url: "/camimgmanager-get-zipfolders",
            data: $('.ckbox input:checked').serialize(),
            cache: false,
            async: true,
            success: function (rsp) {
                window.location.href = '/imgzip/' + rsp;
            }
        });
    } //endif
} //endfunction downloadFolders

function getFolderElementHTML(name, flist) {
    flist = $.parseJSON(flist);
    var arr = [], len;
    for(key in flist) {
        arr.push(key);
    } //endof
    len = arr.length;
    var date = moment(name, "DD-MM-YY").format('DD.MM.YYYY');

    var html = '<div class="col-xs-6 col-sm-4 col-md-3 document" id="doc-' + name + '">' +
                    '<div class="thmb">' +
                        '<div class="ckbox ckbox-default hidel" style="display: none;">' +
                            '<input id="check-'+name+'" type="checkbox" name="names[]" value="'+name+'">' +
                            '<label for="check-'+name+'"></label>' +
                        '</div>' +
                        '<div class="btn-group fm-group hidel" style="display: none;">' +
                            '<button class="btn btn-default dropdown-toggle fm-toggle" data-toggle="dropdown" type="button" aria-expanded="false">' +
                                '<span class="caret"></span>' +
                            '</button>' +
                            '<ul class="dropdown-menu fm-menu pull-right" role="menu">' +
                                '<li>' +
                                    '<a href="javascript: downloadFolder(\''+name+'\');">' +
                                        '<i class="fa fa-download"></i>' +
                                        'Herunterladen' +
                                    '</a>' +
                                '</li>' +
                                '<li>' +
                                    '<a href="javascript: showDelFolder(\''+name+'\');">' +
                                        '<i class="fa fa-trash-o"></i>' +
                                        'L&ouml;schen' +
                                    '</a>' +
                                '</li>' +
                            '</ul>' +
                        '</div>' +
                        '<div class="thmb-prev">' +
                            '<img class="img-responsive" alt="" src="/ftp/'+name+"/"+flist["2"]+'">' +
                        '</div>' +
                        '<h4 class="fm-title">' +
                            '<a href="/camimgmanager-detail/'+name+'">'+ date + '</a>' +
                        '</h4>' +
                        '<small class="text-muted">'+len+' Bilder enthalten.</small>' +
                    '</div>' +
                '</div>';

    return html;
} //endfunction getFolderElementHTML

function setSort(type) {
    if (type == 'asc') {
        type = 'Aufsteigend';
    } else {
        type = 'Absteigend';
    } //endif

    $('#sortselect').html(type + ' <span class="fa fa-caret-down"></span>')
    setPage(window.page);
} //endfunction setSort

function isWithinTimespan(name) {
    var span = $('#reportrange span').text();
    span = span.replace(/\s+/g, '');
    span = span.split('-');
    var startdate = moment(span[0], "DD.MM.YYYY");
    var enddate = moment(span[1], "DD.MM.YYYY");
    var date = moment(name, "DD-MM-YYYY");
    if (date >= startdate && date <= enddate) {
        return true;
    } else {
        return false;
    } //endif
} //endfunction

$(document).ready(function() {
    function cb(start, end) {
        $('#reportrange span').html(start.format('DD.MM.YYYY') + ' - ' + end.format('DD.MM.YYYY'));
        setPage(window.page);
    }
    $('#reportrange span').html(moment().subtract(29, 'days').format('DD.MM.YYYY') + ' - ' + moment().format('DD.MM.YYYY'));

    $('#reportrange').daterangepicker(
        {
            "ranges": {
                "Heute": [moment(), moment()],
                "Gestern": [moment().subtract('days', 1), moment().subtract('days', 1)],
                "Die letzten 7 Tage": [moment().subtract('days', 6), moment()],
                "Die letzten 30 Tage": [moment().subtract('days', 29), moment()],
                "Diesen Monat": [moment().startOf('month'), moment().endOf('month')],
                "Letzten Monat": [moment().subtract('month', 1).startOf('month'), moment(moment().subtract('month', 1)).endOf('month')]
            },
            "startDate": moment().subtract('days', 29),
            "endDate": moment(),
            "miniDate": "DD.MM.YYY",
            "applyClass": 'pull-left btn-success',
            "cancelClass": 'pull-right btn-default',
            "locale": {
                "format": "DD.MM.YYYY",
                "separator": " - ",
                "daysOfWeek": [
                    "So",
                    "Mo",
                    "Di",
                    "Mi",
                    "Do",
                    "Fr",
                    "Sa"
                ],
                "monthNames": [
                    "Januar",
                    "Februar",
                    "M&auml;rz",
                    "April",
                    "Mai",
                    "Juni",
                    "Juli",
                    "August",
                    "September",
                    "Oktober",
                    "November",
                    "Dezember"
                ],
                "firstDay": 1
            }
        }, cb
    );

    createFolders();
});