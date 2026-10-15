<?php

namespace Razorpay\Api;

class Addon extends Entity
{
    
    

    public function fetch($id)
    {
        return parent::fetch($id);
    }

    public function delete()
    {
        $entityUrl = $this->getEntityUrl();

        return $this->request('DELETE', $entityUrl . $this->id);
    }
}
