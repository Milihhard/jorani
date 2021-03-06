<?php 
/**
 * This view displays the list of leave requests submitted to a manager.
 * @copyright  Copyright (c) 2014-2017 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */
?>

<div class="row-fluid">
    <div class="span12">

<h2><?php echo lang('requests_index_title');?><?php echo $help;?></h2>

<?php echo $flash_partial_view;?>

<p><?php echo lang('requests_index_description');?></p>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="leaves" width="100%">
    <thead>
        <tr>
            <th><?php echo lang('requests_index_thead_id');?></th>
            <th><?php echo lang('requests_index_thead_fullname');?></th>
            <th><?php echo lang('requests_index_thead_startdate');?></th>
            <th><?php echo lang('requests_index_thead_enddate');?></th>            
            <th><?php echo lang('requests_index_thead_duration');?></th>
            <th><?php echo lang('requests_index_thead_type');?></th>
            <th><?php echo lang('requests_index_thead_status');?></th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($requests as $requests_item):
    $date = new DateTime($requests_item['startdate']);
    $tmpStartDate = $date->getTimestamp();
    $startdate = $date->format(lang('global_date_format'));
    $date = new DateTime($requests_item['enddate']);
    $tmpEndDate = $date->getTimestamp();
    $enddate = $date->format(lang('global_date_format'));?>
    <tr>
        <td data-order="<?php echo $requests_item['leave_id']; ?>">
            <a href="<?php echo base_url();?>leaves/requests/<?php echo $requests_item['leave_id']; ?>" title="<?php echo lang('requests_index_thead_tip_view');?>"><?php echo $requests_item['leave_id']; ?></a>
            &nbsp;
            <div class="pull-right">
                <a href="<?php echo base_url();?>leaves/requests/<?php echo $requests_item['leave_id']; ?>" title="<?php echo lang('requests_index_thead_tip_view');?>"><i class="icon-eye-open"></i></a>
                &nbsp;
                <a href="#" class="lnkAccept" data-id="<?php echo $requests_item['leave_id']; ?>" title="<?php echo lang('requests_index_thead_tip_accept');?>"><i class="icon-ok"></i></a>
                &nbsp;
                <a href="#" class="lnkReject" data-id="<?php echo $requests_item['leave_id']; ?>" title="<?php echo lang('requests_index_thead_tip_reject');?>"><i class="icon-remove"></i></a>
                <?php if ($this->config->item('enable_history') === TRUE) { ?>
                &nbsp;
                <a href="#" class="show-history" data-id="<?php echo $requests_item['leave_id'];?>" title="<?php echo lang('requests_index_thead_tip_history');?>"><i class="icon-time"></i></a>
                <?php } ?>
            </div>
        </td>
        <td><?php echo $requests_item['firstname'] . ' ' . $requests_item['lastname']; ?></td>
        <td data-order="<?php echo $tmpStartDate; ?>"><?php echo $startdate . ' (' . lang($requests_item['startdatetype']). ')'; ?></td>
        <td data-order="<?php echo$tmpEndDate; ?>"><?php echo $enddate . ' (' . lang($requests_item['enddatetype']) . ')'; ?></td>
        <td><?php echo $requests_item['duration']; ?></td>
        <td><?php echo $requests_item['type_name']; ?></td>
        <?php
        switch ($requests_item['status']) {
            case 1: echo "<td><span class='label'>" . lang($requests_item['status_name']) . "</span></td>"; break;
            case 2: echo "<td><span class='label label-warning'>" . lang($requests_item['status_name']) . "</span></td>"; break;
            case 3: echo "<td><span class='label label-success'>" . lang($requests_item['status_name']) . "</span></td>"; break;
            default: echo "<td><span class='label label-important' style='background-color: #ff0000;'>" . lang($requests_item['status_name']) . "</span></td>"; break;
        }?>
    </tr>
<?php endforeach ?>
	</tbody>
</table>
    </div>
</div>

<div class="row-fluid"><div class="span12">&nbsp;</div></div>

<div class="row-fluid">
    <div class="span12">
        <a href="<?php echo base_url();?>requests/export/<?php echo $filter; ?>" class="btn btn-primary"><i class="fa fa-file-excel-o"></i>&nbsp; <?php echo lang('requests_index_button_export');?></a>
        &nbsp;&nbsp;
        <a href="<?php echo base_url();?>requests/all" class="btn btn-primary"><i class="icon-filter icon-white"></i>&nbsp; <?php echo lang('requests_index_button_show_all');?></a>
        &nbsp;&nbsp;
        <a href="<?php echo base_url();?>requests/requested" class="btn btn-primary"><i class="icon-filter icon-white"></i>&nbsp; <?php echo lang('requests_index_button_show_pending');?></a>
        &nbsp;&nbsp;
        <?php if ($this->config->item('ics_enabled') == TRUE) {?>
        <a id="lnkICS" href="#"><i class="icon-globe"></i> ICS</a>
        <?php }?>
    </div>
</div>

<div class="row-fluid"><div class="span12">&nbsp;</div></div>

<div id="frmShowHistory" class="modal hide fade">
    <div class="modal-body" id="frmShowHistoryBody">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="#" onclick="$('#frmShowHistory').modal('hide');" class="btn"><?php echo lang('OK');?></a>
    </div>
</div>

<div id="frmLinkICS" class="modal hide fade">
    <div class="modal-header">
        <h3>ICS<a href="#" onclick="$('#frmLinkICS').modal('hide');" class="close">&times;</a></h3>
    </div>
    <div class="modal-body" id="frmSelectDelegateBody">
        <div class='input-append'>
                <input type="text" class="input-xlarge" id="txtIcsUrl" onfocus="this.select();" onmouseup="return false;" 
                    value="<?php echo base_url() . 'ics/collaborators/' . $user_id;?>" />
                 <button id="cmdCopy" class="btn" data-clipboard-text="<?php echo base_url() . 'ics/collaborators/' . $user_id;?>">
                     <i class="fa fa-clipboard"></i>
                 </button>
                <a href="#" id="tipCopied" data-toggle="tooltip" title="<?php echo lang('copied');?>" data-placement="right" data-container="#cmdCopy"></a>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#" onclick="$('#frmLinkICS').modal('hide');" class="btn btn-primary"><?php echo lang('OK');?></a>
    </div>
</div>

<link href="<?php echo base_url();?>assets/datatable/DataTables-1.10.11/css/jquery.dataTables.min.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/DataTables-1.10.11/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url();?>assets/js/clipboard-1.6.1.min.js"></script>

<script type="text/javascript">
var clicked = false;
    
$(document).ready(function() {
    //Transform the HTML table in a fancy datatable
    $('#leaves').dataTable({
            order: [[ 2, "desc" ]],
            language: {
                decimal:            "<?php echo lang('datatable_sInfoThousands');?>",
                processing:       "<?php echo lang('datatable_sProcessing');?>",
                search:              "<?php echo lang('datatable_sSearch');?>",
                lengthMenu:     "<?php echo lang('datatable_sLengthMenu');?>",
                info:                   "<?php echo lang('datatable_sInfo');?>",
                infoEmpty:          "<?php echo lang('datatable_sInfoEmpty');?>",
                infoFiltered:       "<?php echo lang('datatable_sInfoFiltered');?>",
                infoPostFix:        "<?php echo lang('datatable_sInfoPostFix');?>",
                loadingRecords: "<?php echo lang('datatable_sLoadingRecords');?>",
                zeroRecords:    "<?php echo lang('datatable_sZeroRecords');?>",
                emptyTable:     "<?php echo lang('datatable_sEmptyTable');?>",
                paginate: {
                    first:          "<?php echo lang('datatable_sFirst');?>",
                    previous:   "<?php echo lang('datatable_sPrevious');?>",
                    next:           "<?php echo lang('datatable_sNext');?>",
                    last:           "<?php echo lang('datatable_sLast');?>"
                },
                aria: {
                    sortAscending:  "<?php echo lang('datatable_sSortAscending');?>",
                    sortDescending: "<?php echo lang('datatable_sSortDescending');?>"
                }
            }
        });

     //Prevent double click on accept and reject buttons
     $('#leaves').on('click', '.lnkAccept', function (event) {
        event.preventDefault();
        if (!clicked) {
            clicked = true;
            window.location.href = "<?php echo base_url();?>requests/accept/" + $(this).data("id");
        }
     });
     $("#leaves").on('click', '.lnkReject', function (event) {
        event.preventDefault();
        if (!clicked) {
            clicked = true;
            window.location.href = "<?php echo base_url();?>requests/reject/" + $(this).data("id");
        }
     });
     
    <?php if ($this->config->item('enable_history') === TRUE) { ?>
    //Prevent to load always the same content (refreshed each time)
    $('#frmShowHistory').on('hidden', function() {
        $("#frmShowHistoryBody").html('<img src="<?php echo base_url();?>assets/images/loading.gif">');
    });
    
    //Popup show history
    $("#leaves tbody").on('click', '.show-history',  function(){
        $("#frmShowHistory").modal('show');
        $("#frmShowHistoryBody").load('<?php echo base_url();?>leaves/' + $(this).data('id') +'/history');
    });
    <?php } ?>
     
    //Copy/Paste ICS Feed
    var client = new Clipboard("#cmdCopy");
    $('#lnkICS').click(function () {
        $("#frmLinkICS").modal('show');
    });
    client.on( "success", function() {
        $('#tipCopied').tooltip('show');
        setTimeout(function() {$('#tipCopied').tooltip('hide')}, 1000);
    });
});
</script>
