<?php

use core\Database;

class UserModel {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    // Encontra usuário pelo email
    public function findUserByEmail($email){
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);
        $row = $this->db->single();

        // Verifica se o usuário existe
        if($this->db->rowCount() > 0){
            return $row;
        } else {
            return false;
        }
    }

    // Registra um novo usuário
    public function register($data){
        $this->db->beginTransaction();
        try {
            $this->db->query('INSERT INTO users (nome, email, senha_hash, perfil) VALUES (:nome, :email, :senha_hash, :perfil)');
            $this->db->bind(':nome', $data['nome']);
            $this->db->bind(':email', $data['email']);
            $this->db->bind(':senha_hash', $data['senha_hash']);
            $this->db->bind(':perfil', $data['perfil']);
            $this->db->execute();
            $userId = $this->db->lastInsertId();

            // Se o perfil for vendedor, insere na tabela de vendedores
            if($data['perfil'] == 'vendedor'){
                $this->db->query('INSERT INTO sellers (user_id, comissao_padrao_perc) VALUES (:user_id, :comissao)');
                $this->db->bind(':user_id', $userId);
                $this->db->bind(':comissao', $data['comissao']);
                $this->db->execute();
            }
            $this->db->commit();
            return true;
        } catch (Exception $e){
            $this->db->rollBack();
            return false;
        }
    }
    
    // Login do usuário
    public function login($email, $password){
        $row = $this->findUserByEmail($email);

        if($row == false){
            error_log("DEBUG: Usuário com email {$email} não encontrado."); // Adicione esta linha
            return false;
        }

        $hashed_password = $row->senha_hash;
        error_log("DEBUG: Email: {$email}, Senha fornecida: {$password}, Hash do BD: {$hashed_password}"); // Adicione esta linha
        
        if(password_verify($password, $hashed_password)){
            error_log("DEBUG: password_verify retornou TRUE."); // Adicione esta linha
            return $row;
        } else {
            error_log("DEBUG: password_verify retornou FALSE."); // Adicione esta linha
            return false;
        }
    }

    // Busca todos os usuários
    public function getAllUsers(){
        $this->db->query("SELECT id, nome, email, perfil, ativo FROM users ORDER BY nome ASC");
        return $this->db->resultSet();
    }

    // Busca usuário por ID
    public function getUserById($id){
        $this->db->query("
            SELECT u.id, u.nome, u.email, u.perfil, u.ativo, s.comissao_padrao_perc 
            FROM users u 
            LEFT JOIN sellers s ON u.id = s.user_id
            WHERE u.id = :id
        ");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Atualiza usuário
    public function updateUser($data){
        $this->db->beginTransaction();
        try {
            $this->db->query('UPDATE users SET nome = :nome, email = :email, perfil = :perfil, ativo = :ativo WHERE id = :id');
            $this->db->bind(':id', $data['id']);
            $this->db->bind(':nome', $data['nome']);
            $this->db->bind(':email', $data['email']);
            $this->db->bind(':perfil', $data['perfil']);
            $this->db->bind(':ativo', $data['ativo']);
            $this->db->execute();

            // Atualiza a senha se ela foi fornecida
            if(!empty($data['senha'])){
                $this->db->query('UPDATE users SET senha_hash = :senha_hash WHERE id = :id');
                $this->db->bind(':id', $data['id']);
                $this->db->bind(':senha_hash', $data['senha_hash']);
                $this->db->execute();
            }

            // Lida com a tabela de vendedores
            if($data['perfil'] == 'vendedor'){
                // Verifica se já existe um registro de vendedor
                $this->db->query('SELECT id FROM sellers WHERE user_id = :user_id');
                $this->db->bind(':user_id', $data['id']);
                $sellerExists = $this->db->single();

                if($sellerExists){
                    $this->db->query('UPDATE sellers SET comissao_padrao_perc = :comissao WHERE user_id = :user_id');
                } else {
                    $this->db->query('INSERT INTO sellers (user_id, comissao_padrao_perc) VALUES (:user_id, :comissao)');
                }
                $this->db->bind(':user_id', $data['id']);
                $this->db->bind(':comissao', $data['comissao']);
                $this->db->execute();
            } else {
                // Se o perfil mudou de vendedor para outro, remove o registro de vendedor
                $this->db->query('DELETE FROM sellers WHERE user_id = :user_id');
                $this->db->bind(':user_id', $data['id']);
                $this->db->execute();
            }

            $this->db->commit();
            return true;
        } catch (Exception $e){
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }
}
