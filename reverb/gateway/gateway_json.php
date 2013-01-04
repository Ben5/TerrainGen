<?php
require_once(__DIR__."/gateway_base.php");

class GatewayHtml extends GatewayBase
{

    public function
    ConstructOutput()
    {      
        $outputVars = $this->componentInstance->GetExposedVariables();

        echo json_encode($outputVars);
    }

}


$gateway = new GatewayHtml;
$gateway->Prepare();
$gateway->ConstructOutput();
