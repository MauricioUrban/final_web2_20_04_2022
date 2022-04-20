<?php
include_once('cuentaModel');
include_once('operacionModel');

include_once('view');
include_once('authHelper');

class PublicacionController
{
    private $cuentaModel;
    private $operacionModel;

    private $authHelper;

    private $view;

    public function __construct()
    {
        $this->cuentaModel = new CuentaModel();
        $this->operacionModel = new OperacionModel;

        $this->authHelper = new AuthHelper();
        $this->view = new View();
    }

    /*
    a.- Crear una nueva operación para una cuenta determinada.
        Verifique que todos los datos son correctos.
 */
    function crearOperacion($id_cuenta) // pasan como parametro el id de la cuenta a crear
    {
        $this->authHelper->checkLoggedIn(); // HACER

        if (empty($id_cuenta)) {
            $this->view->showMsj("El id $id_cuenta enviado no corresponde");
            die();
        }

        $cuenta = $this->cuentaModel->getCuenta($id_cuenta);

        if (empty($cuenta)) {
            $this->view->showMsj("La cuenta con el $id_cuenta no existe");
            die();
        }

        $monto_operacion = $_REQUEST['monto_operacion'];
        $tipo_operacion = $_REQUEST['tipo_operacion'];
        $fecha_operacion = $_REQUEST['fecha_operacion'];

        if (((!isset($monto_operacion)) || (empty($monto_operacion))) ||
            ((!isset($tipo_operacion)) || (empty($tipo_operacion))) ||
            ((!isset($fecha_operacion)) || (empty($fecha_operacion)))
        ) {

            $this->view->showMsj("Error al ingresar los datos");
            die();
        } else {

            $id_operacion = $this->operacionModel->crearOperacion($id_cuenta, $monto_operacion, $tipo_operacion, $fecha_operacion);

            if (empty($id_operacion)) {
                $this->view->showMsj("La operacion no se pudo crear.-");
                die();
            } else {
                $operacion = $this->operacionModel->getOperacion($id_operacion);

                if (empty($operacion)) {
                    $this->view->showMsj("Error, la operacion no se pudo insertar");
                } else {
                    $this->view->showMsj("Operacion insertada con exito");
                }
            }
        }
    }
    /*
  b.- Obtener una lista de operaciones realizadas
      por cuentas que son premium.

*/
    function operacionesXCuentasPremiun()
    {

        $operacionXCuentaPremiun = $this->operacionModel->getOperacionDePremiun();

        if (empty($OperacionXCuentaPremiun)) {
            $this->view->showMsj("No existen operaciones realizadas por cuentas premiun");
            die();
        } else {
            $this->view->showMsj("Listado de Operaciones realizadas por cuentas Premiun", $operacionXCuentaPremiun);
        }
    }

    /*
    c.- Calcular el saldo disponible a una fecha
    (el resultado de todas las operaciones balanceado).     
*/
    function calcularSaldo($id_cuenta, $fecha){

        if ((empty($id_cuenta)) || (empty($fecha))) {
            $this->view->showMsj("El id $id_cuenta o la $fecha enviada no es valida");
            die();
        }
    
        $deposito = $this->operacionModel->getSumaXOperacion(1, $fecha, $id_cuenta);
        $intereses = $this->operacionModel->getSumaXOperacion(2, $fecha, $id_cuenta);
        $pagos = $this->operacionModel->getSumaXOperacion(3, $fecha, $id_cuenta);
        $retiros = $this->operacionModel->getSumaXOperacion(4, $fecha, $id_cuenta);

        $saldo = $deposito + $intereses - $pagos - $retiros;

        $this->view->showMsj("El saldo al dia $fecha es de $saldo");
    }
    /*
    d.- Dar de alta una cuenta.
    Si la cuenta es premium se le debe generar un deposito automático de $1000 como regalo.
    No deben poder existir dos cuentas con el mismo dni
    */

    function altaDeCuenta(){

        $dni = $_REQUEST['dni'];

        if (((!isset($dni)) || (empty($dni))){
            $this->view->showMsj("El DNI ingresado no corresponde");
            die();
        }

        $dni_cuenta = $this->cuentaModel->getCuentaXDni($dni);

        if (!empty($cuentas)) {
            $this->view->showMsj("Ya existe una cuenta con ese DNI");
            die();
        }

        $premiun = $_REQUEST['premiun'];
        $fecha_alta = $_REQUEST['fecha_alta'];

        if (((!isset($fecha_alta)) || (empty($fecha_alta))) ||
            ((!isset($premiun)) || (empty($premiun)))) {
                $this->view->showMsj("Datos incorrectos");
                die();
        }

        $id_cuenta = $this->cuentaModel->agregarCuenta($dni_cuenta, $premiun, $fecha_alta);

        if (empty($id_cuenta)) {
            $this->view->showMsj("No se pudo crear la cuenta");
            die();
        } else {
            if ($premiun){
                $this->operacionModel->crearOperacion($id_cuenta, 1000, 1, $fecha_alta);
            }
        }
    }
}
