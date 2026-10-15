<?php

use PayPal\Api\Payer;
use PayPal\Api\ItemList;
use PayPal\Api\Item;
use PayPal\Api\Payment;
use PayPal\Api\Details;
use PayPal\Api\Amount;
use PayPal\Api\Transaction;
class Ticket_payment extends APP_Controller {
    /**
     * @var AppPaymentBase []
     */
    public function __construct()
    {
        parent::__construct();

    }

    function choose_method($ticket_id="",$reply_id="",$payment_id=""){
        $this->SetTitle("Choose Payment Method");
        if(empty($ticket_id) || empty($reply_id) || empty($payment_id)){
            AddError("Invalid request");
            $this->DisplayMSGOnly("Request param is missing, Try again");
            return;
        }
        $payment_obj = Mticket_payment::FindBy("id", $payment_id, ["ticket_id" => $ticket_id, "reply_id" => $reply_id]);
        if($payment_obj->status=="A"){
            $this->DisplayMSGOnly("Payment has been done already", site_url("ticket/details/{$payment_obj->ticket_id}"), 10, true);
            return;
        }
        $activePaymentList=AppPaymentBase::getActivePaymentMethods($payment_obj->payment_currency);
        AddOnManager::DoFilter('active-payment-methods',$activePaymentList);
        $count_getway=count($activePaymentList);
        if($count_getway==0){
            $this->DisplayMSGOnly("No payment gateway has been activated", site_url("ticket/details/{$payment_obj->ticket_id}"), 10);
            return;
        }elseif($count_getway==1){
            foreach ($activePaymentList as $ID=>$method){
                $gid=strtolower($ID);
                redirect("ticket-payment/process/{$gid}/{$ticket_id}/{$reply_id}/{$payment_id}");
                break;
            }
           return;
        }

        $this->AddViewData("active_methods",$activePaymentList);
        $this->AddViewData("ticket_id",$ticket_id);
        $this->AddViewData("reply_id",$reply_id);
        $this->AddViewData("payment_id",$payment_id);
        $this->AddViewData("payment_obj",$payment_obj);

        $this->Display();
    }
    function process($method='',$ticket_id="",$reply_id="",$payment_id=""){
        if(empty($method) || empty($ticket_id) || empty($reply_id) || empty($payment_id)){
            AddError("Invalid request");
            $this->DisplayMSGOnly("Request param is missing, Try again");
            return;
        }
        $method=strtolower($method);
        $active_methods=AppPaymentBase::getActivePaymentMethods();
        if(isset($active_methods[$method])) {
            $payment_obj = Mticket_payment::FindBy("id", $payment_id, ["ticket_id" => $ticket_id, "reply_id" => $reply_id]);
            if ($payment_obj) {
                if($payment_obj->status=="A"){
                    $this->DisplayMSGOnly("Payment has been done already", site_url("ticket/details/{$payment_obj->ticket_id}"), 10, true);
                    return;
                }
                $payment_id_str = $ticket_id . "-" . $reply_id . "-" . $payment_id;
                ob_start();
                AddOnManager::DoAction("process-payment-" . $method, $payment_id_str, $payment_obj, $this);
                $this->AddViewData("action_data",ob_get_clean());
                $this->Display();
                return;
            } else {
                $this->DisplayMSGOnly("Invalid payment information");
                return;
            }
        }else{
            AddError("Unknown payment method");
            $this->DisplayMSGOnly("Unknown payment method, please use proper link");
            return;
        }

    }
    function ticket_payment_process($method='',$ticket_id="",$reply_id="",$payment_id=""){
        $this->SetTitle("Choose Payment Method");
        if(empty($ticket_id) || empty($reply_id) || empty($payment_id)){
            AddError("Invalid request");
            $this->DisplayMSGOnly("Request param is missing, Try again");
            return;
        }
        $this->SetTitle("Ticket Payment");
        if(empty($ticket_id) || empty($reply_id) || empty($payment_id)){
            AddError("Invalid request");
            $this->DisplayMSGOnly("Request param is missing, Try again");
            return;
        }
        $isDisplayed=false;
        $payment_obj=Mticket_payment::FindBy("id", $payment_id,["ticket_id"=>$ticket_id,"reply_id"=>$reply_id]);
        if($payment_obj) {
            $payment_id_str = $ticket_id . "-" . $reply_id . "-" . $payment_id;
            AddOnManager::DoFilter("process-payment-".$method,$payment_id_str,$payment_obj,$this);
        }else{
            $this->DisplayMSGOnly("Invalid payment information");
            return;
        }
        $this->Display();
    }
    function ticket_payment_paypal($ticket_id="",$reply_id="",$payment_id=""){

        $this->SetTitle("Ticket Payment");
        if(empty($ticket_id) || empty($reply_id) || empty($payment_id)){
            AddError("Invalid request");
            $this->DisplayMSGOnly("Request param is missing, Try again");
            return;
        }
        $payment_obj=Mticket_payment::FindBy("id", $payment_id,["ticket_id"=>$ticket_id,"reply_id"=>$reply_id]);
        if($payment_obj){
            $payment_id_str=$ticket_id."-".$reply_id."-".$payment_id;
            $this->load->library("APPPaypal");
            $paypal=new APPPaypal();
            $success_url=site_url("ticket/paypal-payment-process/S/{$ticket_id}/{$reply_id}/{$payment_id}");
            $cancel_url=site_url("ticket/paypal-payment-process/C/{$ticket_id}/{$reply_id}/{$payment_id}");
            $process_status=$paypal->process_single_payment($payment_id_str,$payment_obj->payment_des,$payment_obj->amount,$success_url,$cancel_url,0,$payment_obj->payment_currency);
            if(!$process_status){
                $this->DisplayMSGOnly("Payment Process failed, Try again");
                return;
            }else{
                $this->output->unset_template();
            }


        }else{
            $this->DisplayMSGOnly("Invalid payment information");
            return;
        }
        $this->DisplayMSGOnly("Invalid payment information");
        return;
    }
    function paypal_payment_process($type="",$ticket_id="",$reply_id="",$payment_id=""){
        $this->SetTitle("Ticket Payment Process");
        if ($type=="S" && !empty($payment_id)) {
            $this->load->library("APPPaypal");
            
            $payment_obj=Mticket_payment::FindBy("id", $payment_id,["ticket_id"=>$ticket_id,"reply_id"=>$reply_id]);
            if(!$payment_obj){
                $this->DisplayMSGOnly("Process Failed");
                return;
            }
            $paypal_obj=new APPPaypal();
            $apiContext=$paypal_obj->getApiContext();
            
            
            
            $paymentId = $_GET['paymentId'];
            $payment = Payment::get($paymentId, $apiContext);
            $currentStatus=$payment->getState();
            if($currentStatus=="approved"){
                $mplog=new Mpayment_log();
                $transaction_id=$payment->getId();
                $mplog->ticket_payment_id($payment_obj->id);
                $mplog->transaction_id($transaction_id);
                if(!$mplog->Select()){
                    $this->finish_order($payment,  $payment_obj);
                }else{
                    $this->DisplayMSGOnly("The payment is already processed");
                    return;
                }
                exit(1);
            }elseif($currentStatus!="created"){
                $this->DisplayMSGOnly("Payment Process Error, Please try again later");
                return;
            }
            
            
            
            
            
            $execution = new PayPal\Api\PaymentExecution();
            $execution->setPayerId($_GET['PayerID']);




            
            
            
            
            $details = new Details();
            $details->setShipping(0)
                ->setSubtotal($payment_obj->amount);

            
            
            
            
            $amount_obj = new Amount();
            $amount_obj->setCurrency(strtoupper($payment_obj->payment_currency))
                ->setTotal($payment_obj->amount)
                ->setDetails($details);

            



            
            
            
            
            
            $transaction = new Transaction();
            $transaction->setAmount($amount_obj);
            
            $execution->addTransaction($transaction);

            try {
                
                
                $paymentObj = $payment->execute($execution, $apiContext);
                $this->finish_order($paymentObj, $payment_obj);


            }catch (PayPal\Exception\PayPalConnectionException $ex) {

                $data=$ex->getData();
                $data=json_decode($data);
                if($data->name=="PAYMENT_ALREADY_DONE"){
                    $mplog=new Mpayment_log();
                    $transaction_id=$payment->getId();
                    $mplog->ticket_payment_id($payment_obj->id);
                    $mplog->transaction_id($transaction_id);
                    if(!$mplog->Select()){
                        $this->finish_order($payment,  $payment_obj);
                    }else{
                        $this->DisplayMSGOnly("The payment is already processed");
                        return;
                    }
                }else{
                    Mdebug_log::AddPaypalLog("Paypal Payment Error for id({$ticket_id}-{$reply_id}-{$payment_id})", Mdebug_log::STATUS_FAILED, Mdebug_log::ENTRY_TYPE_ERROR,$ex->getData());
                }
            } catch (Exception $ex) {
                Mdebug_log::AddPaypalLog("Paypal Payment Error for id({$ticket_id}-{$reply_id}-{$payment_id})", Mdebug_log::STATUS_FAILED, Mdebug_log::ENTRY_TYPE_ERROR,$ex->getData());
            }

        } elseif($type=="C") {
            
            $this->DisplayMSGOnly("You have canceled the payment process",site_url("ticket/details/{$ticket_id}"));
            return;
        }else{
            
            Mdebug_log::AddPaypalLog("Paypal Payment Error for id({$ticket_id}-{$reply_id}-{$payment_id})", Mdebug_log::STATUS_FAILED, Mdebug_log::ENTRY_TYPE_ERROR,"Unknown type({$type}) error");
        }

    }
}