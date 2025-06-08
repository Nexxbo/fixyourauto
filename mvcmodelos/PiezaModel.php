<?php
class PiezaModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getPiezas($filtros) {
        $query = "SELECT * FROM piezas WHERE 1";
        $params = [];

        // Filtro por búsqueda general
        if (!empty($filtros['query'])) {
            $query .= " AND (nombre LIKE :query OR modelo_coche LIKE :query)";
            $params[':query'] = '%' . $filtros['query'] . '%';
        }

        // Filtro por marca
        if (!empty($filtros['marca'])) {
            $query .= " AND marca = :marca";
            $params[':marca'] = $filtros['marca'];
        }

        // Filtro por modelo
        if (!empty($filtros['modelo_coche'])) {
            $query .= " AND modelo_coche LIKE :modelo";
            $params[':modelo'] = '%' . $filtros['modelo_coche'] . '%';
        }

        // Filtro por precio
        if (!empty($filtros['precio_min'])) {
            $query .= " AND precio >= :precio_min";
            $params[':precio_min'] = $filtros['precio_min'];
        }
        if (!empty($filtros['precio_max'])) {
            $query .= " AND precio <= :precio_max";
            $params[':precio_max'] = $filtros['precio_max'];
        }

        // Filtro por stock
        if (!empty($filtros['stock'])) {
            $query .= " AND stock > 0";
        }
        if (!empty($filtros['nombre-pieza'])) {
            $query .= " AND nombre LIKE :nombre_pieza";
            $params[':nombre_pieza'] = '%' . $filtros['nombre-pieza'] . '%';
        }

        switch($filtros['sort']) {
            case 'precio_desc':
                $query .= " ORDER BY precio DESC";
                break;
            case 'recientes':
                $query .= " ORDER BY fecha_agregado DESC";
                break;
            default: // precio_asc
                $query .= " ORDER BY precio ASC";
                break;}

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMarcas() {
        $query = "SELECT DISTINCT marca FROM piezas ORDER BY marca ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        $marcas = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        return $marcas ?: [];
    }

    public function getPiezaById($id) {
        $query = "SELECT * FROM piezas WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>