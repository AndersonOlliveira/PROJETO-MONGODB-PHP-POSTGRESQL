<?php



class CapturaCamposDoPlugin extends Model {

    public function execute($codPlugin) {
	   ini_set('memory_limit', '1024M');
		
		$sql = 
		"SELECT 
			regstrord as ordem,
			regstrdsc as nome_campo 
		FROM 
			reg inner join 
			regstr on regid = regstrreg 
		WHERE 
			regcod = ? and
			regstrord >= 2
		ORDER BY
			regstrord;";
		
		$dados = array();
		$dados[] = $codPlugin;
		
		$result = $this->db->prepare( $sql );
		$result->execute($dados);
		
		$registros=array();
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			
			$registros[] = $row;
		}
		
		return $registros;
	}
}