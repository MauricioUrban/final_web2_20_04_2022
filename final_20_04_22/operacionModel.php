<?php

class OperacionModel{
    private $db;

    public function __construct(){
        $this->db = new PDO ('mysql:host=localhost;'.'dbname=db_vvba;charset=utf8','root', '');
    }

    function crearOperacion($id_cuenta,$monto_operacion,$tipo_operacion, $fecha_operacion) {
        $query = $this->db->prepare('INSERT INTO operacion(id_cuenta, monto_operacion, tipo_operacion, fecha_operacion) VALUES (?, ?, ?, ?)');
        $query->execute([$id_cuenta,$monto_operacion,$tipo_operacion, $fecha_operacion]);

        return $this->db->lastInsertId();
    }

    function getOperacion($id) {
        $query = $this->db->prepare('SELECT * FROM operacion WHERE id = ?');
        $query->execute([$id]);
        $operacion = $query->fetch(PDO::FETCH_OBJ);
        return $operacion;
    }

    function getOperacionDePremiun(){ 
        $query = $this->db->prepare ('SELECT * FROM operacion INNER JOIN cuenta WHERE cuenta.premiun = true');
        $query ->execute();
        $operaciones = $query-> fetchAll( PDO :: FETCH_OBJ );     
        return  $operaciones;
    }

    function getSumaXOperacion($operacion, $fecha, $id_cuenta){
        $query = $this->db->prepare ('SELECT * FROM operacion AS suma SUM(monto_operacion) WHERE tipo_operacion = ? AND fecha_operacion <= ? AND id_cuenta = ?');
        $query ->execute($operacion, $fecha, $id_cuenta);
        $operaciones = $query-> fetchAll( PDO :: FETCH_OBJ );     
        return  $operaciones['suma'];
    }

}