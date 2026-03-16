<?php



class GravaUpdateDie extends Model
{
    protected $filtros;

    public function __construct()
    {
        require_once __DIR__ . '/../models/process.php';
        $this->filtros = new process();
    }

    public function UpdateBatchDie($dados)
    {

        if (empty($dados)) {
            return false;
        }


        $ids_transation = $this->filtros->list_transation($dados);


        // var_dump($ids_transation);

        // die();


        // $this->db->beginTransaction();

        try {

            $i = 0;

            $sql = "UPDATE progestor.transacao 
             SET status = ? WHERE id_processo = ? 
             AND transacao_id = ? ";

            echo "<pre>";

            var_dump($this->db);
            die();

            $stmt = $this->db->prepare($sql);

            foreach ($ids_transation as $values) {

                $stmt->execute([6, $values['processo_id'], $values['transacao_id']]);

                $i++;

                // echo "<pre>";

                // print_r($stmt);

                // die();

                // A cada 200 registros, faz commit e abre nova transação
                if ($i % 200 == 0) {
                    $this->db->commit();
                    $this->db->beginTransaction();
                }
            }

            // Commit final (caso não seja múltiplo de 200)
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();

            throw $e;
        }
    }
}
