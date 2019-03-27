<?php

namespace App\Http\Controllers;

use App\Models\PushNotification;
use App\Models\Scheduler;
use Illuminate\Http\Response;

class PushNotificationController extends Controller
{

    public function checkPush()
    {
        $schedulers = PushNotification::getScheduler();
        $size_schedulers = count($schedulers['schedulers']);
        $result = [];
        $result["schedulers"] = [];
        $result["send_data"] = [];
        for ($i = 0; $i < $size_schedulers; $i++) {
            $devices = PushNotification::getDevicesByScheduler($schedulers['schedulers'][$i]);
            $result["schedulers"][] = $schedulers['schedulers'][$i]->getData();
            $size_devices = count($devices['devices']);
            $registration_ids = [];
            for ($j = 0; $j < $size_devices; $j++) {
                $registration_ids[] = $devices['devices'][$j]->getRegistratorId();
            }
            if (count($registration_ids) > 0) {
                $result["send_data"][] = $this->sendPush($registration_ids, $schedulers['schedulers'][$i]);
            }
        }
        return new Response($result, 200);
    }

    private function sendPush($registration_ids, Scheduler $schuduler)
    {
        $api_key = '<api_key>';
        $data = array(
            'registration_ids' => $registration_ids,
            'priority' => 'high',
            'notification' => array(
                'title' => 'Nova Pergunta',
                'body' => 'Time: ' . $schuduler->getTeam()->getSubgroup()->getName(),
                'icon' => 'myicon',
                'sound' => 'default',
                'badge' => 1,
            ),
            'restricted_package_name' => '',
        );
        $data_string = json_encode($data);
        $ch = curl_init('https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: key=' . $api_key,
        ));
        $result = curl_exec($ch);
        if (!curl_errno($ch)) {
            PushNotification::setSchedulerSent($schuduler);
        }
        curl_close($ch);
        return $result;
    }

}
