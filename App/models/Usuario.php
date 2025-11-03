<?php
class Usuario extends Model {
    public function listarUsuarios() {
        $query = $this->db->query("SELECT * FROM usuarios");
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}