
<?php

$rotas["proDecision"] = array("classe" => 'controllers\DecisonController', "metodo" => "index_descision");
$rotas["proDecisionAcao"] = array("classe" => 'controllers\DecisonController', "metodo" => "index_acao");

$rotas["proDecisionComite"] = array("classe" => 'controllers\DecisonController', "metodo" => "index_comite");
$rotas["proDecisionStatus"] = array("classe" => 'controllers\DecisonController', "metodo" => "index_status");
// $rotas["users_aplications"] = array("classe" => 'controllers\DecisonController', "metodo" => "ListAplicationController");

$rotas["users_aplications"] = array("classe" => 'controllers\ListAplicationController', "metodo" =>  "layout_aplication");
$rotas["users_aplications_data"] = array("classe" =>'controllers\ListAplicationController', "metodo" =>  "layout_aplication_user");

?>