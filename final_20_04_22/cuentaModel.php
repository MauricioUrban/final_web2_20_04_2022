<?php

class CuentaModel{
    private $db;

    public function __construct(){
        $this->db = new PDO ('mysql:host=localhost;'.'dbname=db_vvba;charset=utf8','root', '');
    }

    function getCuenta($id) {
        $query = $this->db->prepare('SELECT * FROM operacion WHERE id = ?');
        $query->execute([$id]);
        $cuenta = $query->fetch(PDO::FETCH_OBJ);
        return $cuenta;
    }

    function getCuentaXDni($dni) {
        $query = $this->db->prepare('SELECT * FROM operacion WHERE dni_cliente = ?');
        $query->execute([$dni]);
        $cuentas = $query->fetchAll(PDO::FETCH_OBJ);
        return $cuentas;
    }



}