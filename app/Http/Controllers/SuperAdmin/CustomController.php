<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Mail\SendMail;
use App\Models\Appointment;
use App\Models\Category;
use App\Models\Doctor;
use App\Models\DoctorSubscription;
use App\Models\Expertise;
use App\Models\Hospital;
use App\Models\Lab;
use App\Models\LabWorkHours;
use App\Models\NotificationTemplate;
use App\Models\Report;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\User;
use App\Models\WorkingHour;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client;
use App\Models\Notification;
use Illuminate\Support\Facades\Config;
use OneSignal;

class CustomController extends Controller
{
    public function imageUpload($image)
    {
        $file = $image;
        $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
        $path = public_path() . '/images/upload';
        $file->move($path, $fileName);
        return $fileName;
    }

    public function deleteFile($file_name)
    {
        if ($file_name != 'prod_default.png' && $file_name != 'defaultUser.png') {
            if (File::exists(public_path('images/upload/' . $file_name))) {
                File::delete(public_path('images/upload/' . $file_name));
            }
            return true;
        }
    }

    public function deletePrescription($file_name)
    {
        if (File::exists(public_path('prescription/upload/' . $file_name))) {
            File::delete(public_path('prescription/upload/' . $file_name));
        }
        return true;
    }

    public function display_category($id)
    {
        $categories = Category::where('treatment_id', $id)->get();
        return response(['success' => true, 'data' => $categories]);
    }

    public function display_expertise($id)
    {
        $expertises = Expertise::where('category_id', $id)->get();
        return response(['success' => true, 'data' => $expertises]);
    }

    public function updateENV($data)
    {
        $envFile = app()->environmentFilePath();
        if ($envFile) {
            $str = file_get_contents($envFile);
            if (count($data) > 0) {
                foreach ($data as $envKey => $envValue) {
                    $str .= "\n"; // In case the searched variable is in the last line without \n
                    $keyPosition = strpos($str, "{$envKey}=");
                    $endOfLinePosition = strpos($str, "\n", $keyPosition);
                    $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);
                    // If key does not exist, add it
                    if (!$keyPosition || !$endOfLinePosition || !$oldLine) {
                        $str .= "{$envKey}={$envValue}\n";
                    } else {
                        $str = str_replace($oldLine, "{$envKey}={$envValue}", $str);
                    }
                }
            }
            $str = substr($str, 0, -1);
            try {
                if (!file_put_contents($envFile, $str)) {
                }
                return true;
            } catch (\Throwable $th) {
                return false;
            }
        }
    }

    public function statusChangeNotification($user, $appointment, $status)
    {
        $notification_template = NotificationTemplate::where('title', 'status change')->first();
        $setting = Setting::first();
        $placeholders = [
            '{{user_name}}' => $user->name,
            '{{appointment_id}}' => $appointment->appointment_id,
            '{{status}}' => $status,
            '{{date}}' => Carbon::now(env('timezone'))->format('Y-m-d'),
            '{{app_name}}' => $setting->business_name,
        ];

        $msg1 = $notification_template->msg_content;
        $mail1 = $notification_template->mail_content;

        $placeholder_keys = array_keys($placeholders);
        $placeholder_values = array_values($placeholders);
        $mail1 = str_ireplace($placeholder_keys, $placeholder_values, $mail1);
        $msg1 = str_ireplace($placeholder_keys, $placeholder_values, $msg1);

        if ($setting->patient_notification == 1) {
            try {
                Config::set('onesignal.app_id', $setting->patient_app_id);
                Config::set('onesignal.rest_api_key', $setting->patient_api_key);
                Config::set('onesignal.user_auth_key', $setting->patient_auth_key);
                OneSignal::sendNotificationToUser(
                    $msg1,
                    $user->device_token,
                    $url = null,
                    $data = null,
                    $buttons = null,
                    $schedule = null,
                    $setting->business_name
                );
            } catch (\Throwable $th) {
                //throw $th;
            }
        }
        if ($setting->patient_mail == 1) {
            try {
                $config = array(
                    'driver'     => $setting->mail_mailer,
                    'host'       => $setting->mail_host,
                    'port'       => $setting->mail_port,
                    'from'       => array('address' => $setting->mail_from_address, 'name' => $setting->mail_from_name),
                    'encryption' => $setting->mail_encryption,
                    'username'   => $setting->mail_username,
                    'password'   => $setting->mail_password
                );
                Config::set('mail', $config);
                Mail::to($user->email)->send(new SendMail($mail1, $notification_template->subject));
            } catch (\Exception $e) {
                info($e);
            }
        }

        $user_notification = array();
        $user_notification['user_id'] = auth()->user()->id;
        $user_notification['doctor_id'] = $appointment->doctor_id;
        $user_notification['user_type'] = 'user';
        $user_notification['title'] = 'status change';
        $user_notification['message'] = $msg1;
        Notification::create($user_notification);
        return true;
    }

    public function sendOtp($user)
    {
        $setting = Setting::first();
        $isVerificationRequire = $setting->verification;
        if ($isVerificationRequire == 1) {
            $otp = mt_rand(1000, 9999);
            $user->update(['otp' => $otp]);

            $isMailNotificationON = $setting->using_mail;
            $isMsgNotificationON = $setting->using_msg;
            $template = NotificationTemplate::where('title', 'verification')->first();
            $mail1 = $template->mail_content;
            $msg1 = $template->msg_content;
            $subject = $template->subject;

            $placeholders = [
                '{{user_name}}' => $user->name,
                '{{otp}}' => $otp,
                '{{app_name}}' => $setting->business_name,
            ];

            $placeholder_keys = array_keys($placeholders);
            $placeholder_values = array_values($placeholders);
            $mail1 = str_ireplace($placeholder_keys, $placeholder_values, $mail1);
            $msg1 = str_ireplace($placeholder_keys, $placeholder_values, $msg1);

            if ($isMailNotificationON == 1) {
                try {
                    $config = array(
                        'driver'     => $setting->mail_mailer,
                        'host'       => $setting->mail_host,
                        'port'       => $setting->mail_port,
                        'from'       => array('address' => $setting->mail_from_address, 'name' => $setting->mail_from_name),
                        'encryption' => $setting->mail_encryption,
                        'username'   => $setting->mail_username,
                        'password'   => $setting->mail_password
                    );
                    Config::set('mail', $config);
                    Mail::to($user->email)->send(new SendMail($mail1, $subject));
                } catch (\Exception $e) {
                    info($e);
                }
            }
            if ($isMsgNotificationON == 1) {
                $sid = $setting->twilio_acc_id;
                $token = $setting->twilio_auth_token;
                try {
                    $phone = $user->phone_code . $user->phone;
                    $client = new Client($sid, $token);
                    $client->messages->create(
                        $phone,
                        array(
                            'from' => $setting->twilio_phone_no,
                            'body' => $msg1
                        )
                    );
                } catch (\Throwable $th) {
                }
            }
            return $user;
        }
    }

    public function sendTestNotification($email, $role)
    {
        $testUser = User::where('email', $email)->first();

        if (!$testUser) {
            return 'Test user not found.';
        }

        if (!$testUser->device_token || $testUser->device_token == "" || $testUser->device_token == "N_A" || $testUser->device_token == "N/A") {
            return 'Test user does not have a device token.';
        }

        $testMessage = "This is a test notification.";
        $testEmailSubject = "Test Notification";
        $testEmailContent = "Hello {{user_name}}, this is a test email notification.";

        // Replace placeholder in email content
        $placeholders = [
            '{{user_name}}' => $testUser->name,
        ];

        foreach ($placeholders as $placeholder => $value) {
            $testEmailContent = str_ireplace($placeholder, $value, $testEmailContent);
        }

        $setting = Setting::first();

        $appID = $role == 'doctor' ? $setting->doctor_app_id : $setting->patient_app_id;
        $restAPIKey = $role == 'doctor' ? $setting->doctor_api_key : $setting->patient_api_key;
        $userAuthKey = $role == 'doctor' ? $setting->doctor_auth_key : $setting->patient_auth_key;

        try {
            Config::set('onesignal.app_id', $appID);
            Config::set('onesignal.rest_api_key', $restAPIKey);
            Config::set('onesignal.user_auth_key', $userAuthKey);
            info('App ID: ' . $appID);
            info('Rest API Key: ' . $restAPIKey);
            info('User Auth Key: ' . $userAuthKey);

            OneSignal::sendNotificationToUser(
                $testMessage,
                $testUser->device_token,
                $url = null,
                $data = null,
                $buttons = null,
                $schedule = null,
                $setting->business_name
            );
        } catch (\Throwable $th) {
            info('Failed to send push notification.' . $th->getMessage());
            return 'Failed to send push notification.'  . $th->getMessage();
        }

        info('Push notification sent successfully!');
        return 'Test notification sent successfully!';
    }

    public function timeSlot($doctor_id, $date)
    {
        $doctor = Doctor::find($doctor_id);
        $workingHours = WorkingHour::where('doctor_id', $doctor->id)->get();
        $master = [];
        $timeslot = $doctor->timeslot == 'other' ? $doctor->custom_timeslot : $doctor->timeslot;
        $dayname = Carbon::parse($date)->format('l');
        foreach ($workingHours as $hours) {
            if ($hours->day_index == $dayname) {
                if ($hours->status == 1) {
                    foreach (json_decode($hours->period_list) as $value) {
                        $start_time = new Carbon($date . ' ' . $value->start_time);
                        if ($date == Carbon::now(env('timezone'))->format('Y-m-d')) {
                            $t = Carbon::now(env('timezone'));
                            $minutes = date('i', strtotime($t));
                            if ($minutes <= 30) {
                                $add = 30 - $minutes;
                            } else {
                                $add = 60 - $minutes;
                            }
                            $add += 60;
                            $d = $t->addMinutes($add)->format('h:i a');
                            $start_time = new Carbon($date . ' ' . $d);
                        }
                        $end_time = new Carbon($date . ' ' . $value->end_time);
                        $diff_in_minutes = $start_time->diffInMinutes($end_time);
                        for ($i = 0; $i <= $diff_in_minutes; $i += intval($timeslot)) {
                            if ($start_time >= $end_time) {
                                break;
                            } else {
                                $temp['start_time'] = $start_time->format('h:i a');
                                $temp['end_time'] = $start_time->addMinutes($timeslot)->format('h:i a');
                                $time = strval($temp['start_time']);
                                $appointment = Appointment::where([['doctor_id', $doctor->id], ['time', $time], ['date', $date]])->first();
                                if ($appointment) {
                                } else {
                                    array_push($master, $temp);
                                }
                            }
                        }
                    }
                }
            }
        }
        return $master;
    }

    public function LabtimeSlot($lab_id, $date)
    {
        $lab = Lab::find($lab_id);
        $workingHours = LabWorkHours::where('lab_id', $lab->id)->get();
        $master = [];
        $timeslot = 15;
        $dayname = Carbon::parse($date)->format('l');
        foreach ($workingHours as $hours) {
            if ($hours->day_index == $dayname) {
                if ($hours->status == 1) {
                    foreach (json_decode($hours->period_list) as $value) {
                        $start_time = new Carbon($date . ' ' . $value->start_time);
                        if ($date == Carbon::now(env('timezone'))->format('Y-m-d')) {
                            $t = Carbon::now(env('timezone'));
                            // dd($t);
                            $minutes = date('i', strtotime($t));
                            if ($minutes <= 30) {
                                $add = 30 - $minutes;
                            } else {
                                $add = 60 - $minutes;
                            }
                            $add += 60;
                            $d = $t->addMinutes($add)->format('h:i a');
                            $start_time = new Carbon($date . ' ' . $d);
                        }
                        $end_time = new Carbon($date . ' ' . $value->end_time);
                        $diff_in_minutes = $start_time->diffInMinutes($end_time);
                        for ($i = 0; $i <= $diff_in_minutes; $i += intval($timeslot)) {
                            if ($start_time >= $end_time) {
                                break;
                            } else {
                                $temp['start_time'] = $start_time->format('h:i a');
                                $temp['end_time'] = $start_time->addMinutes($timeslot)->format('h:i a');
                                $time = strval($temp['start_time']);
                                $appointment = Report::where([['lab_id', $lab->id], ['time', $time], ['date', $date]])->first();
                                if ($appointment) {
                                } else {
                                    array_push($master, $temp);
                                }
                            }
                        }
                    }
                }
            }
        }
        return $master;
    }

    public function cancel_max_order()
    {
        $cancel_time = Setting::first()->auto_cancel;
        $dt = Carbon::now(env('timezone'));
        $formatted = $dt->subMinute($cancel_time);
        $cancel_orders = Appointment::where([['created_at', '<=', $formatted], ['appointment_status', 'pending']])->get();
        foreach ($cancel_orders as $cancel_order) {
            $cancel_order->appointment_status = 'cancel';
            $cancel_order->save();
        }
        return true;
    }

    public function getHospital($doctor_id)
    {
        $doctor = Doctor::find($doctor_id);
        if (isset($doctor->hospital_id)) {
            $hospital_ids = explode(',', $doctor->hospital_id);
            $hospital = [];
            foreach ($hospital_ids as $hospital_id) {
                array_push($hospital, Hospital::find($hospital_id));
            }
            return $hospital;
        }
        return [];
    }

    public function doctorRegister($data)
    {
        $setting = Setting::first();
        $verification = $setting->verification;
        $verify = $verification == 1 ? 0 : 1;
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'verify' => $verify,
            'phone' => $data['phone'],
            'phone_code' => $data['phone_code'],
            'status' => 1,
            'image' => 'defaultUser.png',
            'dob' => $data['dob'],
            'gender' => $data['gender']
        ]);
        $user->assignRole('doctor');
        $data['user_id'] = $user->id;
        $data['image'] = 'defaultUser.png';
        $data['based_on'] = $setting->default_base_on;
        if ($data['based_on'] == 'commission') {
            $data['commission_amount'] = $setting->default_commission;
        }
        $data['since'] = Carbon::now(env('timezone'))->format('Y-m-d , h:i A');
        $data['status'] = 1;
        $data['name'] = $user->name;
        $data['dob'] = $data['dob'];
        $data['start_time'] = '08:00 am';
        $data['end_time'] = '08:00 pm';
        $data['timeslot'] = 15;
        $data['gender'] = $data['gender'];
        $data['subscription_status'] = 1;
        $data['is_filled'] = 0;
        $doctor = Doctor::create($data);
        if ($doctor->based_on == 'subscription') {
            $subscription = Subscription::where('name', 'free')->first();
            if ($subscription) {
                $doctor_subscription['doctor_id'] = $doctor->id;
                $doctor_subscription['subscription_id'] = $subscription->id;
                $doctor_subscription['duration'] = 1;
                $doctor_subscription['start_date'] = Carbon::now(env('timezone'))->format('Y-m-d');
                $doctor_subscription['end_date'] = Carbon::now(env('timezone'))->addMonths(1)->format('Y-m-d');
                $doctor_subscription['status'] = 1;
                $doctor_subscription['payment_status'] = 1;
                DoctorSubscription::create($doctor_subscription);
            }
        }
        $data['status'] = 1;
        $start_time = strtolower('08:00 am');
        $end_time = strtolower('08:00 pm');
        $days = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
        for ($i = 0; $i < count($days); $i++) {
            $master = array();
            $temp2['start_time'] = $start_time;
            $temp2['end_time'] = $end_time;
            array_push($master, $temp2);
            $work_time['doctor_id'] = $doctor->id;
            $work_time['period_list'] = json_encode($master);
            $work_time['day_index'] = $days[$i];
            $work_time['status'] = 1;
            WorkingHour::create($work_time);
        }
        return $user;
    }
}
