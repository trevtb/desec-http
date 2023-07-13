function poll() {
    setTimeout(function() {
        $.ajax({url: "/poll-online", success: function(data) {
        }, dataType: "json", complete: pollCamGallery});
    }, 30000);
} //endfunction poll

function pollCamGallery() {
    if (window.refmode == 'poll') {
        setPollMode();
    } //endif
    poll();
} //endfunction pollcamGallery

function enableSpinner(id) {
    var opts = {
        lines: 13,
        length: 7,
        width: 4,
        radius: 10,
        corners: 1
    };
    var spinner = new Spinner(opts).spin();
    $('#galsm-item-'+id).append(spinner.el);

    return spinner;
} //endfunction enableSpinner

function disableSpinner(spinner) {
    spinner.spin(false);
} //endfunction disableSpinner

function disableSelectors() {
    $('.selceitems').prop('disabled', true);
    $('.selgeitems').prop('disabled', true);
    $('#camselbox').css('opacity', '0.4');
    $('#groupselbox').css('opacity', '0.4');
} //endfunction disableSelectors

function enableSelectors() {
    $('.selceitems').prop('disabled', false);
    $('.selgeitems').prop('disabled', false);
    $('#camselbox').css('opacity', '1.0');
    $('#groupselbox').css('opacity', '1.0');
} //endfunction enableSelectors

function setMode(type, obj, enablesels) {
    var spinner = enableSpinner($(obj).attr("data-id"));
    var id = $(obj).attr("data-id");
    if (type == 'live') {
        $.ajax({
            type: "POST",
            url: "/testcamcon",
            async: true,
            data: 'id='+id,
            success: function(response) {
                var src = "";
                var srcfw = "";
                var iscreated = false;
                var cam = getCam(id);
                if (cam.created == '1') {
                    iscreated = true;
                } //endif
                var resolution = cam.resolution;
                resolution = resolution.split('x');

                if (!iscreated) {
                    src = "/dist/img/creating_320.jpg";
                    srcfw = "/dist/img/creating_320.jpg";
                } else if (iscreated && response == 'login') {
                    src = "/dist/img/nologin_320.jpg";
                    srcfw = "/dist/img/nologin_"+resolution[0]+".jpg";
                } else if (iscreated && response == 'offline') {
                    src = "/dist/img/nocon_320.jpg";
                    srcfw = "/dist/img/nocon_"+resolution[0]+".jpg";
                } else {
                    src = "/cam/" + id;
                    srcfw = src;
                } //endif

                $(obj).attr("src", src).load(function() {disableSpinner(spinner);});
                if (enablesels == '1') {
                    $('#fwimg-'+id).attr("src", srcfw).load(enableSelectors());
                } else {
                    $('#fwimg-'+id).attr("src", srcfw).load();
                } //endif
            }
        });
    } else if (type == 'poll') {
        $.ajax({
            type: "POST",
            url: "/testcamcon",
            async: true,
            data: 'id='+id,
            success: function(response) {
                var src = "";
                var srcfw = "";
                var iscreated = false;
                var cam = getCam(id);
                if (cam.created == '1') {
                    iscreated = true;
                } //endif
                var resolution = cam.resolution;
                resolution = resolution.split('x');

                if (!iscreated) {
                    src = "/dist/img/creating_320.jpg";
                    srcfw = "/dist/img/creating_320.jpg";
                } else if (iscreated && response == 'login') {
                    src = "/dist/img/nologin_320.jpg";
                    srcfw = "/dist/img/nologin_"+resolution[0]+".jpg";
                } else if (iscreated && response == 'offline') {
                    src = "/dist/img/nocon_320.jpg";
                    srcfw = "/dist/img/nocon_"+resolution[0]+".jpg";
                } else {
                    src = "/camframe-thumb/" + id + "/" + new Date().getTime();
                    srcfw = src;
                } //endif
                $(obj).attr("src", src).load(function() {disableSpinner(spinner);});
                $('#fwimg-'+id).attr("src", srcfw).load();
            }
        });
    } //endif
} //endfunction setMode

function setLiveMode() {
    var galimgs = $('.galprevimg');
    var imgcount = $(galimgs).length;
    if (imgcount == 0) {
        enableSelectors();
    } //endif
    var count = 0;
    $(galimgs).each(function () {
        if (count == imgcount-1) {
            setMode('live', $(this), '1');
        } else {
            setMode('live', $(this), '0');
        } //endif
        count++;
    });
} //endfunction setLiveMode

function setPollMode() {
    var galimgs = $('.galprevimg');
    var imgcount = $(galimgs).length;
    if (imgcount == 0) {
        enableSelectors();
    } //endif
    var count = 0;
    $(galimgs).each(function () {
        if (count == imgcount-1) {
            setMode('poll', $(this), '1');
        } else {
            setMode('poll', $(this), '0');
        } //endif
        count++;
    });
} //endfunction setPollMode

function getCam(id) {
    var response = $.ajax({
        type: "POST",
        url: "/cam-by-id",
        data: "id="+id,
        cache: false,
        async: false
    }).responseText;

    return $.parseJSON(response);
} //endfunction

function savePollMode() {
    var url = '';
    if (window.refmode == 'poll') {
        url = '/cam-polling-mode';
    } else {
        url = '/cam-live-mode';
    } //endif

    $.ajax({
        type: "GET",
        url: url,
        cache: false,
        async: true
    });
} //endfunction savePollMode

function liveMonAdd(type, id) {
    disableSelectors();
    var url = "";
    if (type == 'camera') {
        url = "/livemon-addcam";
    } else if (type == 'group') {
        url = "/livemon-addgroup";
    } else {
        return;
    } //endif

    $.ajax({
        type: "POST",
        url: url,
        async: true,
        data: 'id='+id,
        success: function(rsp) {
            window.location = '/cam/monitor/ex/'+type;
        }
    });
} //endfunction liveMonAdd

function liveMonDel(type, id) {
    disableSelectors();
    var url = "";
    if (type == 'camera') {
        url = "/livemon-delcam";
    } else if (type == 'group') {
        url = "/livemon-delgroup";
    } else {
        return;
    } //endif

    $.ajax({
        type: "POST",
        url: url,
        async: true,
        data: 'id='+id,
        success: function(rsp) {
            window.location = '/cam/monitor/ex/'+type;
        }
    });
} //endfunction liveMonDel

$(document).ready(function() {
    $.fn.spin = function (opts) {
        this.each(function () {
            var $this = $(this), data = $this.data();

            if (data.spinner) {
                data.spinner.stop();
                delete data.spinner;
            } //endif
            if (opts !== false) {
                window.s = data.spinner = new Spinner($.extend({color: $this.css('color')}, opts)).spin(this)
            } //endif
        })
        return this
    };

    disableSelectors();

    if ($('.galprevimg').length > 0) {
        $('#gallery-container').sGallery({
            fullScreenEnabled: true
        });
    } //endif

    if (window.refmode == 'poll') {
        setPollMode();
    } else if (window.refmode == 'live') {
        setLiveMode();
    } //endif
    poll();

    $('#camrefmodeRadio1').click(function() {
        window.refmode = 'live';
        savePollMode();
        setLiveMode();
    });

    $('#camrefmodeRadio2').click(function() {
        window.refmode = 'poll';
        savePollMode();
        setPollMode();
    });

    var monrow = $('#monitorrow');
    $(monrow).on('change', 'input.selceitems', function() {
        if ($(this).is(':checked')) {
            liveMonAdd('camera', $(this).val());
        } else {
            liveMonDel('camera', $(this).val());
        } //endif
    });

    $(monrow).on('change', 'input.selgeitems', function() {
        if ($(this).is(':checked')) {
            liveMonAdd('group', $(this).val());
        } else {
            liveMonDel('group', $(this).val());
        } //endif
    });
});