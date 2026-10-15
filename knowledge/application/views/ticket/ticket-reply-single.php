<?php
/**
* @var $reply_object;
* @var $class;
* @var $has_payment;
* @var $payment_obj;
* @var $ticketObj;
* @var $isAdmin;
* @var $files;
* @var $ticket_user_id;
 */
$isAdmin=GetCurrentUserType()=="AD";
$files=Mticket_reply::get_reply_attachments_by($reply_object,false,$ticket_user_id);
$ticketObj=new Mticket();
$user=Mticket_reply::get_user_by_id($reply_object->ticket_id, $reply_object->replied_by);
if(!empty($user)){
    $has_payment=$reply_object->payment_id>0;
    if($has_payment){
        $payment_obj=Mticket_payment::FindBy("id", $reply_object->payment_id,["ticket_id"=>$reply_object->ticket_id,"reply_id"=>$reply_object->reply_id]);
    }
?>
<div id="id_<?php echo $reply_object->ticket_id."_".$reply_object->reply_id;?>" class="<?php echo $class; ?> panel panel-default app-panel-box m-b-10 ticket-reply <?php echo ($user->type=="A" || $user->type=="S")?"admin-user":"";?>">

    <div class="panel-body text-justify reply-body">
        <?php if($user->type=="A" || $user->type=="S"){?>
            <div class="user-type"><?php echo $user->type_title;?></div>
        <?php }?>
        <div class="row">
            <div  class=" col-xs-3 col-sm-2 col-md-2 user-profile ">

                <?php echo get_user_img($user->title,$user->id,$user->type,$user->photo_url);?>
                <div class="tooltip2 r-user-title" title="<?php echo $user->title;?>" ><?php echo $user->title;?></div>
                <div class="r-user-title">
                    <?php echo get_user_date_default_format($reply_object->reply_time);?><br/>
                    <?php echo get_user_time_default_format($reply_object->reply_time);?>
                </div>
            </div>
            <div class="col-xs-9 col-sm-10 col-md-10">
                <div class="reply-text">
                    <?php echo $reply_object->reply_text;
                    if($has_payment){
                        if($payment_obj){

                            ?>
                            <div class="panel panel-default payment-panel <?php echo $payment_obj->status=="A"?" paid-panel":"";?>">
                                <div class="panel-heading p-5"><?php _e("Payment Added"); ?>
                                    <?php if(!$isAdmin && in_array($payment_obj->status,['P','F'])){?>
                                        <a href="<?php echo site_url("ticket-payment/choose-method/{$payment_obj->ticket_id}/{$payment_obj->reply_id}/{$payment_obj->id}");?>" class="payment-btn btn btn-xs btn-success pull-right"><?php _e("Pay Now") ; ?></a>
                                    <?php }?>
                                </div>
                                <div class="panel-body p-5">
                                    <ul class="app-ul-properties payment-ul">
                                        <li class="">
                                            <label class="f-w-2 w-clone" for=""><?php _e("Description") ; ?></label>
                                            <span  class="f-w-10" ><?php echo $payment_obj->payment_des;?></span>


                                        </li>
                                        <li>
                                            <label class="f-w-2 w-clone" for=""><?php _e("Amount") ; ?></label>
                                            <span class="f-w-4"><?php echo $payment_obj->payment_currency." ".$payment_obj->amount;?></span>

                                            <label class="f-w-2 w-clone" for=""><?php _e("Status") ; ?></label>
                                            <span class="f-w-4"><?php echo $payment_obj->getTextByKey("status");?></span>

                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>

                </div>
                <div class="ticket-footer-info">
                    <div class="row">
                        <div class="col-xs-6 col-sm-4">
                            <div class="pro-row">
                                <div class="pro-title"><?php _e("Ticket Status") ; ?>  </div>
                                <div class="pro-value">
                                    <?php echo $ticketObj->getTextByKey("status",true,$reply_object->ticket_status);?>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6">
                            <?php if(count($files)){?>
                                <div class="pro-title"><?php _e("File Attached") ; ?>  </div>
                                <div class="pro-value">
                                    <ul class="app-file-list inline-file-list">
                                        <?php
                                        $utype=GetCurrentUserType();
                                        foreach ($files as $file){
                                            ?>
                                            <li>
                                                <a class="<?php echo strtolower(substr($file->type, 0,3))=="ima"?"popupimg":"";?>" href="<?php echo Mticket::getFileUrl($file,$ticket_user_id,$reply_object);?>" >
                                                    <i class="fa <?php echo $file->class;?>"></i>
                                                    <?php
                                                    echo $file->name." <em>( {$file->size_str} )</em>";
                                                    ?></a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            <?php }?>
                        </div>
                        <?php if($isAdmin){?>
                            <div class="col-xs-12 col-sm-2 text-right text-italic p-l-0">
                                <?php if($reply_object->is_user_seen!="Y"){?>
                                    <a href="<?php echo admin_url("ticket/edit-reply/{$reply_object->ticket_id}/{$reply_object->reply_id}");?>" class="popupformWR" data-onclose="ReloadSiteUrl" data-effect="mfp-move-from-top" ><?php _e("Edit") ; ?></a> &nbsp;
                                <?php } ?>
                                <i title="<?php echo $reply_object->is_user_seen=="Y"?__("Seen by ticket owner"):__("Unseened by ticket owner");?>" data-tooltip-position="top" class="tooltip2 fa <?php echo $reply_object->is_user_seen=="Y"?'fa-eye u-seen':'fa-eye-slash u-unseen';?>"></i> <?php echo $reply_object->is_user_seen=="Y"?app_time_elapsed_string($reply_object->seen_time):""?>
                            </div>
                        <?php }?>
                    </div>
	                <?php if($isAdmin) {
		                echo GetTicketReplyLanguage($reply_object,"");
	                } ?>

                </div>
            </div>
        </div>
    </div>
</div>
<?php
}