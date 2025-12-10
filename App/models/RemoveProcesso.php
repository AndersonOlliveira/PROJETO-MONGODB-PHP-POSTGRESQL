<?php

class RemoveProcesso extends Model
{

	public  function execute($idProcesso)
	{



		$sql = "DELETE FROM progestor.processo WHERE processo_id = ?";

		$dados = array();
		$dados[] = $idProcesso;

		$result = $this->db->prepare($sql);

		$result->execute($dados);

		return true;
	}
}
