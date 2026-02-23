<?php



class GravaUpdateParalizar extends Model
{

    public function insertBatch(array $dados)
    {


        if (empty($dados)) {
            return false;
        }


        $this->db->beginTransaction();

        try {

            $i = 0;

            $sql = "UPDATE progestor.transacao 
             SET status = ? WHERE id_processo = ? 
             AND transacao_id = ? ";

            $stmt = $this->db->prepare($sql);

            foreach ($dados['ids'] as $r) {
                // Executa um por um dentro da transação
                $stmt->execute([17, $r['id_processo'], $r['transacao_id']]);
            }
            if (($i % 200) == 0 && $i > 0) { // update transaction a cada 200 registros
                $this->db->commit();
                $this->db->beginTransaction();
            }

            $i++;
        } catch (Exception $e) {
            $this->db->rollBack();

            throw $e;
        }
    }
}
