<?php

class Ricardovera_PushNotification_Model_Observer{
    /**
     * Magento pasa como primer parámetro de los eventos un Varien_Event_Observer
     */
    public function control(Varien_Event_Observer $observer){
         // Recupera el producto que está siendo actualizado desde el evento observador.
         $product = $observer->getEvent()->getProduct();

         // Escribe una nueva linea en var/log/product-updates.log
         $name = $product->getName();
         $sku = $product->getSku();
         Mage::log(
            "{$name} ({$sku}) updated",
            null,
            'product-updates.log'
         );
    }
    public function implementOrderStatus(Varien_Event_Observer $observer)
        {
            $tokens = ['TOKENIDNAVEGADOR'];
    
            $header = [
                'Authorization: Key=KEY_FIREBASE',
                'Content-Type: Application/json'
            ];

            $msg = [
                'title' => 'TiendaMia',
                'body' => 'Estado de orden modificada',
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