<?php

class Ricardovera_PushNotification_Model_Observer extends Varien_Event_Observer{
    public function sales_order_status_change_handler(Varien_Event_Observer $observer)
        {
            $tokens = ['eIzqV_bEEwc:APA91bGGHxUi1KdzMhEWa3-hzKzphMhA7KMVbYjylIBR-oTd09Nx2XEyOZFbIN7e0li-NB5jA-hkhDPbMjaHlCP9012g2_tEZD4WaapbOVAY8-8E6G7FqnrYOirqHeqV7LCqlx1on5JQ'];
    
            $header = [
                'Authorization: Key=AIzaSyBIpekMXINpkqdU_iUWBrnwXmLNYW7UPg0',
                'Content-Type: Application/json'
            ];

            $msg = [
                'title' => 'TiendaMia',
                'body' => 'Estado de orden modificada por magento',
                'icon' => 'img/icon.png',
                'image' => 'img/image.png',
                'click_action' => 'https://www.google.com.pe',
            ];

            $payload = [
                'registration_ids'  => $tokens,
                'data'              => $msg
            ];

            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => json_encode( $payload ),
              CURLOPT_HTTPHEADER => $header
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
              echo "cURL Error #:" . $err;
            } else {
              echo $response;
            }
            $status = $observer->getEvent()->getOrder()->getStatus();
            $originalData = $observer->getEvent()->getOrder()->getOrigData();
            $previousStatus = $originalData['status'];

            if (($status !== $previousStatus) && ($status == Mage_Sales_Model_Order::STATE_COMPLETE)) {
                exit();
            }
            Mage::log(
            "status updated",
            null,
            'status.log'
         );
        }   

}

?>