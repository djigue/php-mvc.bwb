<?php

namespace Core;

use PDO;

abstract class DAO implements CRUDInterface
{
    protected PDO $pdo;

    public function __construct()
    {
        // Chargement de la configuration de la base de données depuis le fichier JSON
        $config = json_decode(file_get_contents(__DIR__ . '/../config/database.json'), true);
        
        // Instanciation de l'objet PDO avec les informations du fichier JSON
        $dsn = "{$config['driver']}:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";
        $this->pdo = new PDO($dsn, $config['username'], $config['password']);
        
        // Configuration des options de PDO
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function retrieve(int $id): ?object
    {
        // Requête SQL pour récupérer l'entité par son ID
        $stmt = $this->pdo->prepare("SELECT * FROM " . $this->getTableName() . " WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $data ? (object) $data : null;
    }

    public function create(array $data): ?object
    {
        // Préparation de la requête d'insertion
        $columns = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));
        $stmt = $this->pdo->prepare("INSERT INTO " . $this->getTableName() . " ($columns) VALUES ($placeholders)");
        
        // Exécution de la requête
        $result = $stmt->execute($data);
        
        if ($result) {
            $id = $this->pdo->lastInsertId();
            return $this->retrieve((int)$id); // Retourne l'objet créé
        }

        return null; // En cas d'échec
    }

    public function update(int $id, array $data): bool
    {
        // Préparation de la requête de mise à jour
        $setClause = "";
        foreach ($data as $key => $value) {
            $setClause .= "$key = :$key, ";
        }
        $setClause = rtrim($setClause, ", ");
        
        $stmt = $this->pdo->prepare("UPDATE " . $this->getTableName() . " SET $setClause WHERE id = :id");
        
        // Exécution de la requête
        return $stmt->execute(array_merge($data, ['id' => $id]));
    }

    public function delete(int $id): bool
    {
        // Préparation de la requête de suppression
        $stmt = $this->pdo->prepare("DELETE FROM " . $this->getTableName() . " WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getAllBy(array $conditions): array
    {
        // Création de la clause WHERE dynamiquement
        $whereClause = "WHERE 1=1";
        $params = [];
        foreach ($conditions as $field => $value) {
            $whereClause .= " AND $field = :$field";
            $params[$field] = $value;
        }

        // Requête SQL pour récupérer les données
        $stmt = $this->pdo->prepare("SELECT * FROM " . $this->getTableName() . " $whereClause");
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retourne les résultats sous forme de tableau
    }

    // Méthode abstraite à définir dans les classes héritées pour obtenir le nom de la table
    abstract protected function getTableName(): string;
}
