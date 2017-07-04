<?php
/**
 * This view displays the leave requests for a given entity of the organization.
 * @copyright  Copyright (c) 2014-2017 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */
?>

<div class="row-fluid">
    <div class="span12">

<h2><?php echo lang('calendar_organization_title');?><?php echo $help;?></h2>

<div class="row-fluid">
    <div class="span5">
        <label for="txtEntity">
            <?php echo lang('calendar_organization_field_select_entity');?>&nbsp;
            (<input type="checkbox" checked id="chkIncludeChildren"> <?php echo lang('calendar_organization_check_include_subdept');?>)
        </label>
        <div class="input-append">
            <input type="text" id="txtEntity" value="<?php echo $departmentName;?>" readonly />
            <button id="cmdSelectEntity" class="btn btn-primary" title="<?php echo lang('calendar_organization_button_select_entity');?>"><i class="fa fa-sitemap" aria-hidden="true"></i></button>
        </div>
    </div>
    <div class="span5">
        <label for="txtMonthYear">
        <input type="checkbox" checked id="chkIncludeDaysOffs"> <?php echo lang('calendar_individual_day_offs');?>
        </label>
        <div class="input-prepend input-append">
            <button id="cmdPrevious" class="btn btn-primary" title="<?php echo lang('calendar_tabular_button_previous');?>"><i class="icon-chevron-left icon-white"></i></button>
            <input type="text" id="txtMonthYear" style="cursor:pointer;" value="<?php echo $monthName . ' ' . $year;?>" class="input-medium" readonly />
            <button id="cmdNext" class="btn btn-primary" title="<?php echo lang('calendar_tabular_button_next');?>"><i class="icon-chevron-right icon-white"></i></button>
        </div>
    </div>
    <?php if (($this->config->item('ics_enabled') == TRUE) && ($logged_in == TRUE)) {?>
    <div class="span2 pull-right"><a id="lnkICS" href="#"><i class="icon-globe"></i> ICS</a></div>
    <?php } else {?>
    <div class="span2">&nbsp;</div>
    <?php }?>
    
</div>

<div class="row-fluid">
    <div class="span3"><span class="label"><?php echo lang('Planned');?></span></div>
    <div class="span3"><span class="label label-success"><?php echo lang('Accepted');?></span></div>
    <div class="span3"><span class="label label-warning"><?php echo lang('Requested');?></span></div>
    <div class="span3">&nbsp;</div>
</div>

<div id="frmSelectEntity" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmSelectEntity').modal('hide');" class="close">&times;</a>
         <h3><?php echo lang('calendar_organization_popup_entity_title');?></h3>
    </div>
    <div class="modal-body" id="frmSelectEntityBody">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="#" onclick="select_entity();" class="btn"><?php echo lang('calendar_organization_popup_entity_button_ok');?></a>
        <a href="#" onclick="$('#frmSelectEntity').modal('hide');" class="btn"><?php echo lang('calendar_organization_popup_entity_button_cancel');?></a>
    </div>
</div>

<div id='calendar'></div>

    </div>
</div>

<div class="modal hide" id="frmModalAjaxWait" data-backdrop="static" data-keyboard="false">
        <div class="modal-header">
            <h1><?php echo lang('global_msg_wait');?></h1>
        </div>
        <div class="modal-body">
            <img src="<?php echo base_url();?>assets/images/loading.gif"  align="middle">
        </div>
 </div>

<div id="frmLinkICS" class="modal hide fade">
    <div class="modal-header">
        <h3>ICS<a href="#" onclick="$('#frmLinkICS').modal('hide');" class="close">&times;</a></h3>
    </div>
    <div class="modal-body" id="frmSelectDelegateBody">
        <div class='input-append'>
                <input type="text" class="input-xlarge" id="txtIcsUrl" onfocus="this.select();" onmouseup="return false;" 
                    value="" />
                 <button id="cmdCopy" class="btn" data-clipboard-target="#txtIcsUrl">
                     <i class="fa fa-clipboard"></i>
                 </button>
                <a href="#" id="tipCopied" data-toggle="tooltip" title="copied" data-placement="right" data-container="#cmdCopy"></a>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#" onclick="$('#frmLinkICS').modal('hide');" class="btn btn-primary"><?php echo lang('OK');?></a>
    </div>
</div>

<link rel="stylesheet" href="<?php echo base_url();?>assets/bootstrap-datepicker-1.6.4/css/bootstrap-datepicker.min.css">
<script src="<?php echo base_url();?>assets/bootstrap-datepicker-1.6.4/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/moment-with-locales.min.js"></script>
<link href="<?php echo base_url();?>assets/fullcalendar-2.8.0/fullcalendar.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/fullcalendar-2.8.0/lib/moment.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/fullcalendar-2.8.0/fullcalendar.min.js"></script>
<?php if ($language_code != 'en') {?>
<script type="text/javascript" src="<?php echo base_url();?>assets/fullcalendar-2.8.0/lang/<?php echo $language_code;?>.js"></script>
<script src="<?php echo base_url();?>assets/bootstrap-datepicker-1.6.4/locales/bootstrap-datepicker.<?php echo $language_code;?>.min.js"></script>
<?php }?>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery.pers-brow.js"></script>
<script src="<?php echo base_url();?>assets/js/clipboard-1.6.1.min.js"></script>
<script src="<?php echo base_url();?>assets/js/bootbox.min.js"></script>
<script type="text/javascript">
    var entity = 0; //Id of the selected entity
    var entityName = '<?php echo $departmentName;?>';
    var includeChildren = true;
    var month = (<?php echo $month;?> - 1); //Momentjs uses a zero-based number
    var year = <?php echo $year;?>;
    var text; //Label of the selected entity
    var toggleDayoffs = true;
    var currentDate = moment().year(year).month(month).date(1);
    
    //Refresh the calendar if data is available
    function refresh_calendar() {
        $('#calendar').fullCalendar('removeEventSources');
        <?php if ($logged_in == TRUE) {?>
        var source = '<?php echo base_url();?>leaves/organization/' + entity;
        <?php } else {?>
        var source = '<?php echo base_url();?>leaves/public/organization/' + entity;
        <?php }?>
        if ($('#chkIncludeChildren').prop('checked') == true) {
            source += '?children=true';
        } else {
            source += '?children=false';
        }
        $('#calendar').fullCalendar('addEventSource', source);
        <?php if ($logged_in == TRUE) {?>
        source = '<?php echo base_url();?>contracts/calendar/alldayoffs?entity=' + entity;
        <?php } else {?>
        source = '<?php echo base_url();?>contracts/public/calendar/alldayoffs?entity=' + entity;
        <?php }?>
        if ($('#chkIncludeChildren').prop('checked') == true) {
            source += '&children=true';
        } else {
            source += '&children=false';
        }
        if (toggleDayoffs) {
            $('#calendar').fullCalendar('addEventSource', source);
        } else {
            $('#calendar').fullCalendar('removeEventSource', source);
        }
    }
    
    function select_entity() {
        entity = $('#organization').jstree('get_selected')[0];
        entityName = $('#organization').jstree().get_text(entity);
        $('#txtEntity').val(entityName);
        refresh_calendar();
        $.cookie('cal_entity', entity);
        $.cookie('cal_entityName', entityName);
        $.cookie('cal_includeChildren', includeChildren);
        $("#frmSelectEntity").modal('hide');
    }

    $(document).ready(function() {
        
        //Global Ajax error handling mainly used for session expiration
        $( document ).ajaxError(function(event, jqXHR, settings, errorThrown) {
            $('#frmModalAjaxWait').modal('hide');
            if (jqXHR.status == 401) {
                bootbox.alert("<?php echo lang('global_ajax_timeout');?>", function() {
                    //After the login page, we'll be redirected to the current page 
                   location.reload();
                });
            } else { //Oups
                bootbox.alert("<?php echo lang('global_ajax_error');?>");
            }
          });

        //Popup select entity
        $("#cmdSelectEntity").click(function() {
            $("#frmSelectEntity").modal('show');
            $("#frmSelectEntityBody").load('<?php echo base_url(); ?>organization/select');
        });

        //On click the check box "include sub-department", refresh the content if a department was selected
        $('#chkIncludeChildren').click(function() {
            $.cookie('cal_includeChildren', $('#chkIncludeChildren').prop('checked'));
            refresh_calendar();
        });

        //Load alert forms
        $("#frmSelectEntity").alert();
        //Prevent to load always the same content (refreshed each time)
        $('#frmSelectEntity').on('hidden', function() {
            $(this).removeData('modal');
        });

        //Create a calendar and fill it with AJAX events
        $('#calendar').fullCalendar({
            timeFormat: ' ', /*Trick to remove the start time of the event*/
             header: {
                    left: "",
                    center: "title",
                    right: ""
            },
            loading: function(isLoading) {
                if (isLoading) { //Display/Hide a pop-up showing an animated icon during the Ajax query.
                    $('#frmModalAjaxWait').modal('show');
                } else {
                    $('#frmModalAjaxWait').modal('hide');
                }    
            },
            eventRender: function(event, element, view) {
                if(event.imageurl){
                    $(element).find('span:first').prepend('<img src="' + event.imageurl + '" />');
                }
            },
            eventAfterRender: function(event, element, view) {
                //Add tooltip to the element
                $(element).attr('title', event.title);

                if (event.enddatetype == "Morning" || event.startdatetype == "Afternoon") {
                    var nb_days = event.end.diff(event.start, "days");
                    var duration = 0.5;
                    var halfday_length = 0;
                    var length = 0;
                    var width = parseInt(jQuery(element).css('width'));
                    if (nb_days > 0) {
                        if (event.enddatetype == "Afternoon") {
                            duration = nb_days + 0.5;
                        } else {
                            duration = nb_days;
                        }
                        nb_days++;
                        halfday_length = Math.round((width / nb_days) / 2);
                        if (event.startdatetype == "Afternoon" && event.enddatetype == "Morning") {
                            length = width - (halfday_length * 2);
                        } else {
                            length = width - halfday_length;
                        }
                    } else {
                        halfday_length = Math.round(width / 2);   //Average width of a day divided by 2
                        length = halfday_length;
                    }
                }
                $(element).css('width', length + "px");

                //Starting afternoon : shift the position of event to the right
                if (event.startdatetype == "Afternoon") {
                    $(element).css('margin-left', halfday_length + "px");
                }
            },
            windowResize: function(view) {
                $('#calendar').fullCalendar( 'rerenderEvents' );
            }
        });
        
        //Toggle day offs displays
        $('#chkIncludeDaysOffs').on('click', function() {
            toggleDayoffs = !toggleDayoffs;
            $.cookie('cal_dayoffs', toggleDayoffs);
            refresh_calendar();
        });
        
        $('#cmdNext').click(function() {
            currentDate = currentDate.add(1, 'M');
            month = currentDate.month();
            year = currentDate.year();
            var fullDate = currentDate.format("MMMM") + ' ' + year;
            $("#txtMonthYear").val(fullDate);
            $('#calendar').fullCalendar('next');
        });
        
        $('#cmdPrevious').click(function() {
            currentDate = currentDate.add(-1, 'M');
            month = currentDate.month();
            year = currentDate.year();
            var fullDate = currentDate.format("MMMM") + ' ' + year;
            $("#txtMonthYear").val(fullDate);
            $('#calendar').fullCalendar('prev');
        });
        
        //Intialize Month/Year selection
        $("#txtMonthYear").datepicker({
            format: "MM yyyy",
            startView: 1,
            minViewMode: 1,
            todayBtn: 'linked',
            todayHighlight: true,
            language: "<?php echo $language_code;?>",
            autoclose: true
        }).on("changeDate", function(e) {
            month = new Date(e.date).getMonth();
            //Doesn't work : year = new Date(e.date).getYear();
            year = parseInt(String(e.date).split(" ")[3]);
            currentDate = moment().year(year).month(month).date(1);
            var fullDate = currentDate.format("MMMM") + ' ' + year;
            $("#txtMonthYear").val(fullDate);
            $('#calendar').fullCalendar('gotoDate', currentDate);
        });
        
        //Cookie has value ? take -1 by default
        if($.cookie('cal_entity') != null) {
            entity = $.cookie('cal_entity');
            entityName = $.cookie('cal_entityName');
            includeChildren = $.cookie('cal_includeChildren');
            toggleDayoffs = $.cookie('cal_dayoffs');
            //Parse boolean values
            includeChildren = $.parseJSON(includeChildren.toLowerCase());
            toggleDayoffs = $.parseJSON(toggleDayoffs.toLowerCase());
            $('#txtEntity').val(entityName);
            $('#chkIncludeChildren').prop('checked', includeChildren);
            //Load the calendar events
            refresh_calendar();
        } else { //Set default value
            $.cookie('cal_entity', entity);
            $.cookie('cal_entityName', entityName);
            $.cookie('cal_includeChildren', includeChildren);
            $.cookie('cal_dayoffs', toggleDayoffs);
            refresh_calendar();
        }
        
        <?php if ($logged_in == TRUE) { ?>
        //Copy/Paste ICS Feed
        $('#lnkICS').click(function () {
            var urlICS = '<?php echo base_url(); ?>ics/entity/<?php echo $user_id; ?>/0/' + $('#chkIncludeChildren').prop('checked');
            if (entity != -1) {
                urlICS = '<?php echo base_url(); ?>ics/entity/<?php echo $user_id; ?>/' + entity + '/' + $('#chkIncludeChildren').prop('checked');
            }
            $("#frmLinkICS").modal('show');
            $('#txtIcsUrl').val(urlICS);
            //$('#txtIcsUrl').data('data-clipboard-text', urlICS);
        });
        var client = new Clipboard("#cmdCopy");
        client.on( "success", function() {
            $('#tipCopied').tooltip('show');
            setTimeout(function() {$('#tipCopied').tooltip('hide')}, 1000);
        });
        <?php } ?>;
    });
</script>

