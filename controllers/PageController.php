<?php
/**
 * CONTROLADOR: PÁGINA PÚBLICA
 * IED Miguel Samper Agudelo
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../models/Noticia.php';
require_once __DIR__ . '/../models/Galeria.php';

class PageController {
    private $pdo;
    private $noticiaModel;
    private $galeriaModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->noticiaModel = new Noticia($pdo);
        $this->galeriaModel = new Galeria($pdo);
    }

    /**
     * Página de inicio
     */
    public function index() {
        $noticias_destacadas = $this->noticiaModel->getFeatured(3);
        $galeria_reciente = $this->galeriaModel->getAll(1, 6);

        $data = [
            'noticias_destacadas' => $noticias_destacadas,
            'galeria_reciente' => $galeria_reciente
        ];

        return view('public/index', $data);
    }

    /**
     * Página: Nosotros
     */
    public function nosotros() {
        return view('public/nosotros');
    }

    /**
     * Página: Historia
     */
    public function historia() {
        return view('public/historia');
    }

    /**
     * Página: Misión
     */
    public function mision() {
        return view('public/mision');
    }

    /**
     * Página: Visión
     */
    public function vision() {
        return view('public/vision');
    }

    /**
     * Página: Programas
     */
    public function programas() {
        return view('public/programas');
    }

    /**
     * Página: Matrículas
     */
    public function matriculas() {
        return view('public/matriculas');
    }
}

?>
