<?php

// use SoapClient;
// use SoapFault;
class soaps extends Controller
{

    private $_endpoint = "https://proscore.com.br/meuscore_pep/service.php?wsdl";

    private $_token = "3ff5a12968cedfb56cb927a0e49b431b";

    private $_client;

    public function __construct()
    {

        try {


            // ini_set('default_socket_timeout', 30);

            $options = array(
                "trace" => 1,
                // "exceptions" => true,
                "encoding" => 'UTF-8',
                "connection_timeout" => 30, // Forma correta de definir timeout de conexão
                "stream_context" => stream_context_create(array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                ))
            );

            $this->_client = new SoapClient($this->_endpoint, $options);
        } catch (SoapFault $e) {
            echo "<pre>Erro na chamada SOAP:\n";
            print_r($e->getMessage());
            throw $e;
        }
    }


    public function verificaConstaPEP($cpf)
    {


        try {

            $resposta = $this->_client->verificaConstaPEP(
                $this->_token,
                $cpf
            );

            return $resposta;
        } catch (SoapFault $e) {

            return json_encode(array("error" => $e->getMessage()));
        }
    }

    public function verificaCargoPEP($cpf)
    {

        try {

            $resposta = $this->_client->verificaCargoPEP(
                $this->_token,
                $cpf
            );

            return $resposta;
        } catch (\SoapFault $e) {

            return json_encode(array("error" => $e->getMessage()));
        }
    }

    public function teste()
    {
        var_dump('ser]a que sai aqui?');
    }
}
